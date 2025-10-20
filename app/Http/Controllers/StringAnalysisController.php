<?php

namespace App\Http\Controllers;

use App\Enums\Http;
use App\Http\Requests\CreateStringRequest;
use App\Http\Requests\FilterStringsRequest;
use App\Http\Requests\NaturalLanguageFilterRequest;
use App\Interfaces\StringAnalysisInterface;
use App\Responses\SuccessResponse;

/**
 * @group String Analysis
 *
 * APIs for analyzing, filtering, and retrieving strings.
 */
class StringAnalysisController extends Controller
{
    public function __construct(
        private readonly StringAnalysisInterface $stringService,
    ){}

    /**
     * Get Filtered Strings
     *
     * Retrieve strings filtered by optional criteria.
     * @queryParam is_palindrome boolean Optional. Filter strings that are palindromes.
     * @queryParam min_length integer Optional. Minimum string length. Must be 0 or more.
     * @queryParam max_length integer Optional. Maximum string length. Must be 1 or more.
     * @queryParam word_count integer Optional. Exact word count to filter strings.
     * @queryParam contains_character string Optional. Filter strings that contain this character. Must be exactly one character. Example: h
     *
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": "9b74c9897bac770ffc029102a200c5de",
     *       "value": "example string",
     *       "properties": {
     *          "length": 15,
     *          "is_palindrome": false,
     *          "unique_characters": 12,
     *          "word_count": 2,
     *          ..............
     *       },
     *       "created_at": "2025-10-20T15:03:01.000000Z"
     *     }
     *   ],
     *   "count": 1,
     *   "filters_applied": {
     *     "min_length": 5
     *   }
     * }
     */
    public function index(FilterStringsRequest $request)
    {
        return SuccessResponse::make( 
            $this->stringService->getFilteredStrings($request->validated())
        );
    }

    /**
     * Store a new string
     *
     * Submit a string to be analyzed and stored.
     * If the string already exists, returns a 409 Conflict error.
     *
     * @bodyParam value string required The string to be analyzed and stored. max length 255 characters. Example: "Hello World"
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": "9b74c9897bac770ffc029102a200c5de",
     *     "value": "this is a test.",
     *     "properties": {
     *       "length": 15,
     *       "is_palindrome": false,
     *       "unique_characters": 12,
     *       "word_count": 4,
     *       "sha256_hash": "9b74c9897bac770ffc029102a200c5de",
     *       "character_frequency_map": {}
     *     },
     *     "created_at": "2025-10-20T15:03:01.000000Z"
     *   }
     * }
     *
     * @response 409 {
     *   "success": false,
     *   "message": "String already exists in the system"
     * }
     */
    public function store(CreateStringRequest $request)
    {
        return SuccessResponse::make(
            $this->stringService->analyzeString($request->validated()['value']),
            Http::CREATED
        );
    }

    /**
     * Natural Language Filter
     *
     * Filter strings using a natural language query.
     * Returns interpreted query details and matched strings.
     *
     * @queryParam query string required The natural language query string. Minimum 3 characters.
     *   Example: "strings containing the letter b"
     * 
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": "1c4a10f9a1d9db23c1231b3fa0d6e25a",
     *       "value": "madam",
     *       "properties": {
     *          "length": 15,
     *          "is_palindrome": false,
     *          "unique_characters": 12,
     *          "word_count": 1,
     *          ..............
     *       },
     *       "created_at": "2025-10-18T12:00:00Z"
     *     }
     *   ],
     *   "count": 1,
     *   "interpreted_query": {
     *     "original": "strings longer than 5 characters",
     *     "parsed_filters": {
     *       "is_palindrome": true,
     *       "min_length": 6
     *     }
     *   }
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Unable to parse natural language query"
     * }
     */
    public function filterByNaturalLanguage(NaturalLanguageFilterRequest $request)
    {
        return SuccessResponse::make( 
            $this->stringService->filterByNaturalLanguage($request->query('query'))
        );
    }

    /**
     * Get a String by Value
     *
     * Retrieve a string record by its exact value.
     *
     * @urlParam string_value string required The exact string value to find. Example: "this is a test."
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": "9b74c9897bac770ffc029102a200c5de",
     *     "value": "this is a test.",
     *     "properties": {
     *       "length": 15,
     *       "is_palindrome": false,
     *       "unique_characters": 12,
     *       "word_count": 4,
     *       ..............
     *     },
     *     "created_at": "2025-10-20T15:03:01.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "String does not exists in the system"
     * }
     */
    public function show($stringValue)
    {
        return SuccessResponse::make( 
            $this->stringService->getStringByValue($stringValue)
        );
    }
    
    /**
     * Delete a String by Value
     *
     * Deletes a string record identified by its exact value.
     *
     * @urlParam string_value string required The exact string value to delete. Example: "this is a test."
     *
     * @response 204 { No Content }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "String does not exist in the system."
     * }
     */
    public function destroy(string $stringValue)
    {
        return SuccessResponse::make(
            $this->stringService->deleteStringByValue($stringValue)
        );
    }

}
