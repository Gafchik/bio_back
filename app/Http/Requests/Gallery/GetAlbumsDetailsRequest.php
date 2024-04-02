<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class GetAlbumsDetailsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => [
                'required',
                'int',
                'min:1'
            ],
            'is_image' => [
                'required',
                'boolean',
            ]
        ];
    }
}
