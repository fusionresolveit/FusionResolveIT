<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Problem
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Title'),
      'content' => pgettext('global', 'Description'),
      'status' => pgettext('global', 'Status'),
      'urgency' => pgettext('ITIL', 'Urgency'),
      'impact' => pgettext('ITIL', 'Impact'),
      'priority' => pgettext('ITIL', 'Priority'),
      'date' => pgettext('ITIL', 'Opening date'),
      'closedate' => pgettext('ITIL', 'Closing date'),
      'time_to_resolve' => pgettext('ITIL', 'Time to resolve'),
      'solvedate' => pgettext('ITIL', 'Resolution date'),
      'updated_at' => pgettext('global', 'Last update'),
      'category' => npgettext('global', 'Category', 'Categories', 1),
      'actiontime' => pgettext('ITIL', 'Total duration'),
      'usersidlastupdater' => pgettext('ITIL', 'Last edit by'),
      'usersidrecipient' => pgettext('ITIL', 'Writer'),
      'impactcontent' => pgettext('ITIL', 'Impacts'),
      'causecontent' => pgettext('problem', 'Causes'),
      'symptomcontent' => pgettext('problem', 'Symptoms'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(21, $t['content'], 'textarea', 'content', fillable: true));
    $defColl->add(new Def(
      12,
      $t['status'],
      'dropdown',
      'status',
      dbname: 'status',
      values: self::getStatusArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      10,
      $t['urgency'],
      'dropdown',
      'urgency',
      dbname: 'urgency',
      values: self::getUrgencyArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      11,
      $t['impact'],
      'dropdown',
      'impact',
      dbname: 'impact',
      values: self::getImpactArray(),
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
    $defColl->add(new Def(15, $t['date'], 'datetime', 'date', fillable: true));
    $defColl->add(new Def(16, $t['closedate'], 'datetime', 'closedate', fillable: true));
    $defColl->add(new Def(18, $t['time_to_resolve'], 'datetime', 'time_to_resolve', fillable: true));
    $defColl->add(new Def(17, $t['solvedate'], 'datetime', 'solvedate', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(
      7,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'category_id',
      itemtype: '\App\Models\Category',
      fillable: true
    ));
    $defColl->add(new Def(
      45,
      $t['actiontime'],
      'dropdown',
      'actiontime',
      dbname: 'actiontime',
      values: self::getTimestampArray(['addfirstminutes' => true]),
      fillable: true
    ));
    $defColl->add(new Def(
      64,
      $t['usersidlastupdater'],
      'dropdown_remote',
      'usersidlastupdater',
      dbname: 'user_id_lastupdater',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      22,
      $t['usersidrecipient'],
      'dropdown_remote',
      'usersidrecipient',
      dbname: 'user_id_recipient',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(60, $t['impactcontent'], 'textarea', 'impactcontent', fillable: true));
    $defColl->add(new Def(61, $t['causecontent'], 'textarea', 'causecontent', fillable: true));
    $defColl->add(new Def(62, $t['symptomcontent'], 'textarea', 'symptomcontent', fillable: true));

    return $defColl;
    // [
    //   'id'    => 82,
    //   'title' => 'Time to resolve exceeded',
    //   'type'  => 'boolean',
    //   'name'  => 'is_late',
    // ],
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
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
      'id'                 => '151',
      'table'              => $this->getTable(),
      'field'              => 'time_to_resolve',
      'name'               => __('Time to resolve + Progress'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'additionalfields'   => ['status']
    ];

    if (!Session::isCron() // no filter for cron
    && Session::getCurrentInterface() == 'helpdesk')
    {
    $newtab['condition']         = ['is_helpdeskvisible' => 1];
    }
    $tab[] = $newtab;


    // Filter search fields for helpdesk
    if (!Session::isCron() // no filter for cron
    && Session::getCurrentInterface() != 'central')
    {
    // last updater no search
    $newtab['nosearch'] = true;
    }
    $tab[] = $newtab;

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    // For ITIL template
    $tab[] = [
      'id'                 => '142',
      'table'              => 'glpi_documents',
      'field'              => 'name',
      'name'               => Document::getTypeName(Session::getPluralNumber()),
      'forcegroupby'       => true,
      'usehaving'          => true,
      'nosearch'           => true,
      'nodisplay'          => true,
      'datatype'           => 'dropdown',
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'items_id',
      'beforejoin'         => [
      'table'              => 'glpi_documents_items',
      'joinparams'         => [
      'jointype'           => 'itemtype_item'
      ]
      ]
      ]
    ];

    $tab[] = [
      'id'                 => '63',
      'table'              => 'glpi_items_problems',
      'field'              => 'id',
      'name'               => _x('quantity', 'Number of items'),
      'forcegroupby'       => true,
      'usehaving'          => true,
      'datatype'           => 'count',
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '13',
      'table'              => 'glpi_items_problems',
      'field'              => 'items_id',
      'name'               => _n('Associated element', 'Associated elements', Session::getPluralNumber()),
      'datatype'           => 'specific',
      'comments'           => true,
      'nosort'             => true,
      'nosearch'           => true,
      'additionalfields'   => ['itemtype'],
      'joinparams'         => [
      'jointype'           => 'child'
      ],
      'forcegroupby'       => true,
      'massiveaction'      => false
    ];

    $tab[] = [
      'id'                 => '131',
      'table'              => 'glpi_items_problems',
      'field'              => 'itemtype',
      'name'               => _n('Associated item type', 'Associated item types', Session::getPluralNumber()),
      'datatype'           => 'itemtypename',
      'itemtype_list'      => 'ticket_types',
      'nosort'             => true,
      'additionalfields'   => ['itemtype'],
      'joinparams'         => [
      'jointype'           => 'child'
      ],
      'forcegroupby'       => true,
      'massiveaction'      => false
    ];

    $tab = array_merge($tab, $this->getSearchOptionsActors());

    $tab[] = [
      'id'                 => 'analysis',
      'name'               => __('Analysis')
    ];

    ];

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

    $tab = array_merge($tab, ITILFollowup::rawSearchOptionsToAdd());

    $tab = array_merge($tab, ProblemTask::rawSearchOptionsToAdd());

    $tab = array_merge($tab, $this->getSearchOptionsSolution());

    $tab = array_merge($tab, $this->getSearchOptionsStats());

    $tab = array_merge($tab, ProblemCost::rawSearchOptionsToAdd());

    $tab[] = [
      'id'                 => 'ticket',
      'name'               => Ticket::getTypeName(Session::getPluralNumber())
    ];

    $tab[] = [
      'id'                 => '141',
      'table'              => 'glpi_problems_tickets',
      'field'              => 'id',
      'name'               => _x('quantity', 'Number of tickets'),
      'forcegroupby'       => true,
      'usehaving'          => true,
      'datatype'           => 'count',
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];
    */
  }

  /**
   * @return array<int, mixed>
   */
  public static function getStatusArray(): array
  {
    return [
      1 => [
        'title' => pgettext('problem status', 'New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => pgettext('general status', 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => pgettext('general status', 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => pgettext('problem status', 'Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => pgettext('problem status', 'Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => pgettext('problem status', 'Closed'),
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
  public static function getDefinitionAnalysis(): array
  {
    return [
      [
        'id'    => 1,
        'title' => pgettext('ITIL', 'Impacts'),
        'type'  => 'textarea',
        'name'  => 'impactcontent',
      ],
      [
        'id'    => 2,
        'title' => pgettext('problem', 'Causes'),
        'type'  => 'textarea',
        'name'  => 'causecontent',
      ],
      [
        'id'    => 3,
        'title' => pgettext('problem', 'Symptoms'),
        'type'  => 'textarea',
        'name'  => 'symptomcontent',
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
        'title' => npgettext('problem', 'Problem', 'Problems', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('problem', 'Processing problem'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => pgettext('ITIL', 'Analysis'),
        'icon' => 'edit',
        'link' => $rootUrl . '/analysis',
      ],
      [
        'title' => pgettext('ITIL', 'Statistics'),
        'icon' => 'chartline',
        'link' => $rootUrl . '/stats',
      ],
      [
        'title' => npgettext('ticket', 'Ticket', 'Tickets', 2),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/tickets',
        'rightModel' => '\App\Models\Ticket',
      ],
      [
        'title' => npgettext('change', 'Change', 'Changes', 2),
        'icon' => 'paint roller',
        'link' => $rootUrl . '/changes',
        'rightModel' => '\App\Models\Change',
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
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
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
