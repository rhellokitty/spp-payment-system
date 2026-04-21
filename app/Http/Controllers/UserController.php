<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoriesInterface;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private UserRepositoriesInterface $userRepositories;

    public function __construct(UserRepositoriesInterface $userRepositories)
    {
        $this->userRepositories = $userRepositories;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $students = $this->userRepositories->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Diambil', UserResource::collection($students), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data User Gagal Diambil',
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
            $users = $this->userRepositories->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Diambil', PaginateResource::make($users, UserResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data User Gagal Diambil',
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
    public function store(UserStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $user = $this->userRepositories->create($request);

            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Ditambahkan', UserResource::make($user), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data User Gagal Ditambahkan',
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
            $user = $this->userRepositories->getById($id);

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'Data User Tidak Ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Diambil', UserResource::make($user), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data User Gagal Diambil',
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
    public function update(UserUpdateRequest $request, string $id)
    {
        $request = $request->validated();
        try {
            $user = $this->userRepositories->getById($id);

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'Data User Tidak Ditemukan', null, 404);
            }

            $user = $this->userRepositories->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Diupdate', UserResource::make($user), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data User Gagal Diupdate',
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
            $user = $this->userRepositories->getById($id);

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'Data User Tidak Ditemukan', null, 404);
            }

            $user = $this->userRepositories->delete($id);
            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Dihapus', UserResource::make($user), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data User Gagal Dihapus',
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
