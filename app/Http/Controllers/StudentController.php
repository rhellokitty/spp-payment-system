<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\StudentResource;
use App\Interfaces\StudentRepositoriesInterface;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    private StudentRepositoriesInterface $studentRepositories;

    public function __construct(StudentRepositoriesInterface $studentRepositories)
    {
        $this->studentRepositories = $studentRepositories;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $students = $this->studentRepositories->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Student Berhasil Diambil', StudentResource::collection($students), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Student Gagal Diambil',
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
            $students = $this->studentRepositories->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Student Berhasil Diambil', PaginateResource::make($students, StudentResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Student Gagal Diambil',
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
