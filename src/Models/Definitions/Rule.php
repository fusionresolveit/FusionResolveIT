<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Rule
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'ranking' => $translator->translate('Ranking'),
      'description' => $translator->translate('Description'),
      'match' => $translator->translate('Logical operator'),
      'is_active' => $translator->translate('Active'),
      'comment' => $translator->translate('Comments'),
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
    //   'title' => $translator->translatePlural('Criterium', 'Criteria', 2),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'criteria',
    //   'itemtype' => '\App\Models\Rules\Rulecriterium',
    //   'multiple' => true,
    //   'fillable' => true,
    //   'relationfields' => ['id'],
    // ],
    // [
    //   'id'    => 1002,
    //   'title' => $translator->translatePlural('Action', 'Actions', 2),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Rule', 'Rules', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Criterion', 'Criteria', 2),
        'icon' => 'brain',
        'link' => $rootUrl . '/criteria',
      ],
      [
        'title' => $translator->translatePlural('Action', 'Actions', 2),
        'icon' => 'running',
        'link' => $rootUrl . '/actions',
      ],
      [
        'title' => $translator->translate('Testing'),
        'icon' => 'vial',
        'link' => '', // $rootUrl . '/testing',
      ],
      [
        'title' => $translator->translate('Historical'),
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
    global $translator;

    return [
      'AND' => [
        'title' => $translator->translate('and'),
      ],
      'OR' => [
        'title' => $translator->translate('or'),
      ],
    ];
  }
}
