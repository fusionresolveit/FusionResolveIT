<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Projecttask
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'parent' => pgettext('global', 'As child of'),
      'state' => pgettext('global', 'Status'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'date' => pgettext('global', 'Creation date'),
      'plan_start_date' => pgettext('ITIL', 'Planned start date'),
      'plan_end_date' => pgettext('ITIL', 'Planned end date'),
      'planned_duration' => pgettext('ITIL', 'Planned duration'),
      'real_start_date' => pgettext('ITIL', 'Real start date'),
      'real_end_date' => pgettext('ITIL', 'Real end date'),
      'effective_duration' => pgettext('project', 'Effective duration'),
      'percent_done' => pgettext('global', 'Percent done'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
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
    return [
      [
        'title' => npgettext('project', 'Project task', 'Project tasks', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('project', 'Project task', 'Project tasks', 2),
        'icon' => 'home',
        'link' => $rootUrl . '/projecttasks',
      ],
      [
        'title' => npgettext('project', 'Project task team', 'Project task teams', 1),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/projecttaskteams',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('ticket', 'Ticket', 'Tickets', 2),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/tickets',
        'rightModel' => '\App\Models\Ticket',
      ],
      [
        'title' => npgettext('global', 'Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
