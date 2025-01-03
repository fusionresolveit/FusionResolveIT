<?php

declare(strict_types=1);

namespace App\Models\Definitions;

class Profileright
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
