<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Networkequipmentmodel
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'product_number' => pgettext('inventory device', 'Product Number'),
      'weight' => pgettext('inventory device', 'Weight'),
      'required_units' => pgettext('inventory device', 'Required units'),
      'depth' => pgettext('inventory device', 'Depth'),
      'power_connections' => pgettext('inventory device', 'Power connections'),
      'power_consumption' => pgettext('inventory device', 'Power consumption'),
      'is_half_rack' => pgettext('inventory device', 'Is half rack'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(130, $t['product_number'], 'input', 'product_number', fillable: true));
    $defColl->add(new Def(131, $t['weight'], 'input', 'weight', fillable: true));
    $defColl->add(new Def(132, $t['required_units'], 'input', 'required_units', fillable: true));
    $defColl->add(new Def(133, $t['depth'], 'input', 'depth', fillable: true));
    $defColl->add(new Def(134, $t['power_connections'], 'input', 'power_connections', fillable: true));
    $defColl->add(new Def(135, $t['power_consumption'], 'input', 'power_consumption', fillable: true));
    $defColl->add(new Def(136, $t['is_half_rack'], 'boolean', 'is_half_rack', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 137,
    //   'title' => pgettext('inventory device', 'Front picture'),
    //   'type'  => 'file',
    //   'name'  => 'picture_front',
    // ],
    // [
    //   'id'    => 138,
    //   'title' => pgettext('inventory device', 'Rear picture'),
    //   'type'  => 'file',
    //   'name'  => 'picture_rear',
    // ],
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Networking equipment model', 'Networking equipment models', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
