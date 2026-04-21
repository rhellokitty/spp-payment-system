<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
