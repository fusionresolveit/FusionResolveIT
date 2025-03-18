<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Audit
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'type' => $translator->translate('Source'),
      'date' => $translator->translatePlural('Date', 'Dates', 1),
      'service' => $translator->translate('Service'),
      'level' => $translator->translate('Level'),
      'message' => $translator->translate('Message'),
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
    global $translator;
    return [
      'system' => [
        'title' => $translator->translate('System'),
      ],
      'devices' => [
        'title' => $translator->translatePlural('Component', 'Components', 2),
      ],
      'planning' => [
        'title' => $translator->translate('Planning'),
      ],
      'reservation' => [
        'title' => $translator->translatePlural('Reservation', 'Reservations', 2),
      ],
      'dropdown' => [
        'title' => $translator->translatePlural('Dropdown', 'Dropdowns', 2),
      ],
      'rules' => [
        'title' => $translator->translatePlural('Rule', 'Rules', 2),
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getServiceArray(): array
  {
    global $translator;
    return [
      'inventory' => [
        'title' => $translator->translate('Assets'),
      ],
      'tracking' => [
        'title' => $translator->translatePlural('Ticket', 'Tickets', 2),
      ],
      'maintain' => [
        'title' => $translator->translate('Assistance'),
      ],
      'planning' => [
        'title' => $translator->translate('Planning'),
      ],
      'tools' => [
        'title' => $translator->translate('Tools'),
      ],
      'financial' => [
        'title' => $translator->translate('Management'),
      ],
      'login' => [
        'title' => $translator->translatePlural('Connection', 'Connections', 1),
      ],
      'setup' => [
        'title' => $translator->translate('Setup'),
      ],
      'security' => [
        'title' => $translator->translate('Security'),
      ],
      'reservation' => [
        'title' => $translator->translatePlural('Reservation', 'Reservations', 2),
      ],
      'cron' => [
        'title' => $translator->translatePlural('Automatic action', 'Automatic actions', 2),
      ],
      'document' => [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
      ],
      'notification' => [
        'title' => $translator->translatePlural('Notification', 'Notifications', 2),
      ],
      'plugin' => [
        'title' => $translator->translate('Plugin', 'Plugins', 2),
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      // [
      //   'title' => $translator->translate('Historical'),
      //   'icon' => 'history',
      //   'link' => '',
      // ],
    ];
  }
}
