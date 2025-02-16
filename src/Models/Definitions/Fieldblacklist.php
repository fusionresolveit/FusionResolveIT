<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Fieldblacklist
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
      'recursive'     => $translator->translate('Child entities'),
      'value' => $translator->translate('Value'),
      'field' => $translator->translatePlural('Field', 'Fields', 1),
      'item_type' => $translator->translatePlural('Type', 'Types', 1),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));
    $defColl->add(new Def(1001, $t['recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(6, $t['field'], 'input', 'field', fillable: true));
    $defColl->add(new Def(7, $t['value'], 'input', 'value', fillable: true));
    $defColl->add(new Def(4, $t['item_type'], 'input', 'item_type', fillable: true));

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
        'title' => $translator->translatePlural('Ignored value for the unicity', 'Ignored values for the unicity', 1),
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
