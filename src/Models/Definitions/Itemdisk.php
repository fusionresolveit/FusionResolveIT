<?php

namespace App\Models\Definitions;

class Itemdisk
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
}
