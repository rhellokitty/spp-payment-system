<?php

namespace App\Repositories;

use App\Interfaces\PaymentTypeRepositoriesInterface;
use App\Models\PaymentType;

class PaymentTypeRepositories implements PaymentTypeRepositoriesInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = PaymentType::where(function ($query) use ($search) {
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

    public function getById(string $id)
    {
        $query = PaymentType::where('id', $id);
        return $query->first();
    }
}
