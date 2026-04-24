<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\TeacherStoreRequest;
use App\Http\Requests\TeacherUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\TeacherResource;
use App\Interfaces\TeacherRepositoriesInterface;
use App\Models\Teacher;
use Exception;
use Illuminate\Http\Request;

class TeacherController extends Controller
{

    private TeacherRepositoriesInterface $teacherRepositories;

    public function __construct(TeacherRepositoriesInterface $teacherRepositories)
    {
        $this->teacherRepositories = $teacherRepositories;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $teachers = $this->teacherRepositories->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Teacher Berhasil Diambil', TeacherResource::collection($teachers), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Teacher Gagal Diambil',
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
            $teachers = $this->teacherRepositories->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Teacher Berhasil Diambil', PaginateResource::make($teachers, TeacherResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Teacher Gagal Diambil',
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
    public function store(TeacherStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $teachers = $this->teacherRepositories->create($request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Teacher Berhasil Ditambahkan',
                TeacherResource::make($teachers),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Teacher Gagal Ditambahkan',
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
            $teachers = $this->teacherRepositories->getById($id);

            if (!$teachers) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Teacher Tidak Ditemukan',
                    null,
                    404
                );
            }

            return ResponseHelper::jsonResponse(
                true,
                'Data Teacher Berhasil Diambil',
                TeacherResource::make($teachers),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Teacher Gagal Diambil',
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
    public function update(TeacherUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $teacher = $this->teacherRepositories->getById($id);

            if (!$teacher) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Teacher  Tidak Ditemukan',
                    null,
                    404
                );
            }

            $teacher = $this->teacherRepositories->update($id, $request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Teacher  Berhasil Diupdate',
                TeacherResource::make($teacher),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Teacher  Gagal Diupdate',
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
            $teachers = $this->teacherRepositories->getById($id);

            if (!$teachers) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Data Teacher Tidak Ditemukan',
                    null,
                    404
                );
            }

            $teachers = $this->teacherRepositories->delete($id);
            return ResponseHelper::jsonResponse(
                true,
                'Data Teacher Berhasil Dihapus',
                TeacherResource::make($teachers),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Teacher Gagal Dihapus',
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
