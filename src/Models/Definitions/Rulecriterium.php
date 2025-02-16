<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Rulecriterium
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'rule' => $translator->translatePlural('Rule', 'Rules', 1),
      'criteria' => $translator->translate('Criterium'),
      'condition' => $translator->translate('Condition'),
      'pattern' => $translator->translate('Patern'),

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
    global $translator;
    return [
      0 => [
        'title' => $translator->translate('is'),
      ],
      1 => [
        'title' => $translator->translate('is not'),
      ],
      2 => [
        'title' => $translator->translate('contains'),
      ],
      3 => [
        'title' => $translator->translate('does not contain'),
      ],
      4 => [
        'title' => $translator->translate('starting with'),
      ],
      5 => [
        'title' => $translator->translate('finished by'),
      ],
      6 => [
        'title' => $translator->translate('regular expression matches'),
      ],
      7 => [
        'title' => $translator->translate('regular expression does not match'),
      ],
      8 => [
        'title' => $translator->translate('exists'),
      ],
      9 => [
        'title' => $translator->translate('does not exist'),
      ],
      10 => [
        'title' => $translator->translate('find'),
      ],
      11 => [
        'title' => $translator->translate('under'),
      ],
      12 => [
        'title' => $translator->translate('not under'),
      ],
      13 => [
        'title' => $translator->translate('is empty'),
      ],
    ];
  }
}
