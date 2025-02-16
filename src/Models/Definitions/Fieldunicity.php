<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Fieldunicity
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'is_active' => $translator->translate('Active'),
      'item_type' => $translator->translatePlural('Type', 'Types', 1),
      'action_refuse' => $translator->translate('Record into the database denied'),
      'action_notify' => $translator->translate('Send a notification'),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;

    $types = [];
    $types['Budget'] = $translator->translatePlural('Budget', 'Budgets', 1);
    $types['Computer'] = $translator->translatePlural('Computer', 'Computers', 1);
    $types['Contact'] = $translator->translatePlural('Contact', 'Contacts', 1);
    $types['Contract'] = $translator->translatePlural('Contract', 'Contracts', 1);
    $types['Monitor'] = $translator->translatePlural('Monitor', 'Monitors', 1);
    $types['Networkequipment'] = $translator->translatePlural('Network device', 'Network devices', 1);
    $types['Peripheral'] = $translator->translatePlural('Device', 'Devices', 1);
    $types['Infocom'] = $translator->translate('Financial and administrative information');
    $types['Phone'] = $translator->translatePlural('Phone', 'Phones', 1);
    $types['Printer'] = $translator->translatePlural('Printer', 'Printers', 1);
    $types['Software'] = $translator->translatePlural('Software', 'Software', 1);
    $types['Supplier'] = $translator->translatePlural('Supplier', 'Suppliers', 1);
    $types['Rack'] = $translator->translatePlural('Rack', 'Racks', 1);
    $types['Enclosure'] = $translator->translatePlural('Enclosure', 'Enclosures', 1);
    $types['PDU'] = $translator->translatePlural('PDU', 'PDUs', 1);
    $types['SoftwareLicense'] = $translator->translatePlural('License', 'Licenses', 1);
    $types['Cluster'] = $translator->translatePlural('Cluster', 'Clusters', 1);
    $types['User'] = $translator->translatePlural('User', 'Users', 1);
    $types['ItemDeviceSimcard'] = $translator->translatePlural('Simcard', 'Simcards', 1);
    $types['Certificate'] = $translator->translatePlural('Certificate', 'Certificates', 1);

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
    global $translator;
    return [
      [
        'title' => $translator->translate('Fields unicity'),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Duplicates'),
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
