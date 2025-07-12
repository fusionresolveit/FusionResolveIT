<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Blacklist
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'value' => pgettext('blacklist', 'Value'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
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
    return [
      1 => [
        'title' => pgettext('network', 'IP'),
      ],
      2 => [
        'title' => pgettext('network', 'MAC'),
      ],
      3 => [
        'title' => pgettext('inventory device', 'Serial number'),
      ],
      4 => [
        'title' => pgettext('global', 'UUID'),
      ],
      5 => [
        'title' => npgettext('global', 'Email', 'Emails', 1),
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Blacklist', 'Blacklists', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
