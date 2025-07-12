<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Projecttasktemplate
{
  public static function getDefinition(): DefinitionCollection
  {
    $HOUR_TIMESTAMP = 3600;

    $t = [
      'name' => pgettext('global', 'Name'),
      'state' => pgettext('global', 'Status'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'projecttasks' => pgettext('global', 'As child of'),
      'percent_done' => pgettext('global', 'Percent done'),
      'is_milestone' => pgettext('project', 'Milestone'),
      'plan_start_date' => pgettext('ITIL', 'Planned start date'),
      'real_start_date' => pgettext('ITIL', 'Real start date'),
      'plan_end_date' => pgettext('ITIL', 'Planned end date'),
      'real_end_date' => pgettext('ITIL', 'Real end date'),
      'planned_duration' => pgettext('ITIL', 'Planned duration'),
      'effective_duration' => pgettext('project', 'Effective duration'),
      'description' => pgettext('global', 'Description'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      4,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'projectstate_id',
      itemtype: '\App\Models\Projectstate',
      fillable: true
    ));
    $defColl->add(new Def(
      5,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'projecttasktype_id',
      itemtype: '\App\Models\Projecttasktype',
      fillable: true
    ));
    $defColl->add(new Def(
      6,
      $t['projecttasks'],
      'dropdown_remote',
      'projecttasks',
      dbname: 'projecttask_id',
      itemtype: '\App\Models\Projecttask',
      fillable: true
    ));
    $defColl->add(new Def(
      7,
      $t['percent_done'],
      'dropdown',
      'percent_done',
      dbname: 'percent_done',
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 100, 5, [], '%'),
      fillable: true
    ));
    $defColl->add(new Def(8, $t['is_milestone'], 'boolean', 'is_milestone', fillable: true));
    $defColl->add(new Def(9, $t['plan_start_date'], 'datetime', 'plan_start_date', fillable: true));
    $defColl->add(new Def(10, $t['real_start_date'], 'datetime', 'real_start_date', fillable: true));
    $defColl->add(new Def(11, $t['plan_end_date'], 'datetime', 'plan_end_date', fillable: true));
    $defColl->add(new Def(12, $t['real_end_date'], 'datetime', 'real_end_date', fillable: true));
    $defColl->add(new Def(
      13,
      $t['planned_duration'],
      'dropdown',
      'planned_duration',
      dbname: 'planned_duration',
      values: self::getTimestampArray(
        [
          'min' => 0,
          'max' => 100 * $HOUR_TIMESTAMP,
          'step' => $HOUR_TIMESTAMP,
          'addfirstminutes' => true,
          'inhours' => true
        ]
      ),
      fillable: true
    ));
    $defColl->add(new Def(
      14,
      $t['effective_duration'],
      'dropdown',
      'effective_duration',
      dbname: 'effective_duration',
      values: self::getTimestampArray(
        [
          'min' => 0,
          'max' => 100 * $HOUR_TIMESTAMP,
          'step' => $HOUR_TIMESTAMP,
          'addfirstminutes' => true,
          'inhours' => true
        ]
      ),
      fillable: true
    ));
    $defColl->add(new Def(15, $t['description'], 'textarea', 'description', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;

    // [
    //    'id'    => 80,
    //    'title' => npgettext('global', 'Entity', 'Entities', 1),
    //    'type'  => 'dropdown_remote',
    //    'name'  => 'completename',
    //    'itemtype' => '\App\Models\Entity',
    // ],


    /*

    $tab[] = [
    'id'   => 'common',
    'name' => __('Characteristics')
    ];

    $tab[] = [
    'id'                => '2',
    'table'             => $this->getTable(),
    'field'             => 'id',
    'name'              => __('ID'),
    'massiveaction'     => false,
    'datatype'          => 'number'
    ];

    if ($DB->fieldExists($this->getTable(), 'product_number'))
    {
    $tab[] = [
    'id'  => '3',
    'table'  => $this->getTable(),
    'field'  => 'product_number',
    'name'   => __('Product number'),
    'autocomplete' => true,
    ];
    }

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));
    */
  }

  /**
   * @param array<string, mixed> $options
   *
   * @return array<mixed>
   */
  public static function getTimestampArray(array $options = []): array
  {
    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;


    $params = [];
    $params['min']                 = 0;
    $params['max']                 = $DAY_TIMESTAMP;
    $params['step']                = 5 * $MINUTE_TIMESTAMP;
    $params['addfirstminutes']     = false;
    $params['toadd']               = [];
    $params['inhours']             = false;

    if (count($options))
    {
      foreach ($options as $key => $val)
      {
        $params[$key] = $val;
      }
    }

    $params['min'] = floor($params['min'] / $params['step']) * $params['step'];

    if ($params['min'] == 0)
    {
      $params['min'] = $params['step'];
    }

    $values = [];

    if ($params['addfirstminutes'])
    {
      $max = max($params['min'], 10 * $MINUTE_TIMESTAMP);
      for ($i = $MINUTE_TIMESTAMP; $i < $max; $i += $MINUTE_TIMESTAMP)
      {
        $values[$i] = '';
      }
    }

    for ($i = $params['min']; $i <= $params['max']; $i += $params['step'])
    {
      $values[$i] = '';
    }

    if (count($params['toadd']))
    {
      foreach ($params['toadd'] as $key)
      {
        $values[$key] = '';
      }
      ksort($values);
    }

    foreach ($values as $i => $val)
    {
      if ($params['inhours'])
      {
        $day  = 0;
        $hour = floor($i / $HOUR_TIMESTAMP);
      }
      else
      {
        $day  = floor($i / $DAY_TIMESTAMP);
        $hour = floor(($i % $DAY_TIMESTAMP) / $HOUR_TIMESTAMP);
      }
      $minute     = floor(($i % $HOUR_TIMESTAMP) / $MINUTE_TIMESTAMP);
      if ($minute === 0.0)
      {
        $minute = '00';
      }
      $values[$i] = '';
      if ($day > 0)
      {
        if (($hour > 0) || ($minute > 0))
        {
          if ($minute < 10)
          {
            $minute = '0' . $minute;
          }

          //TRANS: %1$d is the number of days, %2$d the number of hours,
          //       %3$s the number of minutes : display 1 day 3h15
          $values[$i] = sprintf(
            npgettext('global', '%1$d day %2$dh%3$s', '%1$d days %2$dh%3$s', (int) $day),
            $day,
            $hour,
            $minute
          );
        }
        else
        {
          $values[$i] = sprintf(npgettext('global', '%d day', '%d days', (int) $day), $day);
        }
      }
      elseif ($hour > 0 || $minute > 0)
      {
        if ($minute < 10)
        {
          $minute = '0' . $minute;
        }

        //TRANS: %1$d the number of hours, %2$s the number of minutes : display 3h15
        $values[$i] = sprintf(pgettext('global', '%1$dh%2$s'), $hour, $minute);
      }
    }

    $tab = [];
    foreach (array_keys($values) as $key)
    {
      $tab[$key]['title'] = $values[$key];
    }
    return $tab;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Project task template', 'Project task templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
