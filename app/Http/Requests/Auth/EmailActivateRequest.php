<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class EmailActivateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'min:6',
                'max:6',
            ],
        ];
    }
}
