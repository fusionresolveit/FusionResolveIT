<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Audit
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'type' => pgettext('audit', 'Source'),
      'date' => npgettext('global', 'Date', 'Dates', 1),
      'service' => pgettext('audit', 'Service'),
      'level' => pgettext('audit', 'Level'),
      'message' => pgettext('audit', 'Message'),
    ];

    $defColl = new DefinitionCollection();

    $defColl->add(new Def(11, $t['date'], 'datetime', 'created_at', readonly: true));
    $defColl->add(new Def(14, $t['message'], 'input', 'message', readonly: true));
    $defColl->add(new Def(1001, 'action', 'input', 'action', readonly: true));
    $defColl->add(new Def(1008, 'subaction', 'input', 'subaction', readonly: true));
    $defColl->add(new Def(
      1002,
      'user',
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      readonly: true
    ));
    $defColl->add(new Def(1003, 'username', 'input', 'username', readonly: true));
    $defColl->add(new Def(1004, 'ip', 'input', 'ip', readonly: true));
    $defColl->add(new Def(1005, 'HTTP method', 'input', 'httpmethod', readonly: true));
    $defColl->add(new Def(1006, 'endpoint', 'input', 'endpoint', readonly: true));
    $defColl->add(new Def(1007, 'HTTP code', 'input', 'httpcode', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getTypeArray(): array
  {
    return [
      'system' => [
        'title' => pgettext('audit', 'System'),
      ],
      'devices' => [
        'title' => npgettext('global', 'Component', 'Components', 2),
      ],
      'planning' => [
        'title' => pgettext('calendar', 'Planning'),
      ],
      'reservation' => [
        'title' => npgettext('global', 'Reservation', 'Reservations', 2),
      ],
      'dropdown' => [
        'title' => npgettext('form', 'Dropdown', 'Dropdowns', 2),
      ],
      'rules' => [
        'title' => npgettext('rule', 'Rule', 'Rules', 2),
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getServiceArray(): array
  {
    return [
      'inventory' => [
        'title' => pgettext('profile', 'Assets'),
      ],
      'tracking' => [
        'title' => npgettext('ticket', 'Ticket', 'Tickets', 2),
      ],
      'maintain' => [
        'title' => pgettext('profile', 'Assistance'),
      ],
      'planning' => [
        'title' => pgettext('calendar', 'Planning'),
      ],
      'tools' => [
        'title' => pgettext('profile', 'Tools'),
      ],
      'financial' => [
        'title' => pgettext('global', 'Management'),
      ],
      'login' => [
        'title' => npgettext('inventory device', 'Connection', 'Connections', 1),
      ],
      'setup' => [
        'title' => pgettext('profile', 'Setup'),
      ],
      'security' => [
        'title' => pgettext('audit', 'Security'),
      ],
      'reservation' => [
        'title' => npgettext('global', 'Reservation', 'Reservations', 2),
      ],
      'cron' => [
        'title' => npgettext('global', 'Automatic action', 'Automatic actions', 2),
      ],
      'document' => [
        'title' => npgettext('global', 'Document', 'Documents', 2),
      ],
      'notification' => [
        'title' => npgettext('global', 'Notification', 'Notifications', 2),
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      // [
      //   'title' => npgettext('global', 'Historical', 'Historicals', 1),
      //   'icon' => 'history',
      //   'link' => '',
      // ],
    ];
  }
}
