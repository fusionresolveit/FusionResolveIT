<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Passivedcequipmentmodel
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'product_number' => $translator->translate('Product Number'),
      'weight' => $translator->translate('Weight'),
      'required_units' => $translator->translate('Required units'),
      'depth' => $translator->translate('Depth'),
      'power_connections' => $translator->translate('Power connections'),
      'power_consumption' => $translator->translate('Power consumption'),
      'is_half_rack' => $translator->translate('Is half rack'),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translate('Front picture'),
    //   'type'  => 'file',
    //   'name'  => 'picture_front',
    // ],
    // [
    //   'id'    => 138,
    //   'title' => $translator->translate('Rear picture'),
    //   'type'  => 'file',
    //   'name'  => 'picture_rear',
    // ],
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Passive device model', 'Passive device models', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
