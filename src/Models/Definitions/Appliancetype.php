<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Appliancetype
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'      => pgettext('global', 'Name'),
      'comment'   => npgettext('global', 'Comment', 'Comments', 2),
      'entity' => npgettext('global', 'Entity', 'Entities', 1),
      'recursive' => pgettext('global', 'Child entities'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(6, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      80,
      $t['entity'],
      'dropdown_remote',
      'entity',
      dbname: 'entity_id',
      itemtype: '\App\Models\Entity',
      display: false,
      relationfields: [
        'id',
        'name',
        'completename',
        'address',
        'country',
        'email',
        'fax',
        'phonenumber',
        'postcode',
        'state',
        'town',
        'website',
      ]
    ));
    $defColl->add(new Def(86, $t['recursive'], 'boolean', 'is_recursive', fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Appliance type', 'Appliance types', 1),
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
