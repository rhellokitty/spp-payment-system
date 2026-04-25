<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StudentStoreRequest extends FormRequest
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
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string',
            'role' => 'required|in:super_admin,admin,teacher,student',
            'class_room_id' => 'required|exists:class_rooms,id',
            'birth_date' => 'required|date',
            'parent_name' => 'required|string',
            'parent_phone_number' => 'required|string',
            'address' => 'required|string',
            'gender' => 'required|string',
            'status' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'username' => 'NISN',
        ];
    }
}
