<?php

namespace App\Repositories;

use App\Interfaces\TeacherRepositoriesInterface;
use App\Models\Teacher;
use Exception;
use Illuminate\Support\Facades\DB;

class TeacherRepositories implements TeacherRepositoriesInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = Teacher::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->latest()->with('user');

        if ($limit) {
            $query->limit($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getById(string $id)
    {
        $query = Teacher::where('id', $id)->with('user');
        return $query->first();
    }

    public function getAllPaginated(?string $search, ?int $rowPerPage)
    {
        $query = $this->getAll($search, $rowPerPage, false);
        return $query->paginate($rowPerPage);
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $student = Teacher::find($id)->with('user')->find($id);
            $student->user()->delete();
            $student->delete();

            DB::commit();
            return $student;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
