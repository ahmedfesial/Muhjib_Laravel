<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBasketsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'client_id' => 'required|exists:clients,id',
        'created_by' => 'required|exists:users,id',
        'include_price_flag' => 'boolean',
        'status' => 'required|string|in:pending,in_progress,done',
    ];
    }
}
