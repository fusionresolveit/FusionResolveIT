<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\DefinitionCollection;

class Infocom
{
  use \App\Traits\Definitions\Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    return self::getDefinitionInfocom();
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [];
  }
}
