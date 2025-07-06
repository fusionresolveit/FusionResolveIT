<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Storage
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
      'rpm' => $translator->translate('Rpm'),
      'cache' => sprintf(
        $translator->translate('%1$s (%2$s)'),
        $translator->translate('Cache'),
        $translator->translate('Mio')
      ),
      'interface' => $translator->translate('Interface'),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
      'type' => $translator->translate('Storage type'),
      'firmware' => $translator->translatePlural('Firmware', 'Firmware', 1),
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
    $defColl->add(new Def(11, $t['size'], 'input', 'size', fillable: true));
    $defColl->add(new Def(12, $t['rpm'], 'input', 'rpm', fillable: true));
    $defColl->add(new Def(13, $t['cache'], 'input', 'cache', fillable: true));
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
    $defColl->add(new Def(
      1001,
      $t['type'],
      'dropdown',
      'type',
      values: self::getTypesArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      1002,
      $t['firmware'],
      'dropdown_remote',
      'firmware',
      dbname: 'firmware_id',
      itemtype: '\App\Models\Firmware',
      fillable: false
    ));

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

  /**
   * @return array<int, mixed>
   */
  public static function getTypesArray(): array
  {
    global $translator;

    return [
      0 => [
        'title' => $translator->translate('Unknown'),
      ],
      1 => [
        'title' => $translator->translate('Hard Disk Drive'),
      ],
      2 => [
        'title' => $translator->translate('Floppy disk'),
      ],
      3 => [
        'title' => $translator->translate('Optical disk'),
      ],
      4 => [
        'title' => $translator->translate('Flash drive'),
      ],
      5 => [
        'title' => $translator->translate('Tape'),
      ],
    ];
  }
}
