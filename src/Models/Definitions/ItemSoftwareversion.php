<?php

declare(strict_types=1);

namespace App\Models\Definitions;

class ItemSoftwareversion
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'      => 2,
        'title'   => $translator->translate('ID'),
        'type'    => 'input',
        'name'    => 'id',
        'display' => false,
      ],
      [
        'id'      => 1001,
        'title'   => $translator->translate('Installation date'),
        'type'    => 'input',
        'name'    => 'date_install',
        'display' => false,
        'fillable' => true,
      ],
    ];
  }

  public static function getRelatedPages($rootUrl): array
  {
    global $translator;
    return [];
  }
}
