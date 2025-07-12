<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Deviceprocessor
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'manufacturer' => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'frequency_default' => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('global', 'Frequency by default'),
        pgettext('global', 'MHz')
      ),
      'frequence' => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('global', 'MHz'),
        pgettext('global', 'MHz')
      ),
      'nbcores_default' => pgettext('inventory device', 'Number of cores'),
      'nbthreads_default' => pgettext('inventory device', 'Number of threads'),
      'model' => npgettext('global', 'Model', 'Models', 1),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
      'cpuid' => pgettext('inventory device', 'CPU ID'),
      'stepping' => pgettext('inventory device', 'Stepping'),
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
    $defColl->add(new Def(11, $t['frequency_default'], 'input', 'frequency_default', fillable: true));
    $defColl->add(new Def(12, $t['frequence'], 'input', 'frequence', fillable: true));
    $defColl->add(new Def(13, $t['nbcores_default'], 'input', 'nbcores_default', fillable: true));
    $defColl->add(new Def(14, $t['nbthreads_default'], 'input', 'nbthreads_default', fillable: true));
    $defColl->add(new Def(
      15,
      $t['model'],
      'dropdown_remote',
      'model',
      dbname: 'deviceprocessormodel_id',
      itemtype: '\App\Models\Deviceprocessortype',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));
    $defColl->add(new Def(1001, $t['cpuid'], 'input', 'cpuid', fillable: true));
    $defColl->add(new Def(1002, $t['stepping'], 'input', 'stepping', fillable: true));

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
        'title' => npgettext('global', 'Processor', 'Processors', 1),
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
}
