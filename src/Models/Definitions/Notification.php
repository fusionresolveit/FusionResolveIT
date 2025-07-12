<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Notification
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'item_type' =>  npgettext('global', 'Type', 'Types', 1),
      'is_active' => pgettext('global', 'Active'),
      'allow_response' => pgettext('notification', 'Allow response'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(5, $t['item_type'], 'dropdown', 'item_type', values: self::getTypeArray(), fillable: true));
    $defColl->add(new Def(6, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(206, $t['allow_response'], 'boolean', 'allow_response', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    [
    'id'    => 2,
    'title' => 'Event', 'Events', 1,
    'type'  => 'dropdown',
    'name'  => 'event',
    'dbname'  => 'event',
    'values' => self::getEvents(),
    // 'additionalfields'  => 'itemtype',
    ],
    */
  }

  /**
   * @return array<string, mixed>
   */
  public static function getTypeArray(): array
  {
    $types = [
      'CartridgeItem' => [
        'title' => npgettext('global', 'Cartridge model', 'Cartridge models', 1),
      ],
      'Change' => [
        'title' => npgettext('change', 'Change', 'Changes', 1),
      ],
      'ConsumableItem' => [
        'title' => npgettext('global', 'Consumable model', 'Consumable models', 1),
      ],
      'Contract' => [
        'title' => npgettext('global', 'Contract', 'Contracts', 1),
      ],
      'CronTask' => [
        'title' => npgettext('global', 'Automatic action', 'Automatic actions', 1),
      ],
      'DBConnection' => [
        'title' => npgettext('global', 'SQL replica', 'SQL replicas', 1),
      ],
      'FieldUnicity' => [
        'title' => npgettext('global', 'Fields unicity', 'Fields unicity', 1),
      ],
      'Infocom' => [
        'title' => pgettext('global', 'Financial and administrative information'),
      ],
      'MailCollector' => [
        'title' => npgettext('global', 'Receiver', 'Receivers', 1),
      ],
      'ObjectLock' => [
        'title' => npgettext('global', 'Object Lock', 'Object Locks', 1),
      ],
      'PlanningRecall' => [
        'title' => npgettext('global', 'Planning reminder', 'Planning reminders', 1),
      ],
      'Problem' => [
        'title' => npgettext('problem', 'Problem', 'Problems', 1),
      ],
      'Project' => [
        'title' => npgettext('global', 'Project', 'Projects', 1),
      ],
      'ProjectTask' => [
        'title' => npgettext('project', 'Project task', 'Project tasks', 1),
      ],
      'Reservation' => [
        'title' => npgettext('global', 'Reservation', 'Reservations', 1),
      ],
      'SoftwareLicense' => [
        'title' => npgettext('global', 'License', 'Licenses', 1),
      ],
      'App\Models\Ticket' => [
        'title' => npgettext('ticket', 'Ticket', 'Tickets', 1),
      ],
      'User' => [
        'title' => npgettext('global', 'User', 'Users', 1),
      ],
      'SavedSearch_Alert' => [
        'title' => npgettext('notification', 'Saved search alert', 'Saved searches alert', 1),
      ],
      'Certificate' => [
        'title' => npgettext('global', 'Certificate', 'Certificates', 1),
      ],
      'Domain' => [
        'title' => npgettext('global', 'Domain', 'Domains', 1),
      ],
    ];

    return $types;
  }

  /**
   * @return array<mixed>
   */
  public static function getEvents(): array
  {
    $eventsParent = [
      'requester_user'    => pgettext('event', 'New user in requesters'),
      'requester_group'   => pgettext('event', 'New group in requesters'),
      'observer_user'     => pgettext('event', 'New user in observers'),
      'observer_group'    => pgettext('event', 'New group in observers'),
      'assign_user'       => pgettext('event', 'New user in assignees'),
      'assign_group'      => pgettext('event', 'New group in assignees'),
      'assign_supplier'   => pgettext('event', 'New supplier in assignees'),
      'add_task'          => pgettext('event', 'New task'),
      'update_task'       => pgettext('event', 'Update of a task'),
      'delete_task'       => pgettext('event', 'Deletion of a task'),
      'add_followup'      => pgettext('event', 'New followup'),
      'update_followup'   => pgettext('event', 'Update of a followup'),
      'delete_followup'   => pgettext('event', 'Deletion of a followup'),
    ];

    $events['CartridgeItem'] = ['alert' => pgettext('event', 'Cartridges alarm')];
    $events['Certificate'] = ['alert' => pgettext('event', 'Alarms on expired certificates')];

    $events['Change'] = [
      'new'               => pgettext('event', 'New change'),
      'update'            => pgettext('event', 'Update of a change'),
      'solved'            => pgettext('event', 'Change solved'),
      'validation'        => pgettext('event', 'Validation request'),
      'validation_answer' => pgettext('event', 'Validation request answer'),
      'closed'            => pgettext('event', 'Closure of a change'),
      'delete'            => pgettext('event', 'Deleting a change')
    ];
    $events['Change'] = array_merge($events['Change'], $eventsParent);

    $events['ConsumableItem'] = ['alert' => pgettext('event', 'Consumables alarm')];

    $events['Contract'] = [
      'end'               => pgettext('event', 'End of contract'),
      'notice'            => pgettext('event', 'Notice'),
      'periodicity'       => pgettext('event', 'Periodicity'),
      'periodicitynotice' => pgettext('event', 'Periodicity notice')
    ];

    $events['CronTask'] = ['alert' => pgettext('event', 'Monitoring of automatic actions')];

    $events['DBConnection'] = ['desynchronization' => pgettext('event', 'Desynchronization SQL replica')];

    $events['FieldUnicity'] = ['refuse' => pgettext('event', 'Alert on duplicate record')];

    $events['Infocom'] = ['alert' => pgettext('event', 'Alarms on financial and administrative information')];

    $events['MailCollector'] = ['error' => pgettext('event', 'Receiver errors')];

    $events['ObjectLock'] = ['unlock' => pgettext('event', 'Unlock Item Request')];

    $events['PlanningRecall'] = ['planningrecall' => pgettext('event', 'Planning recall')];

    $events['Problem'] = [
      'new'            => pgettext('event', 'New problem'),
      'update'         => pgettext('event', 'Update of a problem'),
      'solved'         => pgettext('event', 'Problem solved'),
      'closed'         => pgettext('event', 'Closure of a problem'),
      'delete'         => pgettext('event', 'Deleting a problem')
    ];
    $events['Problem'] = array_merge($events['Problem'], $eventsParent);

    $events['Project'] = [
      'new'               => pgettext('event', 'New project'),
      'update'            => pgettext('event', 'Update of a project'),
      'delete'            => pgettext('event', 'Deletion of a project')
    ];

    $events['ProjectTask'] = [
      'new'               => pgettext('event', 'New project task'),
      'update'            => pgettext('event', 'Update of a project task'),
      'delete'            => pgettext('event', 'Deletion of a project task')
    ];

    $events['Reservation'] = [
      'new'    => pgettext('event', 'New reservation'),
      'update' => pgettext('event', 'Update of a reservation'),
      'delete' => pgettext('event', 'Deletion of a reservation'),
      'alert'  => pgettext('event', 'Reservation expired')
    ];

    $events['SoftwareLicense'] = ['alert' => pgettext('event', 'Alarms on expired licenses')];

    $events['App\Models\Ticket'] = [
      'new'               => pgettext('event', 'New ticket'),
      'update'            => pgettext('event', 'Update of a ticket'),
      'solved'            => pgettext('event', 'Ticket solved'),
      'rejectsolution'    => pgettext('event', 'Solution rejected'),
      'validation'        => pgettext('event', 'Validation request'),
      'validation_answer' => pgettext('event', 'Validation request answer'),
      'closed'            => pgettext('event', 'Closing of the ticket'),
      'delete'            => pgettext('event', 'Deletion of a ticket'),
      'alertnotclosed'    => pgettext('event', 'Not solved tickets'),
      'recall'            => pgettext('event', 'Automatic reminders of SLAs'),
      'recall_ola'        => pgettext('event', 'Automatic reminders of OLAs'),
      'satisfaction'      => pgettext('event', 'Satisfaction survey'),
      'replysatisfaction' => pgettext('event', 'Satisfaction survey answer')
    ];
    $events['App\Models\Ticket'] = array_merge($events['App\Models\Ticket'], $eventsParent);

    $events['SoftwareLicense'] = ['alert' => pgettext('event', 'Alarms on expired licenses')];

    $events['User'] = [
      'passwordexpires' => pgettext('event', 'Password expires'),
      'passwordforget'  => pgettext('event', 'Forgotten password?'),
    ];

    $events['SavedSearch_Alert'] = ['alert' => pgettext('event', 'Private search alert')];

    $events['Domain'] = [
      'ExpiredDomains'     => pgettext('event', 'Expired domains'),
      'DomainsWhichExpire' => pgettext('event', 'Expiring domains')
    ];


    $newEvents = [];
    foreach (array_keys($events) as $keyItem)
    {
      foreach (array_keys($events[$keyItem]) as $key)
      {
        $newEvents[$keyItem][$key]['title'] = $events[$keyItem][$key];
      }
      asort($newEvents[$keyItem]);
    }

    return $newEvents;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Notification', 'Notifications', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('notification', 'Template translation', 'Template translations', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Notification', 'Notifications', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
