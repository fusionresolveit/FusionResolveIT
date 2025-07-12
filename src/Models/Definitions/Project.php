<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Project
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'code' => pgettext('project', 'Code'),
      'content' => pgettext('global', 'Description'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'state' => pgettext('global', 'Status'),
      'date' => pgettext('global', 'Creation date'),
      'show_on_global_gantt' => pgettext('project', 'Show on global GANTT'),
      'user' => pgettext('project', 'Manager'),
      'group' => pgettext('project', 'Manager group'),
      'plan_start_date' => pgettext('ITIL', 'Planned start date'),
      'plan_end_date' => pgettext('ITIL', 'Planned end date'),
      'real_start_date' => pgettext('ITIL', 'Real start date'),
      'real_end_date' => pgettext('ITIL', 'Real end date'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'percent_done' => pgettext('global', 'Percent done'),
      'priority' => pgettext('ITIL', 'Priority'),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(4, $t['code'], 'input', 'code', fillable: true));
    $defColl->add(new Def(21, $t['content'], 'textarea', 'content', fillable: true));
    $defColl->add(new Def(
      14,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'projecttype_id',
      itemtype: '\App\Models\Projecttype',
      fillable: true
    ));
    $defColl->add(new Def(
      12,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'projectstate_id',
      itemtype: '\App\Models\Projectstate',
      fillable: true
    ));
    $defColl->add(new Def(15, $t['date'], 'datetime', 'date', fillable: true));
    $defColl->add(new Def(6, $t['show_on_global_gantt'], 'boolean', 'show_on_global_gantt', fillable: true));
    $defColl->add(new Def(
      24,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      49,
      $t['group'],
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(7, $t['plan_start_date'], 'datetime', 'plan_start_date', fillable: true));
    $defColl->add(new Def(8, $t['plan_end_date'], 'datetime', 'plan_end_date', fillable: true));
    $defColl->add(new Def(9, $t['real_start_date'], 'datetime', 'real_start_date', fillable: true));
    $defColl->add(new Def(10, $t['real_end_date'], 'datetime', 'real_end_date', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      5,
      $t['percent_done'],
      'dropdown',
      'percent_done',
      dbname: 'percent_done',
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 100, 5, [], '%'),
      fillable: true
    ));
    $defColl->add(new Def(
      3,
      $t['priority'],
      'dropdown',
      'priority',
      dbname: 'priority',
      values: self::getPriorityArray(),
      fillable: true
    ));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //    'id'    => 50,
    //    'title' => pgettext('global', 'Template name'),
    //    'type'  => 'input',
    //    'name'  => 'template_name',
    // ],
    // [
    //    'id'    => 80,
    //    'title' => npgettext('global', 'Entity', 'Entities', 1),
    //    'type'  => 'dropdown_remote',
    //    'name'  => 'completename',
    //    'itemtype' => '\App\Models\Entity',
    // ],


    /*
    $tab[] = [
    'id'                 => 'common',
    'name'               => __('Characteristics')
    ];

    $tab[] = [
    'id'                 => '2',
    'table'              => $this->getTable(),
    'field'              => 'id',
    'name'               => __('ID'),
    'massiveaction'      => false,
    'datatype'           => 'number'
    ];


    $tab[] = [
    'id'                 => '13',
    'table'              => $this->getTable(),
    'field'              => 'name',
    'name'               => __('Father'),
    'datatype'           => 'itemlink',
    'massiveaction'      => false,
    'joinparams'         => [
    'condition'          => 'AND 1=1'
    ]
    ];




    $tab[] = [
    'id'                 => '91',
    'table'              => ProjectCost::getTable(),
    'field'              => 'totalcost',
    'name'               => __('Total cost'),
    'datatype'           => 'decimal',
    'forcegroupby'       => true,
    'usehaving'          => true,
    'massiveaction'      => false,
    'joinparams'         => [
    'jointype'           => 'child',
    'specific_itemtype'  => 'ProjectCost',
    'condition'          => 'AND NEWTABLE.`projects_id` = REFTABLE.`id`',
    'beforejoin'         => [
    'table'        => $this->getTable(),
    'joinparams'   => [
    'jointype'  => 'child'
    ],
    ],
    ],
    'computation'        => '(SUM('.$DB->quoteName('TABLE.cost').'))',
    'nometa'             => true, // cannot GROUP_CONCAT a SUM
    ];

    $itil_count_types = [
    'Change'  => _x('quantity', 'Number of changes'),
    'Problem' => _x('quantity', 'Number of problems'),
    'Ticket'  => _x('quantity', 'Number of tickets'),
    ];
    $index = 92;
    foreach ($itil_count_types as $itil_type => $label)
    {
    $tab[] = [
    'id'                 => $index,
    'table'              => Itil_Project::getTable(),
    'field'              => 'id',
    'name'               => $label,
    'datatype'           => 'count',
    'forcegroupby'       => true,
    'usehaving'          => true,
    'massiveaction'      => false,
    'joinparams'         => [
    'jointype'           => 'child',
    'condition'          => "AND NEWTABLE.`itemtype` = '$itil_type'"
    ]
    ];
    $index++;
    }

    $tab[] = [
    'id'                 => 'project_team',
    'name'               => ProjectTeam::getTypeName(),
    ];

    $tab[] = [
    'id'                 => '87',
    'table'              => User::getTable(),
    'field'              => 'name',
    'name'               => User::getTypeName(2),
    'forcegroupby'       => true,
    'datatype'           => 'dropdown',
    'joinparams'         => [
    'jointype'          => 'itemtype_item_revert',
    'specific_itemtype' => 'User',
    'beforejoin'        => [
    'table'      => ProjectTeam::getTable(),
    'joinparams' => [
    'jointype' => 'child',
    ]
    ]
    ]
    ];

    $tab[] = [
    'id'                 => '88',
    'table'              => Group::getTable(),
    'field'              => 'completename',
    'name'               => Group::getTypeName(2),
    'forcegroupby'       => true,
    'datatype'           => 'dropdown',
    'joinparams'         => [
    'jointype'          => 'itemtype_item_revert',
    'specific_itemtype' => 'Group',
    'beforejoin'        => [
    'table'      => ProjectTeam::getTable(),
    'joinparams' => [
    'jointype' => 'child',
    ]
    ]
    ]
    ];

    $tab[] = [
    'id'                 => '89',
    'table'              => Supplier::getTable(),
    'field'              => 'name',
    'name'               => Supplier::getTypeName(2),
    'forcegroupby'       => true,
    'datatype'           => 'dropdown',
    'joinparams'         => [
    'jointype'          => 'itemtype_item_revert',
    'specific_itemtype' => 'Supplier',
    'beforejoin'        => [
    'table'      => ProjectTeam::getTable(),
    'joinparams' => [
    'jointype' => 'child',
    ]
    ]
    ]
    ];

    $tab[] = [
    'id'                 => '90',
    'table'              => Contact::getTable(),
    'field'              => 'name',
    'name'               => Contact::getTypeName(2),
    'forcegroupby'       => true,
    'datatype'           => 'dropdown',
    'joinparams'         => [
    'jointype'          => 'itemtype_item_revert',
    'specific_itemtype' => 'Contact',
    'beforejoin'        => [
    'table'      => ProjectTeam::getTable(),
    'joinparams' => [
    'jointype' => 'child',
    ]
    ]
    ]
    ];

    $tab[] = [
    'id'                 => 'project_task',
    'name'               => ProjectTask::getTypeName(),
    ];

    $tab[] = [
    'id'                 => '111',
    'table'              => ProjectTask::getTable(),
    'field'              => 'name',
    'name'               => __('Name'),
    'datatype'           => 'string',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '112',
    'table'              => ProjectTask::getTable(),
    'field'              => 'content',
    'name'               => __('Description'),
    'datatype'           => 'text',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '113',
    'table'              => ProjectState::getTable(),
    'field'              => 'name',
    'name'               => _x('item', 'State'),
    'datatype'           => 'dropdown',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'          => 'item_revert',
    'specific_itemtype' => 'ProjectState',
    'beforejoin'        => [
    'table'      => ProjectTask::getTable(),
    'joinparams' => [
    'jointype' => 'child',
    ]
    ]
    ]
    ];

    $tab[] = [
    'id'                 => '114',
    'table'              => ProjectTaskType::getTable(),
    'field'              => 'name',
    'name'               => _n('Type', 'Types', 1),
    'datatype'           => 'dropdown',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'          => 'item_revert',
    'specific_itemtype' => 'ProjectTaskType',
    'beforejoin'        => [
    'table'      => ProjectTask::getTable(),
    'joinparams' => [
    'jointype' => 'child',
    ]
    ]
    ]
    ];

    $tab[] = [
    'id'                 => '115',
    'table'              => ProjectTask::getTable(),
    'field'              => 'date',
    'name'               => __('Opening date'),
    'datatype'           => 'datetime',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '116',
    'table'              => ProjectTask::getTable(),
    'field'              => 'updated_at',
    'name'               => __('Last update'),
    'datatype'           => 'datetime',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '117',
    'table'              => ProjectTask::getTable(),
    'field'              => 'percent_done',
    'name'               => __('Percent done'),
    'datatype'           => 'number',
    'unit'               => '%',
    'min'                => 0,
    'max'                => 100,
    'step'               => 5,
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '118',
    'table'              => ProjectTask::getTable(),
    'field'              => 'plan_start_date',
    'name'               => __('Planned start date'),
    'datatype'           => 'datetime',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '119',
    'table'              => ProjectTask::getTable(),
    'field'              => 'plan_end_date',
    'name'               => __('Planned end date'),
    'datatype'           => 'datetime',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '120',
    'table'              => ProjectTask::getTable(),
    'field'              => 'real_start_date',
    'name'               => __('Real start date'),
    'datatype'           => 'datetime',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '122',
    'table'              => ProjectTask::getTable(),
    'field'              => 'real_end_date',
    'name'               => __('Real end date'),
    'datatype'           => 'datetime',
    'massiveaction'      => false,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '123',
    'table'              => ProjectTask::getTable(),
    'field'              => 'planned_duration',
    'name'               => __('Planned Duration'),
    'datatype'           => 'timestamp',
    'min'                => 0,
    'max'                => 100*HOUR_TIMESTAMP,
    'step'               => HOUR_TIMESTAMP,
    'addfirstminutes'    => true,
    'inhours'            => true,
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '124',
    'table'              => ProjectTask::getTable(),
    'field'              => 'effective_duration',
    'name'               => __('Effective duration'),
    'datatype'           => 'timestamp',
    'min'                => 0,
    'max'                => 100*HOUR_TIMESTAMP,
    'step'               => HOUR_TIMESTAMP,
    'addfirstminutes'    => true,
    'inhours'            => true,
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '125',
    'table'              => ProjectTask::getTable(),
    'field'              => 'comment',
    'name'               => __('Comments'),
    'datatype'           => 'text',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    $tab[] = [
    'id'                 => '126',
    'table'              => ProjectTask::getTable(),
    'field'              => 'is_milestone',
    'name'               => __('Milestone'),
    'datatype'           => 'bool',
    'massiveaction'      => false,
    'forcegroupby'       => true,
    'splititems'         => true,
    'joinparams'         => [
    'jointype'  => 'child'
    ]
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

    */
  }

  /**
   * @return array<int, mixed>
   */
  public static function getPriorityArray(): array
  {
    return [
      6 => [
        'title' => pgettext('ITIL priority', 'Major'),
        'color' => 'fusionmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => pgettext('ITIL priority', 'Very high'),
        'color' => 'fusionveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => pgettext('ITIL priority', 'High'),
        'color' => 'fusionhigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => pgettext('ITIL priority', 'Medium'),
        'color' => 'fusionmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => pgettext('ITIL priority', 'Low'),
        'color' => 'fusionlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => pgettext('ITIL priority', 'Very low'),
        'color' => 'fusionverylow',
        'icon'  => 'volume off',
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Project', 'Projects', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('project', 'Project task', 'Project tasks', 2),
        'icon' => 'columns',
        'link' => $rootUrl . '/projecttasks',
      ],
      [
        'title' => npgettext('project', 'Project team', 'Project teams', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/projectteams',
      ],
      [
        'title' => npgettext('global', 'Project', 'Projects', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/projects',
      ],
      [
        'title' => pgettext('project', 'GANTT'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => pgettext('project', 'Kanban'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Cost', 'Costs', 2),
        'icon' => 'money bill alternate',
        'link' => $rootUrl . '/costs',
      ],
      [
        'title' => npgettext('project', 'ITIL item', 'ITIL items', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/itilitems',
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('global', 'Contract', 'Contracts', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => npgettext('global', 'Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => pgettext('global', 'Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
