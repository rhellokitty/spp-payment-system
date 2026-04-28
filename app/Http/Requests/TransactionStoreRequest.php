<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
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
            'bill_id' => 'required|exists:bills,id',
            'gateway_reference' => 'nullable|string|max:255',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'expired_at' => 'nullable|date',
        ];
    }
}
