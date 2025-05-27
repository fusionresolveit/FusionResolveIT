<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Memoryslot
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'slotnumber' => $translator->translate('Slot number'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1001, $t['slotnumber'], 'input', 'slotnumber', fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [];
  }
}
