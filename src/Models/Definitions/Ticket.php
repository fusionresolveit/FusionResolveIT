<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Ticket
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Title'),
      'content' => $translator->translate('Description'),
      'id' => $translator->translate('ID'),
      'entity' => $translator->translatePlural('Entity', 'Entities', 1),
      'type' => $translator->translate('Type', 'Types', 1),
      'status' => $translator->translate('Status'),
      'category' => $translator->translate('Category'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'actiontime' => $translator->translate('Total duration'),
      'urgency' => $translator->translate('Urgency'),
      'impact' => $translator->translate('Impact'),
      'priority' => $translator->translate('Priority'),
      'created_at' => $translator->translate('Opening date'),
      'updated_at' => $translator->translate('Last update'),
      'time_to_resolve' => $translator->translate('Time to resolve'),
      'is_late' => $translator->translate('Time to resolve exceeded'),
      'solved_at' => $translator->translate('Resolution date'),
      'closed_at' => $translator->translate('Closing date'),
      'usersidlastupdater' => $translator->translate('Last edit by'),
      'usersidrecipient' => $translator->translate('Writer'),
      'requester' => $translator->translatePlural('Requester', 'Requesters', 1),
      'requestergroup' => $translator->translatePlural('Requester group', 'Requester groups', 1),
      'watcher' => $translator->translatePlural('Watcher', 'Watchers', 1),
      'watchergroup' => $translator->translatePlural('Watcher group', 'Watcher groups', 1),
      'technician' => $translator->translate('Technician'),
      'techniciangroup' => $translator->translate('Technician group'),
      'followups' => 'Followups',
      'problems' => $translator->translatePlural('Problem', 'Problems', 2),
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
    //   'title' => $translator->translate('Assigned to a supplier'),
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
    global $translator;

    return [
      1 => [
        'title' => $translator->translate('Incident'),
      ],
      2 => [
        'title' => $translator->translate('Request'),
      ],
    ];
  }

  /**
   * @return array<int, mixed>
   */
  public static function getStatusArray(): array
  {
    global $translator;

    return [
      1 => [
        'title' => $translator->translate('New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => $translator->translate('Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => $translator->translate('Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => $translator->translate('Closed'),
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
    global $translator;
    return [
      5 => [
        'title' => $translator->translate('urgency' . "\004" . 'Very high'),
      ],
      4 => [
        'title' => $translator->translate('urgency' . "\004" . 'High'),
      ],
      3 => [
        'title' => $translator->translate('urgency' . "\004" . 'Medium'),
      ],
      2 => [
        'title' => $translator->translate('urgency' . "\004" . 'Low'),
      ],
      1 => [
        'title' => $translator->translate('urgency' . "\004" . 'Very low'),
      ],
    ];
  }

  /**
   * @return array<int, mixed>
   */
  public static function getImpactArray(): array
  {
    global $translator;
    return [
      5 => [
        'title' => $translator->translate('impact' . "\004" . 'Very high'),
      ],
      4 => [
        'title' => $translator->translate('impact' . "\004" . 'High'),
      ],
      3 => [
        'title' => $translator->translate('impact' . "\004" . 'Medium'),
      ],
      2 => [
        'title' => $translator->translate('impact' . "\004" . 'Low'),
      ],
      1 => [
        'title' => $translator->translate('impact' . "\004" . 'Very low'),
      ],
    ];
  }

  /**
   * @return array<int, mixed>
   */
  public static function getPriorityArray(): array
  {
    global $translator;
    return [
      6 => [
        'title' => $translator->translate('priority' . "\004" . 'Major'),
        'color' => 'fusionmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => $translator->translate('priority' . "\004" . 'Very high'),
        'color' => 'fusionveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => $translator->translate('priority' . "\004" . 'High'),
        'color' => 'fusionhigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => $translator->translate('priority' . "\004" . 'Medium'),
        'color' => 'fusionmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => $translator->translate('priority' . "\004" . 'Low'),
        'color' => 'fusionlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => $translator->translate('priority' . "\004" . 'Very low'),
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
    global $translator;

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
            $translator->translatePlural('%1$d day %2$dh%3$s', '%1$d days %2$dh%3$s', $day),
            $day,
            $hour,
            $minute
          );
        }
        else
        {
            $values[$i] = sprintf($translator->translatePlural('%d day', '%d days', $day), $day);
        }
      }
      elseif ($hour > 0 || $minute > 0)
      {
        if ($minute < 10)
        {
            $minute = '0' . $minute;
        }

        //TRANS: %1$d the number of hours, %2$s the number of minutes : display 3h15
        $values[$i] = sprintf($translator->translate('%1$dh%2$s'), $hour, $minute);
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Ticket', 'Tickets', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Statistics'),
        'icon' => 'chartline',
        'link' => $rootUrl . '/stats',
      ],
      [
        'title' => $translator->translatePlural('Approval', 'Approvals', 2),
        'icon' => 'thumbs up',
        'link' => $rootUrl . '/approvals',
      ],
      [
        'title' => $translator->translate('Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowbaseitems',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
      ],
      [
        'title' => $translator->translatePlural('Cost', 'Costs', 2),
        'icon' => 'money bill alternate',
        'link' => $rootUrl . '/costs',
      ],
      [
        'title' => $translator->translatePlural('Project', 'Projects', 2),
        'icon' => 'folder open',
        'link' => $rootUrl . '/projects',
        'rightModel' => '\App\Models\Project',
      ],
      [
        'title' => $translator->translatePlural('Project task', 'Project tasks', 2),
        'icon' => 'tasks',
        'link' => $rootUrl . '/projecttasks',
        'rightModel' => '\App\Models\Project',
      ],
      [
        'title' => $translator->translatePlural('Problem', 'Problems', 2),
        'icon' => 'drafting compass',
        'link' => $rootUrl . '/problem',
        'rightModel' => '\App\Models\Problem',
      ],
      [
        'title' => $translator->translatePlural('Change', 'Changes', 2),
        'icon' => 'paint roller',
        'link' => $rootUrl . '/changes',
        'rightModel' => '\App\Models\Change',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
