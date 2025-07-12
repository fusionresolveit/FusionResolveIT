<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Devicebattery
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'manufacturer' => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'capacity' => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('battery', 'Capacity'),
        pgettext('battery', 'mWh')
      ),
      'voltage' => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('battery', 'Voltage'),
        pgettext('battery', 'mV')
      ),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer',
      fillable: true
    ));
    $defColl->add(new Def(
      13,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'devicebatterytype_id',
      itemtype: '\App\Models\Devicebatterytype',
      fillable: true
    ));
    $defColl->add(new Def(11, $t['capacity'], 'input', 'capacity', fillable: true));
    $defColl->add(new Def(12, $t['voltage'], 'input', 'voltage', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

     return $defColl;
    // [
    // 'id'    => 80,
    // 'title' => npgettext('global', 'Entity', 'Entities', 1),
    // 'type'  => 'dropdown_remote',
    // 'name'  => 'completename',
    // 'itemtype' => '\App\Models\Entity',
    // ],
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Battery', 'Batteries', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
