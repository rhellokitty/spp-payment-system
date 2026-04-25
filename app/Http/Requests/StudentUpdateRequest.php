<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|unique:users,username,' . Student::find($this->route('student'))->user_id,
            'class_room_id' => 'nullable|exists:class_rooms,id',
            'password' => 'nullable|string|min:8',
            'birth_date' => 'required|date',
            'parent_name' => 'required|string',
            'parent_phone_number' => 'required|string',
            'address' => 'nullable|string',
            'gender' => 'required|string',
            'status' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'NISN',
        ];
    }
}
