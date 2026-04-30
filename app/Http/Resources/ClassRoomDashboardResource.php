<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total = $this->student_count ?? 0;
        $paid  = $this->paid_student_count ?? 0;

        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'grade'               => $this->grade,
            'school_level'        => $this->school_level,
            'academic_year'       => $this->start_year . '/' . $this->end_year,
            'homeroom_teacher'    => $this->teacher?->user?->name,
            'total_students'      => $total,
            'paid_count'          => $paid,
            'unpaid_count'        => $this->unpaid_student_count ?? 0,
            'completion_rate'     => $total > 0 ? round(($paid / $total) * 100, 1) : 0,
        ];
    }
}
