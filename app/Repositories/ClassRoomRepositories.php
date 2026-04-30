<?php

namespace App\Repositories;

use App\Interfaces\ClassRoomRepositoriesInterface;
use App\Models\ClassRoom;
use Exception;
use Illuminate\Support\Facades\DB;

class ClassRoomRepositories implements ClassRoomRepositoriesInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = ClassRoom::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->latest()->with('teacher', 'student');

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
        return ClassRoom::where('id', $id)
            ->with([
                'teacher',
                'student' => fn($q) => $q->latest()->with('user'),
            ])
            ->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $classRoom = new ClassRoom();

            $classRoom->teacher_id = $data['teacher_id'] ?? null;
            $classRoom->school_level = $data['school_level'];
            $classRoom->name = $data['name'];
            $classRoom->grade = $data['grade'];
            $classRoom->start_year = $data['start_year'];
            $classRoom->end_year = $data['end_year'];

            $classRoom->save();
            DB::commit();
            return $classRoom;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $classRoom = ClassRoom::find($id);

            $classRoom->teacher_id = $data['teacher_id'];
            $classRoom->school_level = $data['school_level'];
            $classRoom->name = $data['name'];
            $classRoom->grade = $data['grade'];
            $classRoom->start_year = $data['start_year'];
            $classRoom->end_year = $data['end_year'];

            $classRoom->save();
            DB::commit();
            return $classRoom;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $student = ClassRoom::find($id);
            $student->delete();

            DB::commit();
            return $student;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
