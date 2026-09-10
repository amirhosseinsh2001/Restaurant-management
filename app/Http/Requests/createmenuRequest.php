<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class createmenuRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id|integer',
            'name' => 'required|string|max:255',
            'slug' => 'string|unique:menu_items,slug',
            'description' => 'string|max:255',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|mimes:jpeg,jpg,png,gif|max:2048',
            'is_available' => 'boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
