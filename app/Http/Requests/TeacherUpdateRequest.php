<?php

namespace App\Http\Requests;

use App\Models\Teacher;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherUpdateRequest extends FormRequest
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
        $teacherId = $this->route('teacher');
        $teacher = Teacher::find($teacherId);
        $userId = $teacher?->user_id;

        return [
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', Rule::unique('users', 'username')->ignore($userId)],
            'password' => 'nullable|string|min:8',
            'academic_title' => 'required|string',
            'phone_number' => 'required|string',
            'email' => ['nullable', 'string', Rule::unique('teachers', 'email')->ignore($teacherId)],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'nip' => 'NIP',
        ];
    }
}
