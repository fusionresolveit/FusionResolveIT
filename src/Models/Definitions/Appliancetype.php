<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Appliancetype
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name'      => $translator->translate('Name'),
      'comment'   => $translator->translate('Comments'),
      'entity' => $translator->translatePlural('Entity', 'Entities', 1),
      'recursive' => $translator->translate('Child entities'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(6, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      80,
      $t['entity'],
      'dropdown_remote',
      'entity',
      dbname: 'entity_id',
      itemtype: '\App\Models\Entity',
      display: false,
      relationfields: [
        'id',
        'name',
        'completename',
        'address',
        'country',
        'email',
        'fax',
        'phonenumber',
        'postcode',
        'state',
        'town',
        'website',
      ]
    ));
    $defColl->add(new Def(86, $t['recursive'], 'boolean', 'is_recursive', fillable: true));

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
        'title' => $translator->translatePlural('Appliance type', 'Appliance types', 1),
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
