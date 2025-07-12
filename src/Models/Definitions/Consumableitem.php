<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Consumableitem
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'ref' => pgettext('inventory device', 'Reference'),
      'otherserial' => pgettext('inventory device', 'Inventory number'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'manufacturer' => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'location' => npgettext('global', 'Location', 'Locations', 1),
      'usertech' => pgettext('inventory device', 'Technician in charge of the hardware'),
      'grouptech' => pgettext('inventory device', 'Group in charge of the hardware'),
      'alarm_threshold' => pgettext('inventory device', 'Alert threshold'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(34, $t['ref'], 'input', 'ref', fillable: true));
    $defColl->add(new Def(6, $t['otherserial'], 'input', 'otherserial', autocomplete: true, fillable: true));
    $defColl->add(new Def(
      4,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'consumableitemtype_id',
      itemtype: '\App\Models\Consumableitemtype',
      fillable: true
    ));
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
      3,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));
    $defColl->add(new Def(
      24,
      $t['usertech'],
      'dropdown_remote',
      'usertech',
      dbname: 'user_id_tech',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      49,
      $t['grouptech'],
      'dropdown_remote',
      'grouptech',
      dbname: 'group_id_tech',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(
      8,
      $t['alarm_threshold'],
      'dropdown',
      'alarm_threshold',
      dbname: 'alarm_threshold',
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 100, 1, ['-1' => pgettext('contract renewal', 'Never')]),
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));

    return $defColl;

    // [
    //    'id'    => 80,
    //    'title' => npgettext('global', 'Entity', 'Entities', 1),
    //    'type'  => 'dropdown_remote',
    //    'name'  => 'completename',
    //    'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $tab[] = [
      'id'                 => '9',
      'table'              => $this->getTable(),
      'field'              => '_virtual',
      'linkfield'          => '_virtual',
      'name'               => _n('Consumable', 'Consumables', Session::getPluralNumber()),
      'datatype'           => 'specific',
      'massiveaction'      => false,
      'nosearch'           => true,
      'nosort'             => true,
      'additionalfields'   => ['alarm_threshold']
    ];

    $tab[] = [
      'id'                 => '17',
      'table'              => 'glpi_consumables',
      'field'              => 'id',
      'name'               => __('Number of used consumables'),
      'datatype'           => 'count',
      'forcegroupby'       => true,
      'usehaving'          => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => 'AND NEWTABLE.`date_out` IS NOT NULL'
      ]
    ];

    $tab[] = [
      'id'                 => '19',
      'table'              => 'glpi_consumables',
      'field'              => 'id',
      'name'               => __('Number of new consumables'),
      'datatype'           => 'count',
      'forcegroupby'       => true,
      'usehaving'          => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => 'AND NEWTABLE.`date_out` IS NULL'
      ]
    ];

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Consumable model', 'Consumable models', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Consumable', 'Consumables', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/consumables',
      ],
      [
        'title' => pgettext('global', 'Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('global', 'External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => npgettext('global', 'Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
