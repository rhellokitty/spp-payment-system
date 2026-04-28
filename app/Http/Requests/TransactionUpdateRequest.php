<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
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
            'gateway_reference' => 'nullable|string|max:255',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'required|in:pending,success,failed,expired,cancelled',
            'paid_at' => 'nullable|date|required_if:status,success',
            'expired_at' => 'nullable|date|required_if:status,expired',
        ];
    }
}
