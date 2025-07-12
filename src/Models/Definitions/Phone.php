<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;
use App\Traits\Definitions\ItemOperatingsystem;

class Phone
{
  use Infocoms;
  use ItemOperatingsystem;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'location' => npgettext('global', 'Location', 'Locations', 1),
      'state' => pgettext('inventory device', 'Status'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'model' => npgettext('global', 'Model', 'Models', 1),
      'serial' => pgettext('inventory device', 'Serial number'),
      'otherserial' => pgettext('inventory device', 'Inventory number'),
      'contact' => pgettext('inventory device', 'Alternate username'),
      'contact_num' => pgettext('inventory device', 'Alternate username number'),
      'number_line' => pgettext('line', 'Number of lines'),
      'user' => npgettext('global', 'User', 'Users', 1),
      'group' => npgettext('global', 'Group', 'Groups', 1),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'brand' => pgettext('inventory device', 'Brand'),
      'manufacturer' => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'usertech' => pgettext('inventory device', 'Technician in charge of the hardware'),
      'grouptech' => pgettext('inventory device', 'Group in charge of the hardware'),
      'phonepowersupply' => npgettext('global', 'Power supply', 'Power supplies', 1),
      'have_headset' => pgettext('inventory device', 'Headset'),
      'have_hp' => pgettext('inventory device', 'Speaker'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
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
      dbname: 'phonetype_id',
      itemtype: '\App\Models\Phonetype',
      fillable: true
    ));
    $defColl->add(new Def(
      40,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'phonemodel_id',
      itemtype: '\App\Models\Phonemodel',
      fillable: true
    ));
    $defColl->add(new Def(5, $t['serial'], 'input', 'serial', autocomplete: true, fillable: true));
    $defColl->add(new Def(6, $t['otherserial'], 'input', 'otherserial', autocomplete: true, fillable: true));
    $defColl->add(new Def(7, $t['contact'], 'input', 'contact', fillable: true));
    $defColl->add(new Def(8, $t['contact_num'], 'input', 'contact_num', fillable: true));
    $defColl->add(new Def(9, $t['number_line'], 'input', 'number_line', fillable: true));
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
    $defColl->add(new Def(11, $t['brand'], 'input', 'brand', fillable: true));
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
    $defColl->add(new Def(
      42,
      $t['phonepowersupply'],
      'dropdown_remote',
      'phonepowersupply',
      dbname: 'phonepowersupply_id',
      itemtype: '\App\Models\Phonepowersupply',
      fillable: true
    ));
    $defColl->add(new Def(43, $t['have_headset'], 'boolean', 'have_headset', fillable: true));
    $defColl->add(new Def(44, $t['have_hp'], 'boolean', 'have_hp', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 61,
    //   'title' => pgettext('global', 'Template name'),
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
      'id'                 => '32',
      'table'              => 'glpi_devicefirmwares',
      'field'              => 'version',
      'name'               => _n('Firmware', 'Firmware', 1),
      'forcegroupby'       => true,
      'usehaving'          => true,
      'massiveaction'      => false,
      'datatype'           => 'dropdown',
      'joinparams'         => [
      'beforejoin'         => [
      'table'              => 'glpi_items_devicefirmwares',
      'joinparams'         => [
      'jointype'           => 'itemtype_item',
      'specific_itemtype'  => 'Phone'
      ]
      ]
      ]
    ];

    $tab[] = [
      'id'                 => '82',
      'table'              => $this->getTable(),
      'field'              => 'is_global',
      'name'               => __('Global management'),
      'datatype'           => 'bool',
      'massiveaction'      => false
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
        'title' => npgettext('global', 'Phone', 'Phones', 1),
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
        'icon' => 'linkify',
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
