<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class DetailCardRequest extends FormRequest
{
    public function rules()
{
    return [
        'id' => [
            'required',
            'int',
            'min:1'
        ],
    ];
}
}
