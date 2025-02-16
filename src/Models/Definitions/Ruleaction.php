<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Ruleaction
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'rule' => $translator->translatePlural('Rule', 'Rules', 1),
      'action_type' => $translator->translate('Action type'),
      'field' => $translator->translatePlural('Field', 'Fields', 1),
      'value' => $translator->translate('Value'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(
      1001,
      $t['rule'],
      'dropdown_remote',
      'rule',
      dbname: 'rule_id',
      itemtype: '\App\Models\Rules\Rule',
      fillable: true
    ));
    $defColl->add(new Def(
      1002,
      $t['action_type'],
      'dropdown',
      'action_type',
      values: self::getActiontypeArray(),
      fillable: true
    ));
    $defColl->add(new Def(1003, $t['field'], 'input', 'field', fillable: true));
    $defColl->add(new Def(1004, $t['value'], 'input', 'value', fillable: true));

    return $defColl;
  }

  /**
   * @return array<int, mixed>
   */
  public static function getActiontypeArray(): array
  {
    global $translator;
    return [
      0 => [
        'title' => $translator->translate('assign'),
      ],
      1 => [
        'title' => $translator->translate('assign dropdown'),
      ],
      2 => [
        'title' => $translator->translate('append'),
      ],
      3 => [
        'title' => $translator->translate('append dropdown'),
      ],
      4 => [
        'title' => $translator->translate('regex result'),
      ],
      5 => [
        'title' => $translator->translate('append regex result'),
      ],
    ];
  }
}
