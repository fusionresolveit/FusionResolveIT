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
    global $translator;

    $t = [
      'serial' => $translator->translate('Serial number'),
      'otherserial' => $translator->translate('Inventory number'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'state' => $translator->translate('Status'),
      'pin' => $translator->translate('PIN code'),
      'pin2' => $translator->translate('PIN2 code'),
      'puk' => $translator->translate('PUK code'),
      'puk2' => $translator->translate('PUK2 code'),
      'msin' => $translator->translate('Mobile Subscriber Identification Number'),
      'user' => $translator->translatePlural('User', 'Users', 1),
      'group' => $translator->translatePlural('Group', 'Groups', 1),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Simcard', 'Simcards', 1),
        'icon' => 'home',
        'link' => $rootUrl,
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
        'title' => $translator->translatePlural('Contract', 'Contract', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
