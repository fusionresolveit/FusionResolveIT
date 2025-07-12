<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Rulecriterium
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'rule' => npgettext('rule', 'Rule', 'Rules', 1),
      'criteria' => npgettext('rule', 'Criterion', 'Criteria', 1),
      'condition' => pgettext('rule', 'Condition'),
      'pattern' => pgettext('rule', 'Patern'),
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
    $defColl->add(new Def(1002, $t['criteria'], 'input', 'criteria', fillable: true));
    $defColl->add(new Def(
      1003,
      $t['condition'],
      'dropdown',
      'condition',
      values: self::getConditionArray(),
      fillable: true
    ));
    $defColl->add(new Def(1004, $t['pattern'], 'input', 'pattern', fillable: true));

    return $defColl;
  }

  /**
   * @return array<int, mixed>
   */
  public static function getConditionArray(): array
  {
    return [
      0 => [
        'title' => pgettext('rule condition', 'is'),
      ],
      1 => [
        'title' => pgettext('rule condition', 'is not'),
      ],
      2 => [
        'title' => pgettext('rule condition', 'contains'),
      ],
      3 => [
        'title' => pgettext('rule condition', 'does not contain'),
      ],
      4 => [
        'title' => pgettext('rule condition', 'starting with'),
      ],
      5 => [
        'title' => pgettext('rule condition', 'finished by'),
      ],
      6 => [
        'title' => pgettext('rule condition', 'regular expression matches'),
      ],
      7 => [
        'title' => pgettext('rule condition', 'regular expression does not match'),
      ],
      8 => [
        'title' => pgettext('rule condition', 'exists'),
      ],
      9 => [
        'title' => pgettext('rule condition', 'does not exist'),
      ],
      10 => [
        'title' => pgettext('rule condition', 'find'),
      ],
      11 => [
        'title' => pgettext('rule condition', 'under'),
      ],
      12 => [
        'title' => pgettext('rule condition', 'not under'),
      ],
      13 => [
        'title' => pgettext('rule condition', 'is empty'),
      ],
    ];
  }
}
