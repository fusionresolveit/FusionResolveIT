<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Memorymodule
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'manufacturer' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
      'size' => sprintf(
        $translator->translate('%1$s (%2$s)'),
        $translator->translate('Size'),
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
      'serial' => $translator->translate('Serial number'),
      'otherserial' => $translator->translate('Inventory number'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'state' => $translator->translate('Status'),
      'memoryslot' => $translator->translate('Memory slot'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(11, $t['size'], 'input', 'size', fillable: true));
    $defColl->add(new Def(12, $t['frequence'], 'input', 'frequence', fillable: true));
    $defColl->add(new Def(
      1005,
      $t['memoryslot'],
      'dropdown_remote',
      'memoryslot',
      dbname: 'memoryslot_id',
      itemtype: '\App\Models\Memoryslot'
    ));
    $defColl->add(new Def(
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer'
    ));
    $defColl->add(new Def(
      13,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'memorytype_id',
      itemtype: '\App\Models\Memorytype',
      fillable: true
    ));
    $defColl->add(new Def(
      14,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'memorymodel_id',
      itemtype: '\App\Models\Memorymodel',
      fillable: true
    ));
    $defColl->add(new Def(1001, $t['serial'], 'input', 'serial', fillable: true));
    $defColl->add(new Def(1002, $t['otherserial'], 'input', 'otherserial', fillable: true));
    $defColl->add(new Def(
      1003,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true
    ));
    $defColl->add(new Def(
      1004,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));

    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    // $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
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
