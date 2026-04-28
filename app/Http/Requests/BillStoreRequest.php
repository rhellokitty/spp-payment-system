<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BillStoreRequest extends FormRequest
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
            'student_id' => 'required|exists:students,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'billing_month' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('bills')->where(function ($query) {
                    return $query->where('student_id', $this->student_id)
                        ->where('payment_type_id', $this->payment_type_id)
                        ->where('billing_year', $this->billing_year);
                }),
            ],
            'billing_year' => 'required|integer|digits:4',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pending,paid,overdue,expired',
            'paid_date' => 'nullable|date|required_if:status,paid',
        ];
    }
}
