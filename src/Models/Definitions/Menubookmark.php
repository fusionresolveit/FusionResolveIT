<?php

declare(strict_types=1);

namespace App\Models\Definitions;

class Menubookmark
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 2,
        'title' => $translator->translatePlural('User', 'Users', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'user',
        'dbname' => 'user_id',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Endpoint'),
        'type'  => 'input',
        'name'  => 'endpoint',
        'fillable' => true,
      ],
    ];
  }

  public static function getRelatedPages($rootUrl): array
  {
    global $translator;
    return [
    ];
  }
}
