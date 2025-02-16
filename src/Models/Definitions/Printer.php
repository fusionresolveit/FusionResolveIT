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
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'state' => $translator->translate('Status'),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'model' => $translator->translatePlural('Model', 'Models', 1),
      'serial' => $translator->translate('Serial number'),
      'otherserial' => $translator->translate('Inventory number'),
      'contact' => $translator->translate('Alternate username'),
      'contact_num' => $translator->translate('Alternate username number'),
      'user' => $translator->translatePlural('User', 'Users', 1),
      'group' => $translator->translatePlural('Group', 'Groups', 1),
      'comment' => $translator->translate('Comments'),
      'have_serial' => $translator->translate('Serial'),
      'have_parallel' => $translator->translate('Parallel'),
      'have_usb' => $translator->translate('USB'),
      'have_ethernet' => $translator->translate('Ethernet'),
      'have_wifi' => $translator->translate('Wifi'),
      'memory_size' => $translator->translatePlural('Memory', 'Memories', 1),
      'init_pages_counter' => $translator->translate('Initial page counter'),
      'last_pages_counter' => $translator->translate('Current counter of pages'),
      'network' => $translator->translate('Network', 'Networks', 1),
      'manufacturer' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
      'usertech' => $translator->translate('Technician in charge of the hardware'),
      'grouptech' => $translator->translate('Group in charge of the hardware'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translate('Template name'),
    //   'type'  => 'input',
    //   'name'  => 'template_name',
    // ],
    // [
    //   'id'    => 80,
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Printer', 'Printers', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Analysis impact'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Operating system', 'Operating systems', 2),
        'icon' => 'laptop house',
        'link' => $rootUrl . '/operatingsystem',
      ],
      [
        'title' => $translator->translatePlural('Software', 'Softwares', 2),
        'icon' => 'cube',
        'link' => $rootUrl . '/softwares',
      ],
      [
        'title' => $translator->translatePlural('Cartridge', 'Cartridges', 2),
        'icon' => 'fill drip',
        'link' => $rootUrl . '/cartridges',
      ],
      [
        'title' => $translator->translatePlural('Component', 'Components', 2),
        'icon' => 'microchip',
        'link' => $rootUrl . '/components',
      ],
      [
        'title' => $translator->translatePlural('Volume', 'Volumes', 2),
        'icon' => 'hdd',
        'link' => $rootUrl . '/volumes',
      ],
      [
        'title' => $translator->translatePlural('Connection', 'Connections', 2),
        'icon' => 'microchip',
        'link' => $rootUrl . '/connections',
      ],
      [
        'title' => $translator->translatePlural('Network port', 'Network ports', 2),
        'icon' => 'ethernet',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => $translator->translatePlural('Contract', 'Contract', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translate('Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowbaseitems',
      ],
      [
        'title' => $translator->translate('ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
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
        'title' => $translator->translatePlural('Reservation', 'Reservations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/reservations',
      ],
      [
        'title' => $translator->translatePlural('Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => $translator->translatePlural('Domain', 'Domains', 2),
        'icon' => 'globe americas',
        'link' => $rootUrl . '/domains',
      ],
      [
        'title' => $translator->translatePlural('Appliance', 'Appliances', 2),
        'icon' => 'cubes',
        'link' => $rootUrl . '/appliances',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
