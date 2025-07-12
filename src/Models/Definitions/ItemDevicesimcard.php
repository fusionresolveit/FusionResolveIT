<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class ItemDevicesimcard
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'serial' => pgettext('inventory device', 'Serial number'),
      'otherserial' => pgettext('inventory device', 'Inventory number'),
      'location' => npgettext('global', 'Location', 'Locations', 1),
      'state' => pgettext('inventory device', 'Status'),
      'pin' => pgettext('sim', 'PIN code'),
      'pin2' => pgettext('sim', 'PIN2 code'),
      'puk' => pgettext('sim', 'PUK code'),
      'puk2' => pgettext('sim', 'PUK2 code'),
      'msin' => pgettext('sim', 'Mobile Subscriber Identification Number'),
      'user' => npgettext('global', 'User', 'Users', 1),
      'group' => npgettext('global', 'Group', 'Groups', 1),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(10, $t['serial'], 'input', 'serial', fillable: true));
    $defColl->add(new Def(12, $t['otherserial'], 'input', 'otherserial', fillable: true));
    $defColl->add(new Def(
      13,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));
    $defColl->add(new Def(
      14,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true
    ));
    $defColl->add(new Def(15, $t['pin'], 'input', 'pin', fillable: true));
    $defColl->add(new Def(16, $t['pin2'], 'input', 'pin2', fillable: true));
    $defColl->add(new Def(17, $t['puk'], 'input', 'puk', fillable: true));
    $defColl->add(new Def(18, $t['puk2'], 'input', 'puk2', fillable: true));
    $defColl->add(new Def(20, $t['msin'], 'input', 'msin', fillable: true));
    $defColl->add(new Def(
      21,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      22,
      $t['group'],
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true
    ));

    return $defColl;
    /*
      'lines_id'        => ['long name'  => Line::getTypeName(1),
      'short name' => Line::getTypeName(1),
      'size'       => 20,
      'id'         => 19,
      'datatype'   => 'dropdown'],
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'SIM card', 'SIM cards', 1),
        'icon' => 'home',
        'link' => $rootUrl,
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
        'title' => npgettext('global', 'Contract', 'Contracts', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
