<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Memorymodule
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'manufacturer' => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'size' => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('global', 'Size'),
        pgettext('global', 'Mio')
      ),
      'frequence' => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('global', 'Frequency'),
        pgettext('global', 'MHz')
      ),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'model' => npgettext('global', 'Model', 'Models', 1),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
      'serial' => pgettext('inventory device', 'Serial number'),
      'otherserial' => pgettext('inventory device', 'Inventory number'),
      'location' => npgettext('global', 'Location', 'Locations', 1),
      'state' => pgettext('inventory device', 'Status'),
      'memoryslot' => npgettext('memory device', 'Memory slot', 'Memory slots', 1),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(11, $t['size'], 'input', 'size', fillable: true));
    $defColl->add(new Def(12, $t['frequence'], 'input', 'frequence', fillable: true));
    $defColl->add(new Def(
      1005,
      $t['memoryslot'],
      'dropdown_remote',
      'memoryslot',
      dbname: 'memoryslot_id',
      itemtype: '\App\Models\Memoryslot'
    ));
    $defColl->add(new Def(
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer'
    ));
    $defColl->add(new Def(
      13,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'memorytype_id',
      itemtype: '\App\Models\Memorytype',
      fillable: true
    ));
    $defColl->add(new Def(
      14,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'memorymodel_id',
      itemtype: '\App\Models\Memorymodel',
      fillable: true
    ));
    $defColl->add(new Def(1001, $t['serial'], 'input', 'serial', fillable: true));
    $defColl->add(new Def(1002, $t['otherserial'], 'input', 'otherserial', fillable: true));
    $defColl->add(new Def(
      1003,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true
    ));
    $defColl->add(new Def(
      1004,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));

    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    // $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Memory', 'Memory', 1),
        'icon' => 'home',
        'link' => $rootUrl,
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
