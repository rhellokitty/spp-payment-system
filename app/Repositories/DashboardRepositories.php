<?php

namespace App\Repositories;

use App\Interfaces\DashboardRepoositoriesInterface;
use App\Models\Bill;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Transaction;

class DashboardRepositories implements DashboardRepoositoriesInterface
{
    public function getDashboardData(): array
    {
        return [
            'summary'           => $this->getSummary(),
            'classes_breakdown' => $this->getClassesBreakdown(),
        ];
    }

    private function getSummary(): array
    {
        return [
            'total_students'      => Student::count(),
            'total_classes'       => ClassRoom::count(),
            'total_bills'         => Bill::count(),
            'total_paid'          => Bill::where('status', 'paid')->count(),
            'total_unpaid'        => Bill::where('status', 'unpaid')->count(),
            'total_transactions'  => Transaction::where('status', 'settlement')->count(),
        ];
    }

    private function getClassesBreakdown(): object
    {
        return ClassRoom::query()
            ->with('teacher.user')
            ->withCount([
                'student',
                'student as paid_student_count' => fn($q) => $q->whereHas(
                    'bill',
                    fn($q) => $q->where('status', 'paid')
                ),
                'student as unpaid_student_count' => fn($q) => $q->whereHas(
                    'bill',
                    fn($q) => $q->where('status', 'unpaid')
                ),
            ])
            ->get();
    }
}
