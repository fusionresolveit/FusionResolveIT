<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Dcroom
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'datacenter' => npgettext('global', 'Data center', 'Data centers', 1),
      'location' => npgettext('global', 'Location', 'Locations', 1),
      'vis_cols' => pgettext('dc room', 'Number of columns'),
      'vis_rows' => pgettext('dc room', 'Number of rows'),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      2,
      $t['datacenter'],
      'dropdown_remote',
      'datacenter',
      dbname: 'datacenter_id',
      itemtype: '\App\Models\Datacenter',
      fillable: true
    ));
    $defColl->add(new Def(
      3,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));
    $defColl->add(new Def(4, $t['vis_cols'], 'input', 'vis_cols', fillable: true));
    $defColl->add(new Def(5, $t['vis_rows'], 'input', 'vis_rows', fillable: true));
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
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Server room', 'Server rooms', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('inventory device', 'Impact analysis'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Rack', 'Racks', 2),
        'icon' => 'caret square down outline',
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
        'title' => npgettext('global', 'External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => pgettext('global', 'ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
