<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Notification
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'item_type' => $translator->translatePlural('Type', 'Types', 1),
      'is_active' => $translator->translate('Active'),
      'allow_response' => $translator->translate('Allow response'),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    [
    'id'    => 2,
    'title' => $translator->translatePlural('Event', 'Events', 1),
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
    global $translator;

    $types = [
      'CartridgeItem' => [
        'title' => $translator->translatePlural('Cartridge model', 'Cartridge models', 1),
      ],
      'Change' => [
        'title' => $translator->translatePlural('Change', 'Changes', 1),
      ],
      'ConsumableItem' => [
        'title' => $translator->translatePlural('Consumable model', 'Consumable models', 1),
      ],
      'Contract' => [
        'title' => $translator->translatePlural('Contract', 'Contracts', 1),
      ],
      'CronTask' => [
        'title' => $translator->translatePlural('Automatic action', 'Automatic actions', 1),
      ],
      'DBConnection' => [
        'title' => $translator->translatePlural('SQL replica', 'SQL replicas', 1),
      ],
      'FieldUnicity' => [
        'title' => $translator->translate('Fields unicity'),
      ],
      'Infocom' => [
        'title' => $translator->translate('Financial and administrative information'),
      ],
      'MailCollector' => [
        'title' => $translator->translatePlural('Receiver', 'Receivers', 1),
      ],
      'ObjectLock' => [
        'title' => $translator->translatePlural('Object Lock', 'Object Locks', 1),
      ],
      'PlanningRecall' => [
        'title' => $translator->translatePlural('Planning reminder', 'Planning reminders', 1),
      ],
      'Problem' => [
        'title' => $translator->translatePlural('Problem', 'Problems', 1),
      ],
      'Project' => [
        'title' => $translator->translatePlural('Project', 'Projects', 1),
      ],
      'ProjectTask' => [
        'title' => $translator->translatePlural('Project task', 'Project tasks', 1),
      ],
      'Reservation' => [
        'title' => $translator->translatePlural('Reservation', 'Reservations', 1),
      ],
      'SoftwareLicense' => [
        'title' => $translator->translatePlural('License', 'Licenses', 1),
      ],
      'App\Models\Ticket' => [
        'title' => $translator->translatePlural('Ticket', 'Tickets', 1),
      ],
      'User' => [
        'title' => $translator->translatePlural('User', 'Users', 1),
      ],
      'SavedSearch_Alert' => [
        'title' => $translator->translatePlural('Saved search alert', 'Saved searches alerts', 1),
      ],
      'Certificate' => [
        'title' => $translator->translatePlural('Certificate', 'Certificates', 1),
      ],
      'Domain' => [
        'title' => $translator->translatePlural('Domain', 'Domains', 1),
      ],
    ];

    return $types;
  }

  /**
   * @return array<mixed>
   */
  public static function getEvents(): array
  {
    global $translator;

    $eventsParent = [
      'requester_user'    => $translator->translate('New user in requesters'),
      'requester_group'   => $translator->translate('New group in requesters'),
      'observer_user'     => $translator->translate('New user in observers'),
      'observer_group'    => $translator->translate('New group in observers'),
      'assign_user'       => $translator->translate('New user in assignees'),
      'assign_group'      => $translator->translate('New group in assignees'),
      'assign_supplier'   => $translator->translate('New supplier in assignees'),
      'add_task'          => $translator->translate('New task'),
      'update_task'       => $translator->translate('Update of a task'),
      'delete_task'       => $translator->translate('Deletion of a task'),
      'add_followup'      => $translator->translate("New followup"),
      'update_followup'   => $translator->translate('Update of a followup'),
      'delete_followup'   => $translator->translate('Deletion of a followup'),
    ];

    $events['CartridgeItem'] = ['alert' => $translator->translate('Cartridges alarm')];
    $events['Certificate'] = ['alert' => $translator->translate('Alarms on expired certificates')];

    $events['Change'] = [
      'new'               => $translator->translate('New change'),
      'update'            => $translator->translate('Update of a change'),
      'solved'            => $translator->translate('Change solved'),
      'validation'        => $translator->translate('Validation request'),
      'validation_answer' => $translator->translate('Validation request answer'),
      'closed'            => $translator->translate('Closure of a change'),
      'delete'            => $translator->translate('Deleting a change')
    ];
    $events['Change'] = array_merge($events['Change'], $eventsParent);

    $events['ConsumableItem'] = ['alert' => $translator->translate('Consumables alarm')];

    $events['Contract'] = [
      'end'               => $translator->translate('End of contract'),
      'notice'            => $translator->translate('Notice'),
      'periodicity'       => $translator->translate('Periodicity'),
      'periodicitynotice' => $translator->translate('Periodicity notice')
    ];

    $events['CronTask'] = ['alert' => $translator->translate('Monitoring of automatic actions')];

    $events['DBConnection'] = ['desynchronization' => $translator->translate('Desynchronization SQL replica')];

    $events['FieldUnicity'] = ['refuse' => $translator->translate('Alert on duplicate record')];

    $events['Infocom'] = ['alert' => $translator->translate('Alarms on financial and administrative information')];

    $events['MailCollector'] = ['error' => $translator->translate('Receiver errors')];

    $events['ObjectLock'] = ['unlock' => $translator->translate('Unlock Item Request')];

    $events['PlanningRecall'] = ['planningrecall' => $translator->translate('Planning recall')];

    $events['Problem'] = [
      'new'            => $translator->translate('New problem'),
      'update'         => $translator->translate('Update of a problem'),
      'solved'         => $translator->translate('Problem solved'),
      'closed'         => $translator->translate('Closure of a problem'),
      'delete'         => $translator->translate('Deleting a problem')
    ];
    $events['Problem'] = array_merge($events['Problem'], $eventsParent);

    $events['Project'] = [
      'new'               => $translator->translate('New project'),
      'update'            => $translator->translate('Update of a project'),
      'delete'            => $translator->translate('Deletion of a project')
    ];

    $events['ProjectTask'] = [
      'new'               => $translator->translate('New project task'),
      'update'            => $translator->translate('Update of a project task'),
      'delete'            => $translator->translate('Deletion of a project task')
    ];

    $events['Reservation'] = [
      'new'    => $translator->translate('New reservation'),
      'update' => $translator->translate('Update of a reservation'),
      'delete' => $translator->translate('Deletion of a reservation'),
      'alert'  => $translator->translate('Reservation expired')
    ];

    $events['SoftwareLicense'] = ['alert' => $translator->translate('Alarms on expired licenses')];

    $events['App\Models\Ticket'] = [
      'new'               => $translator->translate('New ticket'),
      'update'            => $translator->translate('Update of a ticket'),
      'solved'            => $translator->translate('Ticket solved'),
      'rejectsolution'    => $translator->translate('Solution rejected'),
      'validation'        => $translator->translate('Validation request'),
      'validation_answer' => $translator->translate('Validation request answer'),
      'closed'            => $translator->translate('Closing of the ticket'),
      'delete'            => $translator->translate('Deletion of a ticket'),
      'alertnotclosed'    => $translator->translate('Not solved tickets'),
      'recall'            => $translator->translate('Automatic reminders of SLAs'),
      'recall_ola'        => $translator->translate('Automatic reminders of OLAs'),
      'satisfaction'      => $translator->translate('Satisfaction survey'),
      'replysatisfaction' => $translator->translate('Satisfaction survey answer')
    ];
    $events['App\Models\Ticket'] = array_merge($events['App\Models\Ticket'], $eventsParent);

    $events['SoftwareLicense'] = ['alert' => $translator->translate('Alarms on expired licenses')];

    $events['User'] = [
      'passwordexpires' => $translator->translate('Password expires'),
      'passwordforget'  => $translator->translate('Forgotten password?'),
    ];

    $events['SavedSearch_Alert'] = ['alert' => $translator->translate('Private search alert')];

    $events['Domain'] = [
      'ExpiredDomains'     => $translator->translate('Expired domains'),
      'DomainsWhichExpire' => $translator->translate('Expiring domains')
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Notification', 'Notifications', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Template translation', 'Template translations', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Notification', 'Notifications', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
