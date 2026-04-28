<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\PaymentTypeStoreRequest;
use App\Http\Requests\PaymentTypeUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PaymentTypeResource;
use App\Interfaces\PaymentTypeRepositoriesInterface;
use Exception;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{

    private PaymentTypeRepositoriesInterface $paymentTypeRepositories;

    public function __construct(PaymentTypeRepositoriesInterface $paymentTypeRepositories)
    {
        $this->paymentTypeRepositories = $paymentTypeRepositories;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $paymentTypes = $this->paymentTypeRepositories->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Payment Type Berhasil Diambil', PaymentTypeResource::collection($paymentTypes), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Payment Type Gagal Diambil',
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
            $students = $this->paymentTypeRepositories->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Payment Type Berhasil Diambil', PaginateResource::make($students, PaymentTypeResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Payment Type Gagal Diambil',
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
    public function store(PaymentTypeStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $paymentType = $this->paymentTypeRepositories->create($request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Payment Type Berhasil Ditambahkan',
                PaymentTypeResource::make($paymentType),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Payment Type Gagal Ditambahkan',
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
            $paymentType = $this->paymentTypeRepositories->getById($id);

            if (!$paymentType) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Payment Type Tidak Ditemukan',
                    null,
                    404
                );
            }

            return ResponseHelper::jsonResponse(
                true,
                'Data Payment Type Berhasil Diambil',
                PaymentTypeResource::make($paymentType),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Payment Type Gagal Diambil',
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
     * Update the specified resource in storage.
     */
    public function update(PaymentTypeUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $paymentType = $this->paymentTypeRepositories->getById($id);

            if (!$paymentType) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Payment Type Tidak Ditemukan',
                    null,
                    404
                );
            }

            $paymentType = $this->paymentTypeRepositories->update($id, $request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Payment Type Berhasil Diupdate',
                PaymentTypeResource::make($paymentType),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Payment Type Gagal Diupdate',
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $paymentType = $this->paymentTypeRepositories->getById($id);

            if (!$paymentType) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Payment Type Tidak Ditemukan',
                    null,
                    404
                );
            }

            $paymentType = $this->paymentTypeRepositories->delete($id);

            return ResponseHelper::jsonResponse(
                true,
                'Data Payment Type Berhasil Dihapus',
                PaymentTypeResource::make($paymentType),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Payment Type Gagal Dihapus',
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
