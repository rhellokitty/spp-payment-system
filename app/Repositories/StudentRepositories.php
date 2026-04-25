<?php

namespace App\Repositories;

use App\Interfaces\StudentRepositoriesInterface;
use App\Models\Student;
use Exception;
use Illuminate\Support\Facades\DB;

class StudentRepositories implements StudentRepositoriesInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = Student::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->latest()->with('user', 'classRoom');

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
        $query = Student::where('id', $id)->with('user', 'classRoom');
        return $query->first();
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
                'role' => $data['role'],
            ]);

            $student = new Student();
            $student->user_id = $user->id;
            $student->class_room_id = $data['class_room_id'] ?? null;
            $student->birth_date = $data['birth_date'];
            $student->parent_name = $data['parent_name'];
            $student->parent_phone_number = $data['parent_phone_number'];
            $student->address = $data['address'];
            $student->gender = $data['gender'];
            $student->status = $data['status'];

            $student->save();
            DB::commit();
            return $student;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $student = Student::find($id);

            $userRepositories = new UserRepositories();

            $user = $userRepositories->update($student->user_id, [
                'name' => $data['name'],
                'username' => $data['username'] ?? $student->user->username,
                'password' => bcrypt($data['password']) ? bcrypt($data['password']) : $student->user->password,
            ]);

            $student->user_id = $user->id;
            $student->class_room_id = array_key_exists('class_room_id', $data) ? $data['class_room_id'] : $student->class_room_id;
            $student->birth_date = $data['birth_date'];
            $student->parent_name = $data['parent_name'];
            $student->parent_phone_number = $data['parent_phone_number'];
            $student->address = $data['address'];
            $student->gender = $data['gender'];
            $student->status = $data['status'];

            $student->save();
            DB::commit();
            return $student;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $student = Student::find($id)->with('user')->find($id);
            $student->user()->delete();
            $student->delete();

            DB::commit();
            return $student;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function removeFromClass(string $id)
    {
        $student = Student::find($id);
        $student->class_room_id = null;
        $student->save();
        return $student;
    }
}
