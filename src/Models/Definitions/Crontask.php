<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Crontask
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'frequency' => pgettext('cron', 'Run frequency'),
      'state' => pgettext('global', 'Status'),
      'mode' => pgettext('cron', 'Run mode'),
      'hourmin' => pgettext('cron', 'Begin hour of run period'),
      'hourmax' => pgettext('cron', 'End hour of run period'),
      'logs_lifetime' => pgettext('cron', 'Number of days this action logs are stored'),
      'lastrun' => pgettext('cron', 'Last run'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', readonly: true));
    $defColl->add(new Def(
      6,
      $t['frequency'],
      'dropdown',
      'frequency',
      dbname: 'frequency',
      values: self::getFrequencyArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      4,
      $t['state'],
      'dropdown',
      'state',
      dbname: 'state',
      values: self::getStateArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      5,
      $t['mode'],
      'dropdown',
      'mode',
      dbname: 'mode',
      values: self::getModeArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      17,
      $t['hourmin'],
      'dropdown',
      'hourmin',
      dbname: 'hourmin',
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 24),
      fillable: true
    ));
    $defColl->add(new Def(
      18,
      $t['hourmax'],
      'dropdown',
      'hourmax',
      dbname: 'hourmax',
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 24),
      fillable: true
    ));
    $defColl->add(new Def(
      19,
      $t['logs_lifetime'],
      'dropdown',
      'logs_lifetime',
      dbname: 'logs_lifetime',
      values: \App\v1\Controllers\Dropdown::generateNumbers(10, 360, 10, [0 => pgettext('cron', 'Infinite')]),
      fillable: true
    ));
    $defColl->add(new Def(7, $t['lastrun'], 'datetime', 'lastrun', readonly: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(20, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;

    /*
    $tab[] = [
      'id'                 => '3',
      'table'              => $this->getTable(),
      'field'              => 'description',
      'name'               => __('Description'),
      'nosearch'           => true,
      'nosort'             => true,
      'massiveaction'      => false,
      'datatype'           => 'text',
      'computation'        => $DB->quoteName('TABLE.id') // Virtual data
    ];

    $tab[] = [
      'id'                 => '8',
      'table'              => $this->getTable(),
      'field'              => 'itemtype',
      'name'               => __('Item type'),
      'massiveaction'      => false,
      'datatype'           => 'itemtypename',
      'types'              => self::getUsedItemtypes()
    ];
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getFrequencyArray(): array
  {
    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;

    $tab = [];

    $tab[$MINUTE_TIMESTAMP] = sprintf(npgettext('cron', '%d minute', '%d minutes', 1), 1);

    // Minutes
    for ($i = 5; $i < 60; $i += 5)
    {
      $tab[$i * $MINUTE_TIMESTAMP] = sprintf(npgettext('cron', '%d minute', '%d minutes', $i), $i);
    }

    // Heures
    for ($i = 1; $i < 24; $i++)
    {
      $tab[$i * $HOUR_TIMESTAMP] = sprintf(npgettext('cron', '%d hour', '%d hours', $i), $i);
    }

    // Jours
    $tab[$DAY_TIMESTAMP] = pgettext('cron', 'Each day');
    for ($i = 2; $i < 7; $i++)
    {
      $tab[$i * $DAY_TIMESTAMP] = sprintf(npgettext('global', '%d day', '%d days', $i), $i);
    }

    $tab[$WEEK_TIMESTAMP]  = pgettext('cron', 'Each week');
    $tab[$MONTH_TIMESTAMP] = pgettext('cron', 'Each month');

    $newTab = [];
    foreach (array_keys($tab) as $key)
    {
      $newTab[$key]['title'] = $tab[$key];
    }

    return $newTab;
  }

  /**
   * @return array<int, mixed>
   */
  public static function getStateArray(): array
  {
    return [
      0 => [
        'title' => pgettext('cron state', 'Disabled'),
      ],
      1 => [
        'title' => pgettext('cron state', 'Scheduled'),
      ],
      2 => [
        'title' => pgettext('cron state', 'Running'),
      ],
    ];
  }

  /**
   * @return array<int, mixed>
   */
  public static function getModeArray(): array
  {
    return [
      1 => [
        'title' => pgettext('cron', 'Web browsing'),
      ],
      2 => [
        'title' => pgettext('cron', 'CLI'),
      ],
    ];
  }

  /*
    ['CartridgeItem']['description' => __('Send alarms on cartridges')]
    ['Certificate']['description' => __('Send alarms on expired certificate')];
    ['ConsumableItem']['description' => __('Send alarms on consumables')];
    ['Contract']['description' => __('Send alarms on contracts')];
    ['Infocom']['description' => __('Send alarms on financial and administrative information')];
    ['PurgeLogs']['description' => __("Purge history")];
    ['ReservationItem']['description' => __('Alerts on reservations')];
    ['SoftwareLicense'][['description' => __('Send alarms on expired licenses')];


    CronTask
    case 'checkupdate' :
    return ['description' => __('Check for new updates')];

    case 'logs' :
    return ['description' => __('Clean old logs'),
    'parameter'
    => __('System logs retention period (in days, 0 for infinite)')];

    case 'session' :
    return ['description' => __('Clean expired sessions')];

    case 'graph' :
    return ['description' => __('Clean generated graphics')];

    case 'temp' :
    return ['description' => __('Clean temporary files')];

    case 'watcher' :
    return ['description' => __('Monitoring of automatic actions')];

    case 'circularlogs' :
    return ['description' => __("Archives log files and deletes aging ones"),
    'parameter'   => __("Number of days to keep archived logs")];

    DBConnection
    return ['description' => __('Check the SQL replica'),
    'parameter'   => __('Max delay between master and slave (minutes)')];
    Document
    case 'cleanorphans' :
    return ['description' => __('Clean orphaned documents')];
    Domain
    case 'DomainsAlert':
    return ['description' => __('Expired or expiring domains')];
    case 'mailgate' :
    return ['description' => __('Retrieve email (Mails receivers)'),
    'parameter'   => __('Number of emails to retrieve')];
    MailCollector
    case 'mailgateerror' :
    return ['description' => __('Send alarms on receiver errors')];
    ObjectLock
    case 'unlockobject' :
    return ['description' => __('Unlock forgotten locked objects'),
    'parameter'   => __('Timeout to force unlock (hours)')];
    OlaLevel_Ticket
    case 'olaticket' :
    return ['description' => __('Automatic actions of OLA')];
    PlanningRecall
    case 'planningrecall' :
    return ['description' => __('Send planning recalls')];
    case 'queuednotification' :
    return ['description' => __('Send mails in queue'),
    'parameter'   => __('Maximum emails to send at once')];
    QueuedNotification
    case 'queuednotificationclean' :
    return ['description' => __('Clean notification queue'),
    'parameter'   => __('Days to keep sent emails')];
    SavedSearch_Alert
    case 'send' :
    return ['description' => __('Saved searches alerts')];
    SavedSearch
    case 'countAll' :
    return ['description' => __('Update all bookmarks execution time')];
    SlaLevel_Ticket
    case 'slaticket' :
    return ['description' => __('Automatic actions of SLA')];
    Telemetry
    case 'telemetry' :
    return ['description' => __('Send telemetry information')];
    Ticket
    case 'closeticket' :
    return ['description' => __('Automatic tickets closing')];
    case 'alertnotclosed' :
    return ['description' => __('Not solved tickets')];
    case 'createinquest' :
    return ['description' => __('Generation of satisfaction surveys')];
    case 'purgeticket':
    return ['description' => __('Automatic closed tickets purge')];
    Ticketrecurrent
    case 'ticketrecurrent' :
    return ['description' => self::getTypeName(Session::getPluralNumber())];
    User
    case 'passwordexpiration':
    $info = [
    'description' => __('Handle users passwords expiration policy'),
    'parameter'   => __('Maximum expiration notifications to send at once'),
    ];
  */

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Automatic action', 'Automatic actions', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('cron', 'Execution', 'Executions', 2),
        'icon' => 'cogs',
        'link' => $rootUrl . '/executions',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
