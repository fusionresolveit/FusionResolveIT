<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Storage
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'          => pgettext('global', 'Name'),
      'manufacturer'  => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'size'          => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('global', 'Size'),
        pgettext('global', 'Mio')
      ),
      'rpm'           => pgettext('global', 'RPM'),
      'cache'         => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('global', 'Cache'),
        pgettext('global', 'Mio')
      ),
      'interface'     => pgettext('inventory device', 'Interface'),
      'comment'       => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive'  => pgettext('global', 'Child entities'),
      'updated_at'    => pgettext('global', 'Last update'),
      'created_at'    => pgettext('global', 'Creation date'),
      'type'          => pgettext('inventory device', 'Storage type'),
      'firmware'      => npgettext('global', 'Firmware', 'Firmware', 1),
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
        'title' => npgettext('global', 'Storage', 'Storages', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
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

  /**
   * @return array<int, mixed>
   */
  public static function getTypesArray(): array
  {
    return [
      0 => [
        'title' => pgettext('storage type', 'Unknown'),
      ],
      1 => [
        'title' => pgettext('storage type', 'Hard Disk Drive'),
      ],
      2 => [
        'title' => pgettext('storage type', 'Floppy disk'),
      ],
      3 => [
        'title' => pgettext('storage type', 'Optical disk'),
      ],
      4 => [
        'title' => pgettext('storage type', 'Flash drive'),
      ],
      5 => [
        'title' => pgettext('storage type', 'Tape'),
      ],
    ];
  }
}
