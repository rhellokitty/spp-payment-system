<?php

namespace App\Repositories;

use App\Interfaces\ClassRoomRepositoriesInterface;
use App\Models\ClassRoom;

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
}
