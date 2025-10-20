<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\StringAnalysis;

interface StringAnalysisInterface
{
      public function analyzeString(string $value): array;
      public function getStringByValue(string $value): array;
      public function filterByNaturalLanguage(string $query): array;
      public function getFilteredStrings(array $filters): array;
      public function deleteStringByValue(string $value): array;

}
