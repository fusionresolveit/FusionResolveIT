<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Notificationtemplate
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'itemtype' =>  npgettext('global', 'Type', 'Types', 1),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'css' => pgettext('global', 'CSS'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      4,
      $t['itemtype'],
      'dropdown',
      'itemtype',
      dbname: 'itemtype',
      values: self::getTypeArray(),
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(161, $t['css'], 'textarea', 'css', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getTypeArray(): array
  {
    $types = [];
    $types['CartridgeItem'] = npgettext('global', 'Cartridge model', 'Cartridge models', 1);
    $types['Change'] = npgettext('change', 'Change', 'Changes', 1);
    $types['ConsumableItem'] = npgettext('global', 'Consumable model', 'Consumable models', 1);
    $types['Contract'] = npgettext('global', 'Contract', 'Contracts', 1);
    $types['CronTask'] = npgettext('global', 'Automatic action', 'Automatic actions', 1);
    $types['DBConnection'] = npgettext('global', 'SQL replica', 'SQL replicas', 1);
    $types['FieldUnicity'] = npgettext('global', 'Fields unicity', 'Fields unicity', 1);
    $types['Infocom'] = pgettext('global', 'Financial and administrative information');
    $types['MailCollector'] = npgettext('global', 'Receiver', 'Receivers', 1);
    $types['ObjectLock'] = npgettext('global', 'Object Lock', 'Object Locks', 1);
    $types['PlanningRecall'] = npgettext('global', 'Planning reminder', 'Planning reminders', 1);
    $types['Problem'] = npgettext('problem', 'Problem', 'Problems', 1);
    $types['Project'] = npgettext('global', 'Project', 'Projects', 1);
    $types['ProjectTask'] = npgettext('project', 'Project task', 'Project tasks', 1);
    $types['Reservation'] = npgettext('global', 'Reservation', 'Reservations', 1);
    $types['SoftwareLicense'] = npgettext('global', 'License', 'Licenses', 1);
    $types['Ticket'] = npgettext('ticket', 'Ticket', 'Tickets', 1);
    $types['User'] = npgettext('global', 'User', 'Users', 1);
    $types['SavedSearch_Alert'] = npgettext('notification', 'Saved search alert', 'Saved searches alert', 1);
    $types['Certificate'] = npgettext('global', 'Certificate', 'Certificates', 1);
    $types['Domain'] = npgettext('global', 'Domain', 'Domains', 1);

    asort($types);

    $newTypes = [];
    foreach (array_keys($types) as $key)
    {
      $newTypes[$key]['title'] = $types[$key];
    }

    return $newTypes;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('notification', 'Notification template', 'Notification templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('notification', 'Template translation', 'Template translations', 2),
        'icon' => 'language',
        'link' => $rootUrl . '/templatetranslation',
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
