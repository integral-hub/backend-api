<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class StringAnalysis extends Model
{
    use Filterable;
    protected $fillable = ['value', 'sha256_hash', 'properties'];

    protected static function booted()
    {
        static::creating(function (self $string) {
            $string->sha256_hash = $string->hashed();
            $string->properties = $string->generateProperties();
        });

        static::updating(function (self $string) {
            $string->properties = $string->generateProperties();
        });
    }

    /**
     * Automatically cast computed properties to array
     */
    protected $casts = [
        'properties' => 'array',
    ];
    
    protected $hidden = [
        'sha256_hash'
    ];

    /**
     * Generate computed properties for storage.
     */
    public function generateProperties(): array
    {
        return [
            'length' => strlen($this->value),
            'is_palindrome' => $this->isPalindrome(),
            'unique_characters' => count(array_unique(str_split($this->value))),
            'word_count' => str_word_count($this->value),
            'sha256_hash' =>  $this->hashed(),
            'character_frequency_map' => $this->characterFrequencyMap(),
        ];
    }

    /**
     * hash string
     */
    public function hashed(): string    
    {
        return hash('sha256', strtolower($this->value));
    }

    /**
     * Determine if string is palindrome (case-insensitive)
     */
    public function isPalindrome(): bool
    {
        $cleaned = strtolower(preg_replace('/\s+/', '', $this->value));
        return (bool) $cleaned === strrev($cleaned);
    }

    /**
     * Map of each character to its frequency.
     */
    public function characterFrequencyMap(): array
    {
        $chars = str_split($this->value);
        $frequencies = [];

        foreach ($chars as $char) {
            $frequencies[$char] = ($frequencies[$char] ?? 0) + 1;
        }

        return $frequencies;
    }
}
