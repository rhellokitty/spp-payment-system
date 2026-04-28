<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoriesInterface;
use App\Models\Bill;
use App\Models\Transaction;
use App\Services\MidtransSnapService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionRepositories implements TransactionRepositoriesInterface
{
    public function __construct(
        private MidtransSnapService $midtransSnapService
    ) {}

    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = Transaction::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->latest()->with('bill.student.user', 'bill.paymentType');

        if ($limit) {
            $query->limit($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(?string $search, ?int $rowPerPage)
    {
        $query = $this->getAll($search, $rowPerPage, false);
        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = Transaction::where('id', $id)->with('bill.student.user', 'bill.paymentType');
        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $bill = Bill::find($data['bill_id']);

            if (!$bill) {
                throw new Exception('Data Bill Tidak Ditemukan');
            }

            if ($bill->status === 'paid') {
                throw new Exception('Bill sudah dibayar');
            }

            $transaction = new Transaction();
            $transaction->bill_id = $bill->id;
            $transaction->transaction_code = $this->generateTransactionCode();
            $transaction->gateway_reference = $data['gateway_reference'] ?? null;
            $transaction->snap_token = $data['snap_token'] ?? null;
            $transaction->snap_redirect_url = $data['snap_redirect_url'] ?? null;
            $transaction->amount_paid = $data['amount_paid'] ?? $bill->amount_snapshot ?? $bill->amount;
            $transaction->payment_method = $data['payment_method'];
            $transaction->status = 'pending';
            $transaction->paid_at = null;
            $transaction->expired_at = $this->resolveExpiredAt($data);

            $transaction->save();
            $this->syncBillStatus($bill);

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function initiatePayment(array $data)
    {
        DB::beginTransaction();

        try {
            $bill = Bill::where('id', $data['bill_id'])
                ->with('student.user', 'paymentType')
                ->first();

            if (!$bill) {
                throw new Exception('Data Bill Tidak Ditemukan');
            }

            if ($bill->status === 'paid') {
                throw new Exception('Bill sudah dibayar');
            }

            $transactionCode = $this->generateTransactionCode();
            $gatewayReference = 'MID-' . $transactionCode;

            $transaction = new Transaction();
            $transaction->bill_id = $bill->id;
            $transaction->transaction_code = $transactionCode;
            $transaction->gateway_reference = $gatewayReference;
            $transaction->snap_token = null;
            $transaction->snap_redirect_url = null;
            $transaction->amount_paid = $bill->amount_snapshot ?? $bill->amount;
            $transaction->payment_method = 'online';
            $transaction->status = 'pending';
            $transaction->paid_at = null;
            $transaction->expired_at = null;

            $transaction->save();

            $snapTransaction = $this->midtransSnapService->createTransaction($bill, $gatewayReference);
            $transaction->snap_token = $snapTransaction['token'] ?? null;
            $transaction->snap_redirect_url = $snapTransaction['redirect_url'] ?? null;
            $transaction->save();

            $this->syncBillStatus($bill);

            DB::commit();
            return $transaction->load('bill.student.user', 'bill.paymentType');
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function retry(array $data)
    {
        return $this->initiatePayment($data);
    }

    public function handleWebhook(array $data)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::where('gateway_reference', $data['order_id'])->first();

            if (!$transaction) {
                throw new Exception('Data Transaction Tidak Ditemukan');
            }

            $mappedStatus = $this->mapMidtransStatus(
                $data['transaction_status'] ?? null,
                $data['fraud_status'] ?? null
            );

            $paidAt = $this->resolveWebhookPaidAt($data, $transaction->paid_at);
            $expiredAt = $this->resolveWebhookExpiredAt($data, $transaction->expired_at, $mappedStatus);
            $paymentMethod = $data['payment_type'] ?? $transaction->payment_method;
            $gatewayReference = $data['order_id'] ?? $transaction->gateway_reference;

            $isSameStatus = $transaction->status === $mappedStatus
                && $transaction->gateway_reference === $gatewayReference
                && $transaction->payment_method === $paymentMethod
                && $transaction->paid_at === $paidAt
                && $transaction->expired_at === $expiredAt;

            if ($isSameStatus) {
                DB::commit();
                return $transaction->load('bill.student.user', 'bill.paymentType');
            }

            $transaction->gateway_reference = $gatewayReference;
            $transaction->payment_method = $paymentMethod;
            $transaction->status = $mappedStatus;
            $transaction->paid_at = $paidAt;
            $transaction->expired_at = $expiredAt;
            $transaction->save();

            $this->syncBillStatus($transaction->bill);

            DB::commit();
            return $transaction->load('bill.student.user', 'bill.paymentType');
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    private function generateTransactionCode(): string
    {
        do {
            $transactionCode = 'TRX-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
        } while (Transaction::where('transaction_code', $transactionCode)->exists());

        return $transactionCode;
    }

    private function resolvePaidAt(array $data, ?string $currentPaidAt = null): ?string
    {
        if (($data['status'] ?? null) !== 'success') {
            return null;
        }

        if (!empty($data['paid_at'])) {
            return Carbon::parse($data['paid_at'])->toDateTimeString();
        }

        return $currentPaidAt ?? now()->toDateTimeString();
    }

    private function resolveExpiredAt(array $data, ?string $currentExpiredAt = null): ?string
    {
        if (!empty($data['expired_at'])) {
            return Carbon::parse($data['expired_at'])->toDateTimeString();
        }

        if (($data['status'] ?? null) === 'expired') {
            return $currentExpiredAt ?? now()->toDateTimeString();
        }

        return $currentExpiredAt;
    }

    private function resolveWebhookPaidAt(array $data, ?string $currentPaidAt = null): ?string
    {
        if (($data['transaction_status'] ?? null) === 'settlement' || (($data['transaction_status'] ?? null) === 'capture' && ($data['fraud_status'] ?? null) === 'accept')) {
            if (!empty($data['settlement_time'])) {
                return Carbon::parse($data['settlement_time'])->toDateTimeString();
            }

            return $currentPaidAt ?? now()->toDateTimeString();
        }

        return null;
    }

    private function resolveWebhookExpiredAt(array $data, ?string $currentExpiredAt = null, string $mappedStatus = 'pending'): ?string
    {
        if ($mappedStatus === 'expired') {
            return $currentExpiredAt ?? now()->toDateTimeString();
        }

        return $currentExpiredAt;
    }

    private function mapMidtransStatus(?string $transactionStatus, ?string $fraudStatus): string
    {
        return match ($transactionStatus) {
            'settlement' => 'success',
            'pending' => 'pending',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            'capture' => $fraudStatus === 'accept' ? 'success' : 'pending',
            'deny', 'failure', 'failed' => 'failed',
            default => 'failed',
        };
    }

    private function syncBillStatus(Bill $bill): void
    {
        $settledTransaction = $bill->transaction()
            ->where('status', 'success')
            ->latest('paid_at')
            ->first();

        if ($settledTransaction) {
            $bill->status = 'paid';
            $bill->paid_date = $settledTransaction->paid_at ? Carbon::parse($settledTransaction->paid_at)->toDateString() : now()->toDateString();
            $bill->save();
            return;
        }

        if ($bill->status === 'paid') {
            return;
        }

        $bill->status = Carbon::parse($bill->due_date)->isPast() ? 'overdue' : 'pending';
        $bill->paid_date = null;
        $bill->save();
    }
}
