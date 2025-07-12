<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Fieldunicity
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'is_active' => pgettext('global', 'Active'),
      'item_type' =>  npgettext('global', 'Type', 'Types', 1),
      'action_refuse' => pgettext('global', 'Record into the database denied'),
      'action_notify' => pgettext('global', 'Send a notification'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(30, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(
      4,
      $t['item_type'],
      'dropdown',
      'item_type',
      dbname: 'item_type',
      values: self::getTypeArray(),
      fillable: true
    ));
    $defColl->add(new Def(5, $t['action_refuse'], 'boolean', 'action_refuse', fillable: true));
    $defColl->add(new Def(6, $t['action_notify'], 'boolean', 'action_notify', fillable: true));
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
    $tab[] = [
      'id'                 => '3',
      'table'              => $this->getTable(),
      'field'              => 'fields',
      'name'               => __('Unique fields'),
      'massiveaction'      => false,
      'datatype'           => 'specific',
      'additionalfields'   => ['itemtype']
    ];
    */
  }

  /**
   * @return array<string, mixed>
   */
  public static function getTypeArray(): array
  {
    $types = [];
    $types['Budget'] = npgettext('global', 'Budget', 'Budgets', 1);
    $types['Computer'] = npgettext('global', 'Computer', 'Computers', 1);
    $types['Contact'] = npgettext('global', 'Contact', 'Contacts', 1);
    $types['Contract'] = npgettext('global', 'Contract', 'Contracts', 1);
    $types['Monitor'] = npgettext('inventory device', 'Monitor', 'Monitors', 1);
    $types['Networkequipment'] = npgettext('global', 'Network device', 'Network devices', 1);
    $types['Peripheral'] = npgettext('global', 'Peripheral', 'Peripherals', 1);
    $types['Infocom'] = pgettext('global', 'Financial and administrative information');
    $types['Phone'] = npgettext('global', 'Phone', 'Phones', 1);
    $types['Printer'] = npgettext('global', 'Printer', 'Printers', 1);
    $types['Software'] = npgettext('global', 'Software', 'Software', 1);
    $types['Supplier'] = npgettext('global', 'Supplier', 'Suppliers', 1);
    $types['Rack'] = npgettext('global', 'Rack', 'Racks', 1);
    $types['Enclosure'] = npgettext('global', 'Enclosure', 'Enclosures', 1);
    $types['PDU'] = npgettext('global', 'PDU', 'PDUs', 1);
    $types['SoftwareLicense'] = npgettext('global', 'License', 'Licenses', 1);
    $types['Cluster'] = npgettext('global', 'Cluster', 'Clusters', 1);
    $types['User'] = npgettext('global', 'User', 'Users', 1);
    $types['ItemDeviceSimcard'] = npgettext('global', 'SIM card', 'SIM cards', 1);
    $types['Certificate'] = npgettext('global', 'Certificate', 'Certificates', 1);

    asort($types);

    $newTypes = [];
    foreach (array_keys($types) as $key)
    {
      $newTypes[$key]['title'] = $types[$key];
    }

    return $newTypes;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Fields unicity', 'Fields unicity', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('global', 'Duplicates'),
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
