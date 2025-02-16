<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class State
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'state' => $translator->translate('As child of'),
      'is_visible_computer' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Computer', 'Computers', 2)
      ),
      'is_visible_monitor' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Monitor', 'Monitors', 2)
      ),
      'is_visible_networkequipment' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Network device', 'Network devices', 2)
      ),
      'is_visible_peripheral' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Device', 'Devices', 2)
      ),
      'is_visible_phone' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Phone', 'Phones', 2)
      ),
      'is_visible_printer' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Printer', 'Printers', 2)
      ),
      'is_visible_softwarelicense' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('License', 'Licenses', 2)
      ),
      'is_visible_certificate' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Certificate', 'Certificates', 2)
      ),
      'is_visible_enclosure' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Enclosure', 'Enclosures', 2)
      ),
      'is_visible_pdu' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('PDU', 'PDUs', 1)
      ),
      'is_visible_line' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Line', 'Lines', 2)
      ),
      'is_visible_rack' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Rack', 'Racks', 2)
      ),
      'is_visible_softwareversion' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Version', 'Versions', 2)
      ),
      'is_visible_cluster' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Cluster', 'Clusters', 2)
      ),
      'is_visible_contract' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Contract', 'Contract', 2)
      ),
      'is_visible_appliance' => sprintf(
        $translator->translate('%1$s - %2$s'),
        $translator->translate('Visibility'),
        $translator->translatePlural('Appliance', 'Appliances', 2)
      ),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Status of items', 'Statuses of items', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Status of items', 'Statuses of items', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/states',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
