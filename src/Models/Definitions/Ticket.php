<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Ticket
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'                => pgettext('global', 'Title'),
      'content'             => pgettext('global', 'Description'),
      'id'                  => pgettext('global', 'Id'),
      'entity'              => npgettext('global', 'Entity', 'Entities', 1),
      'type'                => npgettext('ticket', 'Type', 'Types', 1),
      'status'              => pgettext('global', 'Status'),
      'category'            => npgettext('global', 'Category', 'Categories', 1),
      'location'            => npgettext('global', 'Location', 'Locations', 1),
      'actiontime'          => pgettext('ITIL', 'Total duration'),
      'urgency'             => pgettext('ITIL', 'Urgency'),
      'impact'              => pgettext('ITIL', 'Impact'),
      'priority'            => pgettext('ITIL', 'Priority'),
      'created_at'          => pgettext('ITIL', 'Opening date'),
      'updated_at'          => pgettext('global', 'Last update'),
      'time_to_resolve'     => pgettext('ITIL', 'Time to resolve'),
      'is_late'             => pgettext('ticket', 'Time to resolve exceeded'),
      'solved_at'           => pgettext('ITIL', 'Resolution date'),
      'closed_at'           => pgettext('ITIL', 'Closing date'),
      'usersidlastupdater'  => pgettext('ITIL', 'Last edit by'),
      'usersidrecipient'    => pgettext('ITIL', 'Writer'),
      'requester'           => npgettext('ITIL', 'Requester', 'Requesters', 1),
      'requestergroup'      => npgettext('ITIL', 'Requester group', 'Requester groups', 1),
      'watcher'             => npgettext('ITIL', 'Watcher', 'Watchers', 1),
      'watchergroup'        => npgettext('ITIL', 'Watcher group', 'Watcher groups', 1),
      'technician'          => pgettext('ITIL', 'Technician'),
      'techniciangroup'     => pgettext('ITIL', 'Technician group'),
      'followups'           => npgettext('ITIL', 'Followup', 'Followups', 2),
      'problems'            => npgettext('problem', 'Problem', 'Problems', 2),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', displaygroup: 'main', fillable: true));
    $defColl->add(new Def(21, $t['content'], 'textarea', 'content', fillable: true));
    $defColl->add(new Def(2, $t['id'], 'input', 'id', displaygroup: 'main', display: false, fillable: false));
    $defColl->add(new Def(
      80,
      $t['entity'],
      'dropdown_remote',
      'entity',
      dbname: 'entity_id',
      itemtype: '\App\Models\Entity',
      display: false,
      relationfields: [
        'id',
        'name',
        'completename',
        'address',
        'country',
        'email',
        'fax',
        'phonenumber',
        'postcode',
        'state',
        'town',
        'website',
      ]
    ));
    $defColl->add(new Def(
      14,
      $t['type'],
      'dropdown',
      'type',
      values: self::getTypesArray(),
      displaygroup: 'main',
      fillable: true
    ));
    $defColl->add(new Def(
      12,
      $t['status'],
      'dropdown',
      'status',
      values: self::getStatusArray(),
      displaygroup: 'main',
      fillable: true
    ));
    $defColl->add(new Def(
      7,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'category_id',
      itemtype: '\App\Models\Category',
      displaygroup: 'main',
      fillable: true,
      relationfields: ['id', 'name', 'completename']
    ));
    $defColl->add(new Def(
      83,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      displaygroup: 'main',
      fillable: true,
      relationfields: ['id', 'name', 'completename']
    ));
    $defColl->add(new Def(45, $t['actiontime'], 'input', 'actiontime', displaygroup: 'main', readonly: true));
    $defColl->add(new Def(
      10,
      $t['urgency'],
      'dropdown',
      'urgency',
      values: self::getUrgencyArray(),
      displaygroup: 'priority',
      fillable: true
    ));
    $defColl->add(new Def(
      11,
      $t['impact'],
      'dropdown',
      'impact',
      values: self::getImpactArray(),
      displaygroup: 'priority',
      fillable: true
    ));
    $defColl->add(new Def(
      3,
      $t['priority'],
      'dropdown',
      'priority',
      values: self::getPriorityArray(),
      displaygroup: 'priority',
      fillable: true
    ));
    $defColl->add(new Def(15, $t['created_at'], 'datetime', 'created_at', displaygroup: 'dates', readonly: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true, displaygroup: 'dates'));
    $defColl->add(new Def(18, $t['time_to_resolve'], 'datetime', 'time_to_resolve', displaygroup: 'dates'));
    $defColl->add(new Def(
      82,
      $t['is_late'],
      'boolean',
      'is_late',
      displaygroup: 'dates',
      readonly: true,
      fillable: false
    ));
    $defColl->add(new Def(17, $t['solved_at'], 'datetime', 'solved_at', displaygroup: 'dates', readonly: true));
    $defColl->add(new Def(16, $t['closed_at'], 'datetime', 'closed_at', displaygroup: 'dates', readonly: true));
    $defColl->add(new Def(
      64,
      $t['usersidlastupdater'],
      'dropdown_remote',
      'usersidlastupdater',
      dbname: 'user_id_lastupdater',
      itemtype: '\App\Models\User',
      displaygroup: 'contributor',
      fillable: true,
      readonly: true,
      relationfields: ['id', 'completename']
    ));
    $defColl->add(new Def(
      22,
      $t['usersidrecipient'],
      'dropdown_remote',
      'usersidrecipient',
      dbname: 'user_id_recipient',
      itemtype: '\App\Models\User',
      displaygroup: 'contributor',
      fillable: true,
      readonly: true,
      relationfields: ['id', 'completename']
    ));
    $defColl->add(new Def(
      4,
      $t['requester'],
      'dropdown_remote',
      'requester',
      itemtype: '\App\Models\User',
      multiple: true,
      pivot: ['type' => 1],
      displaygroup: 'contributor',
      fillable: true,
      relationfields: ['id', 'completename']
    ));
    $defColl->add(new Def(
      71,
      $t['requestergroup'],
      'dropdown_remote',
      'requestergroup',
      itemtype: '\App\Models\Group',
      multiple: true,
      pivot: ['type' => 1],
      displaygroup: 'contributor',
      fillable: true,
      relationfields: ['id', 'name', 'completename']
    ));
    $defColl->add(new Def(
      66,
      $t['watcher'],
      'dropdown_remote',
      'watcher',
      itemtype: '\App\Models\User',
      multiple: true,
      pivot: ['type' => 3],
      displaygroup: 'contributor',
      fillable: true,
      relationfields: ['id', 'completename']
    ));
    $defColl->add(new Def(
      65,
      $t['watchergroup'],
      'dropdown_remote',
      'watchergroup',
      itemtype: '\App\Models\Group',
      multiple: true,
      pivot: ['type' => 3],
      displaygroup: 'contributor',
      fillable: true,
      relationfields: ['id', 'name', 'completename']
    ));
    $defColl->add(new Def(
      5,
      $t['technician'],
      'dropdown_remote',
      'technician',
      itemtype: '\App\Models\User',
      multiple: true,
      pivot: ['type' => 2],
      displaygroup: 'contributor',
      fillable: true,
      relationfields: ['id', 'completename']
    ));
    $defColl->add(new Def(
      8,
      $t['techniciangroup'],
      'dropdown_remote',
      'techniciangroup',
      itemtype: '\App\Models\Group',
      multiple: true,
      pivot: ['type' => 2],
      displaygroup: 'contributor',
      fillable: true,
      relationfields: ['id', 'name', 'completename']
    ));
    $defColl->add(new Def(
      301,
      $t['followups'],
      'input',
      'followups',
      itemtype: '\App\Models\Followup',
      multiple: true,
      fillable: false,
      display: false,
      relationfields: ['id', 'content', 'user.completename'],
      usein: ['search', 'notification']
    ));
    $defColl->add(new Def(
      300,
      $t['problems'],
      'dropdown_remote',
      'problems',
      itemtype: '\App\Models\Problem',
      multiple: true,
      fillable: false,
      display: false,
      relationfields: ['id', 'name', 'date', 'content'],
      usein: ['search', 'notification']
    ));

    return $defColl;

    // [ TODO supplier
    //   'id'    => 6,
    //   'title' => 'Assigned to a supplier',
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'technician:id,name,firstname,lastname',
    //   'itemtype' => '\App\Models\User',
    //   'multiple' => true,
    // ],


    // TODO others like users
  }

  /**
   * @return array<int, mixed>
   */
  public static function getTypesArray(): array
  {
    return [
      1 => [
        'title' => pgettext('ticket type', 'Incident'),
      ],
      2 => [
        'title' => pgettext('ticket type', 'Request'),
      ],
    ];
  }

  /**
   * @return array<int, mixed>
   */
  public static function getStatusArray(): array
  {
    return [
      1 => [
        'title' => pgettext('ticket status', 'New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => pgettext('ticket status', 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => pgettext('ticket status', 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => pgettext('ticket status', 'Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => pgettext('ticket status', 'Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => pgettext('ticket status', 'Closed'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
    ];
  }

  /**
   * @return array<int, mixed>
   */
  public static function getUrgencyArray(): array
  {
    return [
      5 => [
        'title' => pgettext('ITIL urgency', 'Very high'),
      ],
      4 => [
        'title' => pgettext('ITIL urgency', 'High'),
      ],
      3 => [
        'title' => pgettext('ITIL urgency', 'Medium'),
      ],
      2 => [
        'title' => pgettext('ITIL urgency', 'Low'),
      ],
      1 => [
        'title' => pgettext('ITIL urgency', 'Very low'),
      ],
    ];
  }

  /**
   * @return array<int, mixed>
   */
  public static function getImpactArray(): array
  {
    return [
      5 => [
        'title' => pgettext('ITIL impact', 'Very high'),
      ],
      4 => [
        'title' => pgettext('ITIL impact', 'High'),
      ],
      3 => [
        'title' => pgettext('ITIL impact', 'Medium'),
      ],
      2 => [
        'title' => pgettext('ITIL impact', 'Low'),
      ],
      1 => [
        'title' => pgettext('ITIL impact', 'Very low'),
      ],
    ];
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
        'title' => npgettext('ticket', 'Ticket', 'Tickets', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('ITIL', 'Statistics'),
        'icon' => 'chartline',
        'link' => $rootUrl . '/stats',
      ],
      [
        'title' => npgettext('ITIL', 'Approval', 'Approvals', 2),
        'icon' => 'thumbs up',
        'link' => $rootUrl . '/approvals',
      ],
      [
        'title' => pgettext('global', 'Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
      ],
      [
        'title' => npgettext('global', 'Cost', 'Costs', 2),
        'icon' => 'money bill alternate',
        'link' => $rootUrl . '/costs',
      ],
      [
        'title' => npgettext('global', 'Project', 'Projects', 2),
        'icon' => 'folder open',
        'link' => $rootUrl . '/projects',
        'rightModel' => '\App\Models\Project',
      ],
      [
        'title' => npgettext('project', 'Project task', 'Project tasks', 2),
        'icon' => 'tasks',
        'link' => $rootUrl . '/projecttasks',
        'rightModel' => '\App\Models\Project',
      ],
      [
        'title' => npgettext('problem', 'Problem', 'Problems', 2),
        'icon' => 'drafting compass',
        'link' => $rootUrl . '/problem',
        'rightModel' => '\App\Models\Problem',
      ],
      [
        'title' => npgettext('change', 'Change', 'Changes', 2),
        'icon' => 'paint roller',
        'link' => $rootUrl . '/changes',
        'rightModel' => '\App\Models\Change',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
