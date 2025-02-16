<?php

declare(strict_types=1);

namespace App\v1\Controllers\Datastructures;

trait Translation
{
  public function addTranslation(string $key, string $value): void
  {
    $this->translation->{$key} = $value;
  }
}
