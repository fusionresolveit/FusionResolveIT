<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;
use App\Traits\Definitions\ItemOperatingsystem;

class Printer
{
  use Infocoms;
  use ItemOperatingsystem;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'                => pgettext('global', 'Name'),
      'location'            => npgettext('global', 'Location', 'Locations', 1),
      'state'               => pgettext('inventory device', 'Status'),
      'type'                => npgettext('global', 'Type', 'Types', 1),
      'model'               => npgettext('global', 'Model', 'Models', 1),
      'serial'              => pgettext('inventory device', 'Serial number'),
      'otherserial'         => pgettext('inventory device', 'Inventory number'),
      'contact'             => pgettext('inventory device', 'Alternate username'),
      'contact_num'         => pgettext('inventory device', 'Alternate username number'),
      'user'                => npgettext('global', 'User', 'Users', 1),
      'group'               => npgettext('global', 'Group', 'Groups', 1),
      'comment'             => npgettext('global', 'Comment', 'Comments', 2),
      'have_serial'         => pgettext('printer', 'Serial port'),
      'have_parallel'       => pgettext('printer', 'Parallel port'),
      'have_usb'            => pgettext('printer', 'USB port'),
      'have_ethernet'       => pgettext('printer', 'Ethernet port'),
      'have_wifi'           => pgettext('printer', 'Wifi connection'),
      'memory_size'         => pgettext('inventory device', 'Memory size'),
      'init_pages_counter'  => pgettext('printer', 'Initial page counter'),
      'last_pages_counter'  => pgettext('printer', 'Current counter of pages'),
      'network'             => npgettext('inventory device', 'Network', 'Networks', 1),
      'manufacturer'        => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'usertech'            => pgettext('inventory device', 'Technician in charge of the hardware'),
      'grouptech'           => pgettext('inventory device', 'Group in charge of the hardware'),
      'updated_at'          => pgettext('global', 'Last update'),
      'created_at'          => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['name'], 'input', 'name', fillable: true));
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
      31,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true
    ));
    $defColl->add(new Def(
      4,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'printertype_id',
      itemtype: '\App\Models\Printertype',
      fillable: true
    ));
    $defColl->add(new Def(
      40,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'printermodel_id',
      itemtype: '\App\Models\Printermodel',
      fillable: true
    ));
    $defColl->add(new Def(5, $t['serial'], 'input', 'serial', autocomplete: true, fillable: true));
    $defColl->add(new Def(6, $t['otherserial'], 'input', 'otherserial', autocomplete: true, fillable: true));
    $defColl->add(new Def(7, $t['contact'], 'input', 'contact', fillable: true));
    $defColl->add(new Def(8, $t['contact_num'], 'input', 'contact_num', fillable: true));
    $defColl->add(new Def(
      70,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      71,
      $t['group'],
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(42, $t['have_serial'], 'boolean', 'have_serial', fillable: true));
    $defColl->add(new Def(43, $t['have_parallel'], 'boolean', 'have_parallel', fillable: true));
    $defColl->add(new Def(44, $t['have_usb'], 'boolean', 'have_usb', fillable: true));
    $defColl->add(new Def(45, $t['have_ethernet'], 'boolean', 'have_ethernet', fillable: true));
    $defColl->add(new Def(46, $t['have_wifi'], 'boolean', 'have_wifi', fillable: true));
    $defColl->add(new Def(13, $t['memory_size'], 'input', 'memory_size', fillable: true));
    $defColl->add(new Def(11, $t['init_pages_counter'], 'input', 'init_pages_counter', fillable: true));
    $defColl->add(new Def(12, $t['last_pages_counter'], 'input', 'last_pages_counter', fillable: true));
    $defColl->add(new Def(
      32,
      $t['network'],
      'dropdown_remote',
      'network',
      dbname: 'network_id',
      itemtype: '\App\Models\Network',
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
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 61,
    //   'title' => 'Template name',
    //   'type'  => 'input',
    //   'name'  => 'template_name',
    // ],
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $tab[] = [
      'id'                 => '9',
      'table'              => $this->getTable(),
      'field'              => '_virtual',
      'linkfield'          => '_virtual',
      'name'               => _n('Cartridge', 'Cartridges', Session::getPluralNumber()),
      'datatype'           => 'specific',
      'massiveaction'      => false,
      'nosearch'           => true,
      'nosort'             => true
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

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

    $tab = array_merge($tab, Item_Devices::rawSearchOptionsToAdd(get_class($this)));
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Printer', 'Printers', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('global', 'Analysis impact'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('inventory device', 'Operating System', 'Operating Systems', 2),
        'icon' => 'laptop house',
        'link' => $rootUrl . '/operatingsystem',
      ],
      [
        'title' => npgettext('global', 'Software', 'Software', 2),
        'icon' => 'cube',
        'link' => $rootUrl . '/softwares',
      ],
      [
        'title' => npgettext('global', 'Cartridge', 'Cartridges', 2),
        'icon' => 'fill drip',
        'link' => $rootUrl . '/cartridges',
      ],
      [
        'title' => npgettext('global', 'Component', 'Components', 2),
        'icon' => 'microchip',
        'link' => $rootUrl . '/components',
      ],
      [
        'title' => npgettext('inventory device', 'Volume', 'Volumes', 2),
        'icon' => 'hdd',
        'link' => $rootUrl . '/volumes',
      ],
      [
        'title' => npgettext('inventory device', 'Connection', 'Connections', 2),
        'icon' => 'microchip',
        'link' => $rootUrl . '/connections',
      ],
      [
        'title' => npgettext('inventory device', 'Network port', 'Network ports', 2),
        'icon' => 'ethernet',
        'link' => '',
      ],
      [
        'title' => pgettext('global', 'Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => npgettext('global', 'Contract', 'Contracts', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => pgettext('global', 'Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => pgettext('global', 'ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
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
        'title' => npgettext('global', 'Reservation', 'Reservations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/reservations',
      ],
      [
        'title' => npgettext('global', 'Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => npgettext('global', 'Domain', 'Domains', 2),
        'icon' => 'globe americas',
        'link' => $rootUrl . '/domains',
      ],
      [
        'title' => npgettext('global', 'Appliance', 'Appliances', 2),
        'icon' => 'cubes',
        'link' => $rootUrl . '/appliances',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
