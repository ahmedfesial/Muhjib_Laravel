<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
<<<<<<< HEAD
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'sometimes|string|in:user,admin,super_admin',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
=======
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        ];
    }
}
