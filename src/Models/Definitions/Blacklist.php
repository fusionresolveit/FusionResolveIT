<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Blacklist
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'value' => $translator->translate('Value'),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(11, $t['value'], 'input', 'value', fillable: true));
    $defColl->add(new Def(12, $t['type'], 'dropdown', 'type', values: self::getTypeArray(), fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getTypeArray(): array
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

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
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
