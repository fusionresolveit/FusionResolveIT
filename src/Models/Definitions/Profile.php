<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Profile
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'is_default' => $translator->translate('Default profile'),
      'interface' => $translator->translate("Profile's interface"),
      'create_ticket_on_login' => $translator->translate('Ticket creation form on login'),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(3, $t['is_default'], 'boolean', 'is_default', fillable: true));
    $defColl->add(new Def(
      5,
      $t['interface'],
      'dropdown',
      'interface',
      values: self::getInterfaceArray(),
      fillable: true
    ));
    $defColl->add(new Def(118, $t['create_ticket_on_login'], 'boolean', 'create_ticket_on_login', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getInterfaceArray(): array
  {
    global $translator;
    return [
      'central' => [
        'title' => $translator->translate('Standard interface'),
      ],
      'helpdesk' => [
        'title' => $translator->translate('Simplified interface'),
      ]
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Profile', 'Profiles', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Assets'),
        'icon' => 'laptop house',
        'link' => $rootUrl . '/assets',
      ],
      [
        'title' => $translator->translate('Assistance'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/assistance',
      ],
      [
        'title' => $translator->translatePlural('Form', 'Forms', 2),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/forms',
      ],
      [
        'title' => $translator->translate('Management'),
        'icon' => 'block layout',
        'link' => $rootUrl . '/management',
      ],
      [
        'title' => $translator->translate('Tools'),
        'icon' => 'toolbox',
        'link' => $rootUrl . '/tools',
      ],
      [
        'title' => $translator->translate('Administration'),
        'icon' => 'screwdriver',
        'link' => $rootUrl . '/administration',
      ],
      [
        'title' => $translator->translate('Setup'),
        'icon' => 'tools',
        'link' => $rootUrl . '/setup',
      ],
      [
        'title' => $translator->translatePlural('User', 'Users', 2),
        'icon' => 'users',
        'link' => $rootUrl . '/users',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
