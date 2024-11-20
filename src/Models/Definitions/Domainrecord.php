<?php

namespace App\Models\Definitions;

class Domainrecord
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
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [];
  }
}
