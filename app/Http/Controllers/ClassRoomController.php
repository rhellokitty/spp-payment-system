<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ClassRoomStoreRequest;
use App\Http\Resources\ClassRoomResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\ClassRoomRepositoriesInterface;
use App\Models\ClassRoom;
use Exception;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    private ClassRoomRepositoriesInterface $classRoomRepositories;

    public function __construct(ClassRoomRepositoriesInterface $classRoomRepositories)
    {
        $this->classRoomRepositories = $classRoomRepositories;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $students = $this->classRoomRepositories->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data ClassRoom Berhasil Diambil', ClassRoomResource::collection($students), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data ClassRoom Gagal Diambil',
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
            $students = $this->classRoomRepositories->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Class Room Berhasil Diambil', PaginateResource::make($students, ClassRoomResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Class Room Gagal Diambil',
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
    public function store(ClassRoomStoreRequest $request)
    {
        $request = $request->validated();

        try {

            $student = $this->classRoomRepositories->create($request);

            return ResponseHelper::jsonResponse(
                true,
                'Data Class Room Berhasil Ditambahkan',
                ClassRoomResource::make($student),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Class Room Gagal Ditambahkan',
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
    public function show(ClassRoom $classRoom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassRoom $classRoom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassRoom $classRoom)
    {
        //
    }
}
