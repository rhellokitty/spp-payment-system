<?php

namespace App\Repositories;

use App\Interfaces\BillRepositoriesInterface;
use App\Models\Bill;
use App\Models\PaymentType;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BillRepositories implements BillRepositoriesInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = Bill::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->latest()->with('student.user', 'student.classRoom', 'paymentType');

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
        $query = Bill::where('id', $id)->with('student.user', 'student.classRoom', 'paymentType');
        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $paymentType = PaymentType::find($data['payment_type_id']);

            $bill = new Bill();
            $bill->student_id = $data['student_id'];
            $bill->payment_type_id = $paymentType->id;
            $bill->amount = $paymentType->amount;
            $bill->amount_snapshot = $paymentType->amount;
            $bill->payment_type_name_snapshot = $paymentType->name;
            $bill->billing_month = $data['billing_month'];
            $bill->billing_year = $data['billing_year'];
            $bill->due_date = $this->resolveDueDate($data, $paymentType);
            $bill->status = $data['status'] ?? 'pending';
            $bill->paid_date = $this->resolvePaidDate($data);

            $bill->save();
            DB::commit();
            return $bill;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $bill = Bill::find($id);
            $paymentType = PaymentType::find($data['payment_type_id']);

            $bill->student_id = $data['student_id'];
            $bill->payment_type_id = $paymentType->id;
            $bill->amount = $paymentType->amount;
            $bill->amount_snapshot = $paymentType->amount;
            $bill->payment_type_name_snapshot = $paymentType->name;
            $bill->billing_month = $data['billing_month'];
            $bill->billing_year = $data['billing_year'];
            $bill->due_date = $this->resolveDueDate($data, $paymentType);
            $bill->status = $data['status'] ?? $bill->status;
            $bill->paid_date = $this->resolvePaidDate($data, $bill->paid_date);

            $bill->save();
            DB::commit();
            return $bill;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $bill = Bill::find($id);
            $bill->delete();

            DB::commit();
            return $bill;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function generateByClassRoom(array $data)
    {
        DB::beginTransaction();

        try {
            $paymentType = PaymentType::find($data['payment_type_id']);
            $students = Student::where('class_room_id', $data['classroom_id'])
                ->where('status', 'active')
                ->get();
            $bills = collect();

            foreach ($students as $student) {
                $isBillExists = Bill::where('student_id', $student->id)
                    ->where('payment_type_id', $paymentType->id)
                    ->where('billing_month', $data['billing_month'])
                    ->where('billing_year', $data['billing_year'])
                    ->whereNull('deleted_at')
                    ->exists();

                if ($isBillExists) {
                    continue;
                }

                $bill = new Bill();
                $bill->student_id = $student->id;
                $bill->payment_type_id = $paymentType->id;
                $bill->amount = $paymentType->amount;
                $bill->amount_snapshot = $paymentType->amount;
                $bill->payment_type_name_snapshot = $paymentType->name;
                $bill->billing_month = $data['billing_month'];
                $bill->billing_year = $data['billing_year'];
                $bill->due_date = $this->resolveDueDate([
                    'billing_month' => $data['billing_month'],
                    'billing_year' => $data['billing_year'],
                ], $paymentType);
                $bill->status = 'pending';
                $bill->paid_date = null;
                $bill->save();

                $bills->push($bill);
            }

            DB::commit();
            return $bills;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    private function resolveDueDate(array $data, PaymentType $paymentType): string
    {
        if (!empty($data['due_date'])) {
            return Carbon::parse($data['due_date'])->toDateString();
        }

        $dueDay = $paymentType->due_day ?: 1;
        $date = Carbon::createFromDate($data['billing_year'], $data['billing_month'], 1);
        $dueDay = min($dueDay, $date->daysInMonth);

        return $date->day($dueDay)->toDateString();
    }

    private function resolvePaidDate(array $data, ?string $currentPaidDate = null): ?string
    {
        if (($data['status'] ?? 'pending') !== 'paid') {
            return null;
        }

        if (!empty($data['paid_date'])) {
            return Carbon::parse($data['paid_date'])->toDateString();
        }

        return $currentPaidDate;
    }
}
