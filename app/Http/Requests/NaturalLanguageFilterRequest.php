<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request to filter strings using a natural language query.
 */
class NaturalLanguageFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:3',
        ];
    }
}
