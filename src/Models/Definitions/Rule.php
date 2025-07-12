<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Rule
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'ranking' => pgettext('rule', 'Ranking'),
      'description' => pgettext('global', 'Description'),
      'match' => pgettext('rule', 'Logical operator'),
      'is_active' => pgettext('global', 'Active'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(3, $t['ranking'], 'input', 'ranking', fillable: true, display: false));
    $defColl->add(new Def(4, $t['description'], 'textarea', 'description', fillable: true));
    $defColl->add(new Def(
      5,
      $t['match'],
      'dropdown',
      'match',
      values: self::getMatchArray(),
      fillable: true
    ));
    $defColl->add(new Def(8, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));

    return $defColl;

    // [
    //   'id'    => 1001,
    //   'title' => 'Criteria',
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'criteria',
    //   'itemtype' => '\App\Models\Rules\Rulecriterium',
    //   'multiple' => true,
    //   'fillable' => true,
    //   'relationfields' => ['id'],
    // ],
    // [
    //   'id'    => 1002,
    //   'title' => 'Actions',
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'actions',
    //   'itemtype' => '\App\Models\Ruleaction',
    //   'multiple' => true,
    //   'fillable' => true,
    //   'relationfields' => ['id'],
    // ],
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('rule', 'Rule', 'Rules', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('rule', 'Criterion', 'Criteria', 2),
        'icon' => 'brain',
        'link' => $rootUrl . '/criteria',
      ],
      [
        'title' => npgettext('rule', 'Action', 'Actions', 2),
        'icon' => 'running',
        'link' => $rootUrl . '/actions',
      ],
      [
        'title' => pgettext('rule', 'Testing'),
        'icon' => 'vial',
        'link' => '', // $rootUrl . '/testing',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => '', // $rootUrl . '/history',
      ],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  public static function getMatchArray(): array
  {
    return [
      'AND' => [
        'title' => strtolower(pgettext('global', 'And')),
      ],
      'OR' => [
        'title' => strtolower(pgettext('global', 'Or')),
      ],
    ];
  }
}
