<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class ItemSoftwareversion
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'id' => $translator->translate('ID'),
      'date_install' => $translator->translate('Installation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false));
    $defColl->add(new Def(1001, $t['date_install'], 'input', 'date_install', display: false, fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [];
  }
}
