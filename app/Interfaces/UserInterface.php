<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface UserInterface
{
      public function getUserWithFact(): array;

}
