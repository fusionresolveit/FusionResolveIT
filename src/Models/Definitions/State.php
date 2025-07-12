<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class State
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'state' => pgettext('global', 'As child of'),
      'is_visible_computer' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Computer', 'Computers', 2)
      ),
      'is_visible_monitor' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('inventory device', 'Monitor', 'Monitors', 2)
      ),
      'is_visible_networkequipment' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Network device', 'Network devices', 2)
      ),
      'is_visible_peripheral' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Peripheral', 'Peripherals', 2)
      ),
      'is_visible_phone' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Phone', 'Phones', 2)
      ),
      'is_visible_printer' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Printer', 'Printers', 2)
      ),
      'is_visible_softwarelicense' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'License', 'Licenses', 2)
      ),
      'is_visible_certificate' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Certificate', 'Certificates', 2)
      ),
      'is_visible_enclosure' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Enclosure', 'Enclosures', 2)
      ),
      'is_visible_pdu' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'PDU', 'PDUs', 1)
      ),
      'is_visible_line' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Line', 'Lines', 2)
      ),
      'is_visible_rack' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Rack', 'Racks', 2)
      ),
      'is_visible_softwareversion' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Version', 'Versions', 2)
      ),
      'is_visible_cluster' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Cluster', 'Clusters', 2)
      ),
      'is_visible_contract' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Contract', 'Contracts', 2)
      ),
      'is_visible_appliance' => sprintf(
        pgettext('global', '%1$s - %2$s'),
        pgettext('global', 'Visibility'),
        npgettext('global', 'Appliance', 'Appliances', 2)
      ),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(14, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      13,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true
    ));
    $defColl->add(new Def(21, $t['is_visible_computer'], 'boolean', 'is_visible_computer', fillable: true));
    $defColl->add(new Def(23, $t['is_visible_monitor'], 'boolean', 'is_visible_monitor', fillable: true));
    $defColl->add(new Def(
      27,
      $t['is_visible_networkequipment'],
      'boolean',
      'is_visible_networkequipment',
      fillable: true
    ));
    $defColl->add(new Def(25, $t['is_visible_peripheral'], 'boolean', 'is_visible_peripheral', fillable: true));
    $defColl->add(new Def(26, $t['is_visible_phone'], 'boolean', 'is_visible_phone', fillable: true));
    $defColl->add(new Def(24, $t['is_visible_printer'], 'boolean', 'is_visible_printer', fillable: true));
    $defColl->add(new Def(
      28,
      $t['is_visible_softwarelicense'],
      'boolean',
      'is_visible_softwarelicense',
      fillable: true
    ));
    $defColl->add(new Def(29, $t['is_visible_certificate'], 'boolean', 'is_visible_certificate', fillable: true));
    $defColl->add(new Def(32, $t['is_visible_enclosure'], 'boolean', 'is_visible_enclosure', fillable: true));
    $defColl->add(new Def(33, $t['is_visible_pdu'], 'boolean', 'is_visible_pdu', fillable: true));
    $defColl->add(new Def(31, $t['is_visible_line'], 'boolean', 'is_visible_line', fillable: true));
    $defColl->add(new Def(30, $t['is_visible_rack'], 'boolean', 'is_visible_rack', fillable: true));
    $defColl->add(new Def(
      22,
      $t['is_visible_softwareversion'],
      'boolean',
      'is_visible_softwareversion',
      fillable: true
    ));
    $defColl->add(new Def(34, $t['is_visible_cluster'], 'boolean', 'is_visible_cluster', fillable: true));
    $defColl->add(new Def(36, $t['is_visible_contract'], 'boolean', 'is_visible_contract', fillable: true));
    $defColl->add(new Def(37, $t['is_visible_appliance'], 'boolean', 'is_visible_appliance', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*

    $tab = [];

    $tab[] = [
        'id'   => 'common',
        'name' => __('Characteristics')
    ];

    $tab[] = [
        'id'                => '1',
        'table'              => $this->getTable(),
        'field'              => 'completename',
        'name'               => __('Complete name'),
        'datatype'           => 'itemlink',
        'massiveaction'      => false
    ];

    $tab[] = [
        'id'                => '2',
        'table'              => $this->getTable(),
        'field'              => 'id',
        'name'               => __('ID'),
        'massiveaction'      => false,
        'datatype'           => 'number'
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));






    $tab[] = [
        'id'                 => '35',
        'table'              => $this->getTable(),
        'field'              => 'is_visible_passivedcequipment',
        'name'               => sprintf(__('%1$s - %2$s'), __('Visibility'),
                                    PassiveDCEquipment::getTypeName(Session::getPluralNumber())),
        'datatype'           => 'bool'
    ];



    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Status of items', 'Statuses of items', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Status of items', 'Statuses of items', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/states',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
