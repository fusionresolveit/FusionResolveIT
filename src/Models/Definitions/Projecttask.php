<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Projecttask
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'parent' => $translator->translate('As child of'),
      'state' => $translator->translate('Status'),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'date' => $translator->translate('Creation date'),
      'plan_start_date' => $translator->translate('Planned start date'),
      'plan_end_date' => $translator->translate('Planned end date'),
      'planned_duration' => $translator->translate('Planned duration'),
      'real_start_date' => $translator->translate('Real start date'),
      'real_end_date' => $translator->translate('Real end date'),
      'effective_duration' => $translator->translate('Effective duration'),
      'percent_done' => $translator->translate('Percent done'),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      2,
      $t['parent'],
      'dropdown_remote',
      'parent',
      dbname: 'projecttask_id',
      itemtype: '\App\Models\Projecttask',
      fillable: true
    ));
    $defColl->add(new Def(
      3,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'projectstate_id',
      itemtype: '\App\Models\Projectstate',
      fillable: true
    ));
    $defColl->add(new Def(
      4,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'projecttasktype_id',
      itemtype: '\App\Models\Projecttasktype',
      fillable: true
    ));
    $defColl->add(new Def(15, $t['date'], 'datetime', 'date', fillable: true));
    $defColl->add(new Def(7, $t['plan_start_date'], 'datetime', 'plan_start_date', fillable: true));
    $defColl->add(new Def(8, $t['plan_end_date'], 'datetime', 'plan_end_date', fillable: true));
    $defColl->add(new Def(17, $t['planned_duration'], 'input', 'planned_duration', readonly: true));
    $defColl->add(new Def(9, $t['real_start_date'], 'datetime', 'real_start_date', fillable: true));
    $defColl->add(new Def(10, $t['real_end_date'], 'datetime', 'real_end_date', fillable: true));
    $defColl->add(new Def(18, $t['effective_duration'], 'input', 'effective_duration', readonly: true));
    $defColl->add(new Def(
      5,
      $t['percent_done'],
      'dropdown',
      'percent_done',
      dbname: 'percent_done',
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 100, 5, [], '%'),
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment'));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

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
        'title' => $translator->translatePlural('Project task', 'Project tasks', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Project task', 'Project tasks', 2),
        'icon' => 'home',
        'link' => $rootUrl . '/projecttasks',
      ],
      [
        'title' => $translator->translatePlural('Project task team', 'Project task teams', 1),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/projecttaskteams',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('Ticket', 'Tickets', 2),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/tickets',
        'rightModel' => '\App\Models\Ticket',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
