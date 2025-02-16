<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;
use App\Traits\Definitions\ItemOperatingsystem;

class Peripheral
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
      'brand' => $translator->translate('Brand'),
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
      dbname: 'peripheraltype_id',
      itemtype: '\App\Models\Networkequipmenttype',
      fillable: true
    ));
    $defColl->add(new Def(
      40,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'peripheralmodel_id',
      itemtype: '\App\Models\Networkequipmentmodel',
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
      'id'                 => '82',
      'table'              => $this->getTable(),
      'field'              => 'is_global',
      'name'               => __('Global management'),
      'datatype'           => 'bool',
      'massiveaction'      => false
    ];

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

    $tab = array_merge($tab, Datacenter::rawSearchOptionsToAdd(get_class($this)));
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
        'title' => $translator->translatePlural('Device', 'Devices', 1),
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
        'title' => $translator->translatePlural('Component', 'Components', 2),
        'icon' => 'microchip',
        'link' => $rootUrl . '/components',
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
        'title' => $translator->translate('Information d\'import'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
