<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_level' => $this->school_level,
            'name' => $this->name,
            'grade' => $this->grade,
            'start_year' => $this->start_year,
            'end_year' => $this->end_year,
            'teacher' => TeacherResource::make($this->teacher),
            'student' => StudentResource::collection($this->whenLoaded('student')),
        ];
    }
}
