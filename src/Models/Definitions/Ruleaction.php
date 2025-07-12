<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Ruleaction
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'rule' => npgettext('rule', 'Rule', 'Rules', 1),
      'action_type' => pgettext('rule', 'Action type'),
      'field' => npgettext('rule', 'Field', 'Fields', 1),
      'value' => pgettext('rule', 'Value'),
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
    return [
      0 => [
        'title' => pgettext('rule action type', 'assign'),
      ],
      1 => [
        'title' => pgettext('rule action type', 'assign dropdown'),
      ],
      2 => [
        'title' => pgettext('rule action type', 'append'),
      ],
      3 => [
        'title' => pgettext('rule action type', 'append dropdown'),
      ],
      4 => [
        'title' => pgettext('rule action type', 'regex result'),
      ],
      5 => [
        'title' => pgettext('rule action type', 'append regex result'),
      ],
    ];
  }
}
