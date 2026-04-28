<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\TransactionInitiateRequest;
use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\TransactionResource;
use App\Interfaces\TransactionRepositoriesInterface;
use App\Services\MidtransSnapService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    private TransactionRepositoriesInterface $transactionRepositories;
    private MidtransSnapService $midtransSnapService;

    public function __construct(TransactionRepositoriesInterface $transactionRepositories, MidtransSnapService $midtransSnapService)
    {
        $this->transactionRepositories = $transactionRepositories;
        $this->midtransSnapService = $midtransSnapService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $transactions = $this->transactionRepositories->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Transaction Berhasil Diambil', TransactionResource::collection($transactions), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Transaction Gagal Diambil',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer',
        ]);

        try {
            $transactions = $this->transactionRepositories->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Transaction Berhasil Diambil', PaginateResource::make($transactions, TransactionResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Transaction Gagal Diambil',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $transaction = $this->transactionRepositories->create($request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Transaction Berhasil Ditambahkan',
                TransactionResource::make($transaction),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Transaction Gagal Ditambahkan',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $transaction = $this->transactionRepositories->getById($id);

            if (!$transaction) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Transaction Tidak Ditemukan',
                    null,
                    404
                );
            }

            return ResponseHelper::jsonResponse(
                true,
                'Data Transaction Berhasil Diambil',
                TransactionResource::make($transaction),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Transaction Gagal Diambil',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }


    public function retry(TransactionInitiateRequest $request)
    {
        $request = $request->validated();

        try {
            $transaction = $this->transactionRepositories->retry($request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Transaction Retry Berhasil Ditambahkan',
                TransactionResource::make($transaction),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Transaction Retry Gagal Ditambahkan',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }

    public function initiatePayment(TransactionInitiateRequest $request)
    {
        $request = $request->validated();

        try {
            $transaction = $this->transactionRepositories->initiatePayment($request);

            return ResponseHelper::jsonResponse(
                true,
                'Payment Transaction Berhasil Diinisiasi',
                TransactionResource::make($transaction),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Payment Transaction Gagal Diinisiasi',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }

    public function webhook(Request $request)
    {

        $payload = $request->validate([
            'order_id' => 'required|string',
            'status_code' => 'required|string',
            'gross_amount' => 'required',
            'signature_key' => 'required|string',
            'transaction_status' => 'required|string',
            'fraud_status' => 'nullable|string',
            'payment_type' => 'nullable|string',
            'settlement_time' => 'nullable|string',
        ]);

        try {
            if (!$this->midtransSnapService->verifySignature($payload)) {

                return ResponseHelper::jsonResponse(
                    false,
                    'Signature Midtrans Tidak Valid',
                    null,
                    403
                );
            }

            $transaction = $this->transactionRepositories->handleWebhook($payload);

            return ResponseHelper::jsonResponse(
                true,
                'Webhook Transaction Berhasil Diproses',
                TransactionResource::make($transaction),
                200
            );
        } catch (Exception $e) {

            return ResponseHelper::jsonResponse(
                false,
                'Webhook Transaction Gagal Diproses',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }
}
