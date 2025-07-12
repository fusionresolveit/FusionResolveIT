<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;
use App\Traits\Definitions\ItemOperatingsystem;

class Computer
{
  use Infocoms;
  use ItemOperatingsystem;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'             => pgettext('global', 'Name'),
      'state'            => pgettext('inventory device', 'Status'),
      'type'             => npgettext('global', 'Type', 'Types', 1),
      'user'             => npgettext('global', 'User', 'Users', 1),
      'manufacturer'     => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'model'            => npgettext('global', 'Model', 'Models', 1),
      'serial'           => pgettext('inventory device', 'Serial number'),
      'otherserial'      => pgettext('inventory device', 'Inventory number'),
      'location'         => npgettext('global', 'Location', 'Locations', 1),
      'usertech'         => pgettext('inventory device', 'Technician in charge of the hardware'),
      'grouptech'        => pgettext('inventory device', 'Group in charge of the hardware'),
      'contact'          => pgettext('inventory device', 'Alternate username'),
      'contact_num'      => pgettext('inventory device', 'Alternate username number'),
      'group'            => npgettext('global', 'Group', 'Groups', 1),
      'network'          => npgettext('inventory device', 'Network', 'Networks', 1),
      'uuid'             => pgettext('global', 'UUID'),
      'autoupdatesystem' => npgettext('inventory device', 'Update Source', 'Update Sources', 1),
      'comment'          => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at'       => pgettext('global', 'Last update'),
      'created_at'       => pgettext('global', 'Creation date'),
      'firmware'         => npgettext('global', 'Firmware', 'Firmware', 1),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['name'], 'input', 'name', fillable: true));
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
      dbname: 'computertype_id',
      itemtype: '\App\Models\Computertype',
      fillable: true
    ));
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
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer',
      fillable: true
    ));
    $defColl->add(new Def(
      40,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'computermodel_id',
      itemtype: '\App\Models\Computermodel',
      fillable: true
    ));
    $defColl->add(new Def(
      1001,
      $t['firmware'],
      'dropdown_remote',
      'firmware',
      dbname: 'firmware_id',
      itemtype: '\App\Models\Firmware',
      fillable: true,
    ));
    $defColl->add(new Def(5, $t['serial'], 'input', 'serial', fillable: true));
    $defColl->add(new Def(6, $t['otherserial'], 'input', 'otherserial', fillable: true));
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
    $defColl->add(new Def(7, $t['contact'], 'input', 'contact', fillable: true));
    $defColl->add(new Def(8, $t['contact_num'], 'input', 'contact_num', fillable: true));
    $defColl->add(new Def(
      71,
      $t['group'],
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(
      32,
      $t['network'],
      'dropdown_remote',
      'network',
      dbname: 'network_id',
      itemtype: '\App\Models\Network',
      fillable: true
    ));
    $defColl->add(new Def(47, $t['uuid'], 'input', 'uuid', fillable: true));
    $defColl->add(new Def(
      42,
      $t['autoupdatesystem'],
      'dropdown_remote',
      'autoupdatesystem',
      dbname: 'autoupdatesystem_id',
      itemtype: '\App\Models\Autoupdatesystem',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
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
        'title' => npgettext('global', 'Computer', 'Computers', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('inventory device', 'Impact analysis'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('inventory device', 'Operating System', 'Operating Systems', 1),
        'icon' => 'laptop house',
        'link' => $rootUrl . '/operatingsystem',
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
        'title' => npgettext('global', 'Software', 'Software', 2),
        'icon' => 'cube',
        'link' => $rootUrl . '/softwares',
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
        'title' => pgettext('inventory device', 'Virtualization'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/virtualization',
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
        'title' => npgettext('global', 'Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => npgettext('inventory device', 'Lock', 'Locks', 2),
        'icon' => 'caret square down outline',
        'link' => '',
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
        'title' => pgettext('inventory device', 'Import information'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
