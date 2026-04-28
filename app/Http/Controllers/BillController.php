<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\BillStoreRequest;
use App\Http\Requests\BillUpdateRequest;
use App\Http\Requests\GenerateBillRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\BillRepositoriesInterface;
use Exception;
use Illuminate\Http\Request;

class BillController extends Controller
{
    private BillRepositoriesInterface $billRepositories;

    public function __construct(BillRepositoriesInterface $billRepositories)
    {
        $this->billRepositories = $billRepositories;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $bills = $this->billRepositories->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Bill Berhasil Diambil', BillResource::collection($bills), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Bill Gagal Diambil',
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
            $bills = $this->billRepositories->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Bill Berhasil Diambil', PaginateResource::make($bills, BillResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Bill Gagal Diambil',
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
    public function store(BillStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $bill = $this->billRepositories->create($request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Bill Berhasil Ditambahkan',
                BillResource::make($bill),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Bill Gagal Ditambahkan',
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
            $bill = $this->billRepositories->getById($id);

            if (!$bill) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Bill Tidak Ditemukan',
                    null,
                    404
                );
            }

            return ResponseHelper::jsonResponse(
                true,
                'Data Bill Berhasil Diambil',
                BillResource::make($bill),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Bill Gagal Diambil',
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
    public function update(BillUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $bill = $this->billRepositories->getById($id);

            if (!$bill) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Bill Tidak Ditemukan',
                    null,
                    404
                );
            }

            $bill = $this->billRepositories->update($id, $request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Bill Berhasil Diupdate',
                BillResource::make($bill),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Bill Gagal Diupdate',
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
            $bill = $this->billRepositories->getById($id);

            if (!$bill) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Bill Tidak Ditemukan',
                    null,
                    404
                );
            }

            $bill = $this->billRepositories->delete($id);

            return ResponseHelper::jsonResponse(
                true,
                'Data Bill Berhasil Dihapus',
                BillResource::make($bill),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Bill Gagal Dihapus',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }

    public function generateByClassRoom(GenerateBillRequest $request)
    {
        $request = $request->validated();

        try {
            $bills = $this->billRepositories->generateByClassRoom($request);

            if ($bills->isEmpty()) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Bill untuk kelas dan periode ini sudah pernah digenerate',
                    null,
                    422
                );
            }

            return ResponseHelper::jsonResponse(
                true,
                'Data Bill Berhasil Digenerate',
                BillResource::collection($bills),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Bill Gagal Digenerate',
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
