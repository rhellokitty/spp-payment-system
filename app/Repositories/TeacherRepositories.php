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

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $userRepositories = new UserRepositories();

            $user = $userRepositories->create([
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => bcrypt($data['password']),
            ]);

            $user->assignRole('teacher');

            $teacher = new Teacher();
            $teacher->user_id = $user->id;
            $teacher->academic_title = $data['academic_title'];
            $teacher->phone_number = $data['phone_number'];
            $teacher->email = $data['email'];

            $teacher->save();
            DB::commit();
            return $teacher;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $teacher = Teacher::find($id);

            $userRepositories = new UserRepositories();

            $user = $userRepositories->update($teacher->user_id, [
                'name' => $data['name'],
                'username' => $data['username'] ?? $teacher->user->username,
                'password' => !empty($data['password']) ? bcrypt($data['password']) : $teacher->user->password,
            ]);

            $teacher->user_id = $user->id;
            $teacher->academic_title = $data['academic_title'];
            $teacher->phone_number = $data['phone_number'];
            $teacher->email = $data['email'];

            $teacher->save();
            DB::commit();
            return $teacher;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $teacher = Teacher::find($id)->with('user')->find($id);
            $teacher->user()->delete();
            $teacher->delete();

            DB::commit();
            return $teacher;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
