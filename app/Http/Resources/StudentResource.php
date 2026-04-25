<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'birth_date' => $this->birth_date,
            'parent_name' => $this->parent_name,
            'parent_phone_number' => $this->parent_phone_number,
            'address' => $this->address,
            'gender' => $this->gender,
            'status' => $this->status,
            'user' => new UserResource($this->user),
            'classRoom' => ClassRoomResource::make($this->classRoom),
        ];
    }
}
