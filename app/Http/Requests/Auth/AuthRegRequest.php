<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthRegRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'phone' => [
                'required',
                'regex:/[0-9 ]+$/'
            ],
            'password' => [
                'required',
                'confirmed',
                'min:' . config('auth.passwords.min_length'), // 8 length
            ],
        ];
    }
}
