<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Devicememory
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'manufacturer' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
      'size_default' => sprintf(
        $translator->translate('%1$s (%2$s)'),
        $translator->translate('Size by default'),
        $translator->translate('Mio')
      ),
      'frequence' => sprintf(
        $translator->translate('%1$s (%2$s)'),
        $translator->translate('Frequency'),
        $translator->translate('MHz')
      ),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'model' => $translator->translatePlural('Model', 'Models', 1),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer'
    ));
    $defColl->add(new Def(11, $t['size_default'], 'input', 'size_default', fillable: true));
    $defColl->add(new Def(12, $t['frequence'], 'input', 'frequence', fillable: true));
    $defColl->add(new Def(
      13,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'devicememorytype_id',
      itemtype: '\App\Models\Devicememorytype',
      fillable: true
    ));
    $defColl->add(new Def(
      14,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'devicememorymodel_id',
      itemtype: '\App\Models\Devicememorytype',
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
        'title' => $translator->translatePlural('Memory', 'Memory', 1),
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
