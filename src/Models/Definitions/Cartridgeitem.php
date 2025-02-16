<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Cartridgeitem
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'ref' => $translator->translate('Reference'),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'manufacturer' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'usertech' => $translator->translate('Technician in charge of the hardware'),
      'grouptech' => $translator->translate('Group in charge of the hardware'),
      'alarm_threshold' => $translator->translate('Alert threshold'),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(34, $t['ref'], 'input', 'ref', fillable: true));
    $defColl->add(new Def(
      4,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'cartridgeitemtype_id',
      itemtype: '\App\Models\Cartridgeitemtype',
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
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 100, 1, [-1 => $translator->translate('Never')]),
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));

    return $defColl;

    // [
    //    'id'    => 80,
    //    'title' => $translator->translatePlural('Entity', 'Entities', 1),
    //    'type'  => 'dropdown_remote',
    //    'name'  => 'completename',
    //    'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $tab[] = [
      'id'                 => '9',
      'table'              => $this->getTable(),
      'field'              => '_virtual',
      'name'               => _n('Cartridge', 'Cartridges', Session::getPluralNumber()),
      'datatype'           => 'specific',
      'massiveaction'      => false,
      'nosearch'           => true,
      'nosort'             => true,
      'additionalfields'   => ['alarm_threshold']
    ];

    $tab[] = [
      'id'                 => '17',
      'table'              => 'glpi_cartridges',
      'field'              => 'id',
      'name'               => __('Number of used cartridges'),
      'datatype'           => 'count',
      'forcegroupby'       => true,
      'usehaving'          => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => 'AND NEWTABLE.`date_use` IS NOT NULL
      AND NEWTABLE.`date_out` IS NULL'
      ]
    ];

    $tab[] = [
      'id'                 => '18',
      'table'              => 'glpi_cartridges',
      'field'              => 'id',
      'name'               => __('Number of worn cartridges'),
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
      'table'              => 'glpi_cartridges',
      'field'              => 'id',
      'name'               => __('Number of new cartridges'),
      'datatype'           => 'count',
      'forcegroupby'       => true,
      'usehaving'          => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => 'AND NEWTABLE.`date_use` IS NULL
      AND NEWTABLE.`date_out` IS NULL'
      ]
    ];

    $tab[] = [
      'id'                 => '40',
      'table'              => 'glpi_printermodels',
      'field'              => 'name',
      'datatype'           => 'dropdown',
      'name'               => _n('Printer model', 'Printer models', Session::getPluralNumber()),
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'beforejoin'         => [
      'table'              => 'glpi_cartridgeitems_printermodels',
      'joinparams'         => [
      'jointype'           => 'child'
      ]
      ]
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Cartridge model', 'Cartridge models', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Cartridge', 'Cartridges', 2),
        'icon' => 'fill drip',
        'link' => $rootUrl . '/cartridges',
      ],
      [
        'title' => $translator->translatePlural('Printer model', 'Printer models', 2),
        'icon' => 'print',
        'link' => $rootUrl . '/printermodels',
      ],
      [
        'title' => $translator->translate('Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
