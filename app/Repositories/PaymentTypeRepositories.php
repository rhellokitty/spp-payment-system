<?php

namespace App\Repositories;

use App\Interfaces\PaymentTypeRepositoriesInterface;
use App\Models\PaymentType;
use Exception;
use Illuminate\Support\Facades\DB;

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

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $paymentType = new PaymentType();

            $paymentType->name = $data['name'];
            $paymentType->due_day = $data['due_day'] ?? null;
            $paymentType->amount = $data['amount'];
            $paymentType->is_recurring = $data['is_recurring'];

            $paymentType->save();
            DB::commit();
            return $paymentType;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $paymentType = PaymentType::find($id);

            $paymentType->name = $data['name'];
            $paymentType->due_day = $data['due_day'] ?? null;
            $paymentType->amount = $data['amount'];
            $paymentType->is_recurring = $data['is_recurring'];

            $paymentType->save();
            DB::commit();
            return $paymentType;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $paymentType = PaymentType::find($id);
            $paymentType->delete();

            DB::commit();
            return $paymentType;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
