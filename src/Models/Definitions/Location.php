<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Location
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'completename' => $translator->translate('Complete name'),
      'id' => $translator->translate('ID'),
      'location' => $translator->translate('As child of'),
      'address' => $translator->translate('Address'),
      'postcode' => $translator->translate('Postal code'),
      'town' => $translator->translate('Town'),
      'state' => $translator->translate('location' . "\004" . 'State'),
      'country' => $translator->translate('Country'),
      'building' => $translator->translate('Building number'),
      'room' => $translator->translate('Room number'),
      'latitude' => $translator->translate('Latitude'),
      'longitude' => $translator->translate('Longitude'),
      'altitude' => $translator->translate('Altitude'),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(14, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(1, $t['completename'], 'input', 'completename', fillable: false, readonly: true));
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false));
    $defColl->add(new Def(
      13,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));
    $defColl->add(new Def(15, $t['address'], 'input', 'address', fillable: true));
    $defColl->add(new Def(17, $t['postcode'], 'input', 'postcode', fillable: true));
    $defColl->add(new Def(18, $t['town'], 'input', 'town', fillable: true));
    $defColl->add(new Def(104, $t['state'], 'input', 'state', fillable: true));
    $defColl->add(new Def(105, $t['country'], 'input', 'country', fillable: true));
    $defColl->add(new Def(11, $t['building'], 'input', 'building', fillable: true));
    $defColl->add(new Def(12, $t['room'], 'input', 'room', fillable: true));
    $defColl->add(new Def(21, $t['latitude'], 'input', 'latitude', fillable: true));
    $defColl->add(new Def(20, $t['longitude'], 'input', 'longitude', fillable: true));
    $defColl->add(new Def(22, $t['altitude'], 'input', 'altitude', fillable: true));
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
      'id'                 => '101',
      'table'              => 'glpi_locations',
      'field'              => 'address',
      'name'               => __('Address'),
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '102',
      'table'              => 'glpi_locations',
      'field'              => 'postcode',
      'name'               => __('Postal code'),
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '103',
      'table'              => 'glpi_locations',
      'field'              => 'town',
      'name'               => __('Town'),
      'datatype'           => 'string',
      'autocomplete'       => true,
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
        'title' => $translator->translatePlural('Location', 'Locations', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Location', 'Locations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/locations',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Network outlet', 'Network outlets', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
