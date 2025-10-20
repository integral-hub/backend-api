<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request to filter strings based on various optional criteria.
 */
class FilterStringsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_palindrome' => 'nullable|boolean',
            'min_length' => 'nullable|integer|min:0',
            'max_length' => 'nullable|integer|min:1',
            'word_count' => 'nullable|integer|min:0',
            'contains_character' => 'nullable|string|size:1',
        ];
    }
}
