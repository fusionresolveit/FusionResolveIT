<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Notificationtemplate
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'itemtype' => $translator->translatePlural('Type', 'Types', 1),
      'comment' => $translator->translate('Comments'),
      'css' => $translator->translate('CSS'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    global $translator;

    $types = [];
    $types['CartridgeItem'] = $translator->translatePlural('Cartridge model', 'Cartridge models', 1);
    $types['Change'] = $translator->translatePlural('Change', 'Changes', 1);
    $types['ConsumableItem'] = $translator->translatePlural('Consumable model', 'Consumable models', 1);
    $types['Contract'] = $translator->translatePlural('Contract', 'Contracts', 1);
    $types['CronTask'] = $translator->translatePlural('Automatic action', 'Automatic actions', 1);
    $types['DBConnection'] = $translator->translatePlural('SQL replica', 'SQL replicas', 1);
    $types['FieldUnicity'] = $translator->translate('Fields unicity');
    $types['Infocom'] = $translator->translate('Financial and administrative information');
    $types['MailCollector'] = $translator->translatePlural('Receiver', 'Receivers', 1);
    $types['ObjectLock'] = $translator->translatePlural('Object Lock', 'Object Locks', 1);
    $types['PlanningRecall'] = $translator->translatePlural('Planning reminder', 'Planning reminders', 1);
    $types['Problem'] = $translator->translatePlural('Problem', 'Problems', 1);
    $types['Project'] = $translator->translatePlural('Project', 'Projects', 1);
    $types['ProjectTask'] = $translator->translatePlural('Project task', 'Project tasks', 1);
    $types['Reservation'] = $translator->translatePlural('Reservation', 'Reservations', 1);
    $types['SoftwareLicense'] = $translator->translatePlural('License', 'Licenses', 1);
    $types['Ticket'] = $translator->translatePlural('Ticket', 'Tickets', 1);
    $types['User'] = $translator->translatePlural('User', 'Users', 1);
    $types['SavedSearch_Alert'] = $translator->translatePlural('Saved search alert', 'Saved searches alerts', 1);
    $types['Certificate'] = $translator->translatePlural('Certificate', 'Certificates', 1);
    $types['Domain'] = $translator->translatePlural('Domain', 'Domains', 1);

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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Notification template', 'Notification templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Template translation', 'Template translations', 2),
        'icon' => 'language',
        'link' => $rootUrl . '/templatetranslation',
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
