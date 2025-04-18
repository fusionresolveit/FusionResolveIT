<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Softwarelicensetype
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'softwarelicensetype' => $translator->translate('As child of'),
      'is_recursive' => $translator->translate('Child entities'),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      13,
      $t['softwarelicensetype'],
      'dropdown_remote',
      'softwarelicensetype',
      dbname: 'softwarelicensetype_id',
      itemtype: '\App\Models\Softwarelicensetype',
      fillable: true
    ));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
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
        'title' => $translator->translatePlural('License type', 'License types', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('License type', 'License types', 2),
        'icon' => 'edit',
        'link' => $rootUrl . '/licencetypes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
