<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoriesInterface;
use App\Models\User;

class UserRepositories implements UserRepositoriesInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = User::where(function ($query) use ($search) {
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
