<?php

declare(strict_types=1);

namespace App\Interfaces;

interface SayItNicerInterface
{
      public function rephrase(string $text): string;

}
