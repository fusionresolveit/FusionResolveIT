<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Deviceharddrive
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'manufacturer' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
      'capacity_default' => sprintf(
        $translator->translate('%1$s (%2$s)'),
        $translator->translate('Capacity by default'),
        $translator->translate('Mio')
      ),
      'rpm' => $translator->translate('Rpm'),
      'cache' => sprintf(
        $translator->translate('%1$s (%2$s)'),
        $translator->translate('Cache'),
        $translator->translate('Mio')
      ),
      'model' => $translator->translatePlural('Model', 'Models', 1),
      'interface' => $translator->translate('Interface'),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      3,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer',
      fillable: true
    ));
    $defColl->add(new Def(11, $t['capacity_default'], 'input', 'capacity_default', fillable: true));
    $defColl->add(new Def(12, $t['rpm'], 'input', 'rpm', fillable: true));
    $defColl->add(new Def(13, $t['cache'], 'input', 'cache', fillable: true));
    $defColl->add(new Def(
      15,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'deviceharddrivemodel_id',
      itemtype: '\App\Models\Deviceharddrivemodel',
      fillable: true
    ));
    $defColl->add(new Def(
      14,
      $t['interface'],
      'dropdown_remote',
      'interface',
      dbname: 'interfacetype_id',
      itemtype: '\App\Models\Interfacetype',
      fillable: true
    ));
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
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Hard drive', 'Hard drives', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
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
