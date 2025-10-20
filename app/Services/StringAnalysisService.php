<?php

namespace App\Services;

use App\Enums\Http;
use App\Interfaces\StringAnalysisInterface;
use App\Models\StringAnalysis;

class StringAnalysisService implements StringAnalysisInterface
{
    /**
     * Analyze and store a string if it does not already exist.
     *
     * @param string $value
     * @return array [status_code, data_or_message]
     */
    public function analyzeString($value): array
    {
        // Validate string type
        if (!is_string($value)) {
            return [['message' => '"value" (must be a string)'], Http::UNPROCESSABLE_ENTITY];
        }

        $hash = hash('sha256', strtolower($value));

        // Check if already exists
        $existing = StringAnalysis::where('sha256_hash', $hash)->first();
        if ($existing) {
            return [['message' => 'String already exists in the system'], Http::CONFLICT];
        }

        // Create new record
        $record = StringAnalysis::create([
            'value' => $value,
        ]);

        return $this->formatResponse($record);
    }

    /**
     * Find a specific analyzed string by its original value.
     *
     * @param string $value
     * @return array
     */
    public function getStringByValue(string $value): array
    {
        $hash = hash('sha256', strtolower($value));

        $record = StringAnalysis::where('sha256_hash', $hash)->first();

        if (!$record) {
            return [['message' => 'String does not exists in the system'], Http::NOT_FOUND];
        }

        return $this->formatResponse($record);
    }

    /**
     * Filter strings using structured query parameters.
     *
     * @param array $filters
     */
    public function getFilteredStrings(array $filters): array
    {
        $query = StringAnalysis::filter($filters);

        $results = $query->get()->map(fn($record) => $this->formatResponse($record));

        return [
                'data' => $results,
                'count' => $results->count(),
                'filters_applied' => $filters
        ];
    }

    /**
     * Filter strings using natural language query.
     *
     * @param string $query
     */
    public function filterByNaturalLanguage(string $query): array
    {
        $parsedFilters = $this->interpretNaturalLanguage($query);

        if ($parsedFilters === null) {
            return [['message' => 'Unable to parse natural language query'], Http::BAD_REQUEST];
        }

        $data = $this->getFilteredStrings($parsedFilters);

        return [
                'data' => $data['data'],
                'count' => $data['count'],
                'interpreted_query' => [
                    'original' => $query,
                    'parsed_filters' => $parsedFilters,
                ],
        ];
    }

    /**
     * Interpret common natural language queries into filters.
     *
     * @param string $query
     * @return array|null
     */
    protected function interpretNaturalLanguage(string $query): ?array
    {
        $query = strtolower(trim($query));
        $filters = [];

        // Examples of patterns (can expand this)
        if (str_contains($query, 'palindromic')) {
            $filters['is_palindrome'] = true;
        }

        if (str_contains($query, 'single word')) {
            $filters['word_count'] = 1;
        }

        if (preg_match('/longer than (\d+)/', $query, $match)) {
            $filters['min_length'] = (int) $match[1] + 1;
        }

        if (preg_match('/shorter than (\d+)/', $query, $match)) {
            $filters['max_length'] = (int) $match[1] - 1;
        }

        if (preg_match('/contain.*first vowel/', $query)) {
            // Heuristic — assume 'a' is the first vowel
            $filters['contains_character'] = 'a';
        }

        if (preg_match('/strings containing the letter (\w)/', $query, $match)) {
            $filters['contains_character'] = strtolower($match[1]);
        }

        return count($filters) ? $filters : null;
    }

    /**
     * Delete a string by its original value.
     *
     * @param string $value
     */
    public function deleteStringByValue(string $value): array
    {
        $hash = hash('sha256', $value);

        $record = StringAnalysis::where('sha256_hash', $hash)->first();

        if (!$record) {
            return [['message' => 'String does not exist in the system'], Http::NOT_FOUND];
        }

        $record->delete();

        return [['message' => 'String deleted successfully.'], Http::NO_CONTENT];
}



    /**
     * Format the output to match API response
     */
    protected function formatResponse(StringAnalysis $record): array
    {
        return [
            'id' => $record->sha256_hash,
            'value' => $record->value,
            'properties' => $record->properties,
            'created_at' => $record->created_at->toISOString(),
        ];
    }
}
