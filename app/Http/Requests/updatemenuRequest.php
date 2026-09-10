<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updatemenuRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string',
            'slug' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|integer',
            'image_url' => 'sometimes|mimes:jpg,jpeg,png',
            'is_available' => 'sometimes|boolean'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
