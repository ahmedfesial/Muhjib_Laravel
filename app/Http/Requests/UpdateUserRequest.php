<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
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
        ];
    }
}
