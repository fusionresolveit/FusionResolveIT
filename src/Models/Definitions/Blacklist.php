<?php

declare(strict_types=1);

namespace App\Models\Definitions;

class Blacklist
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 1,
        'title' => $translator->translate('Name'),
        'type'  => 'input',
        'name'  => 'name',
        'fillable' => true,
      ],
      [
        'id'    => 11,
        'title' => $translator->translate('Value'),
        'type'  => 'input',
        'name'  => 'value',
        'fillable' => true,
      ],
      [
        'id'    => 12,
        'title' => $translator->translatePlural('Type', 'Types', 1),
        'type'  => 'dropdown',
        'name'  => 'type',
        'dbname'  => 'type',
        'values' => self::getTypeArray(),
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
        'fillable' => true,
      ],
      [
        'id'    => 19,
        'title' => $translator->translate('Last update'),
        'type'  => 'datetime',
        'name'  => 'updated_at',
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 121,
        'title' => $translator->translate('Creation date'),
        'type'  => 'datetime',
        'name'  => 'created_at',
        'readonly'  => 'readonly',
      ],
    ];
  }

  public static function getTypeArray()
  {
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('IP'),
      ],
      2 => [
        'title' => $translator->translate('MAC'),
      ],
      3 => [
        'title' => $translator->translate('Serial number'),
      ],
      4 => [
        'title' => $translator->translate('UUID'),
      ],
      5 => [
        'title' => $translator->translatePlural('Email', 'Emails', 1),
      ],
    ];
  }

  public static function getRelatedPages($rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Blacklist', 'Blacklists', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
