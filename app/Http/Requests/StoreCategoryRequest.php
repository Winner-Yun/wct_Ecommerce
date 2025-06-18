<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // or add your auth logic here
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}

