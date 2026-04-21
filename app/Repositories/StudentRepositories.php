<?php

namespace App\Repositories;

use App\Interfaces\StudentRepositoriesInterface;
use App\Models\Student;

class StudentRepositories implements StudentRepositoriesInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = Student::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->latest();

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
