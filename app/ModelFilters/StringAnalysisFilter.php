<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class StringAnalysisFilter extends ModelFilter
{
    public $relations = [];

    /**
     * Filter by is_palindrome = true/false
     */
    public function isPalindrome($value)
    {
        if (is_bool($value) || in_array($value, ['true', 'false', 0, 1, '0', '1'], true)) {
            return $this->where('properties->is_palindrome', filter_var($value, FILTER_VALIDATE_BOOLEAN));
        }

        return $this;
    }

    /**
     * Filter strings with a minimum length
     */
    public function minLength($value)
    {
        return $this->where('properties->length', '>=', (int) $value);
    }

    /**
     * Filter strings with a maximum length
     */
    public function maxLength($value)
    {
        return $this->where('properties->length', '<=', (int) $value);
    }

    /**
     * Filter strings by exact word count
     */
    public function wordCount($value)
    {
        return $this->where('properties->word_count', (int) $value);
    }

    /**
     * Filter strings containing a specific character
     */
    public function containsCharacter($char)
    {
        if (!is_string($char) || strlen($char) !== 1) {
            return $this;
        }

        return $this->where('value', 'like', '%' . $char . '%');
    }

    /**
     * Basic search in the string value (optional extra filter)
     */
    public function search($term)
    {
        if (!empty($term)) {
            return $this->where('value', 'like', '%' . $term . '%');
        }

        return $this;
    }

    /**
     * Sorting direction (asc or desc) based on created_at
     */
    public function sort($direction)
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        return $this->orderBy('created_at', $direction);
    }
}
