<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class updateDeskRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'desk_number' => 'sometimes|integer|unique:desks,desk_number',
            'capacity' => 'sometimes|integer|min:2|max:10',
            'status' => 'sometimes|string|in:available,unavailable',
        ];
    }
}
