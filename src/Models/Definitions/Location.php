<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Location
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'          => pgettext('global', 'Name'),
      'completename'  => pgettext('global', 'Complete name'),
      'id'            => pgettext('global', 'Id'),
      'location'      => pgettext('global', 'As child of'),
      'address'       => pgettext('location', 'Address'),
      'postcode'      => pgettext('location', 'Postal code'),
      'town'          => pgettext('location', 'Town'),
      'state'         => pgettext('location', 'State'),
      'country'       => pgettext('location', 'Country'),
      'building'      => pgettext('location', 'Building number'),
      'room'          => pgettext('location', 'Room number'),
      'latitude'      => pgettext('location', 'Latitude'),
      'longitude'     => pgettext('location', 'Longitude'),
      'altitude'      => pgettext('location', 'Altitude'),
      'comment'       => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive'  => pgettext('global', 'Child entities'),
      'updated_at'    => pgettext('global', 'Last update'),
      'created_at'    => pgettext('global', 'Creation date'),
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
    return [
      [
        'title' => npgettext('global', 'Location', 'Locations', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Location', 'Locations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/locations',
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => '',
      ],
      [
        'title' => npgettext('inventory device', 'Network outlet', 'Network outlets', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
