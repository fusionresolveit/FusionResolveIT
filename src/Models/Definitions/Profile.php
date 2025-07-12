<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Profile
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'                    => pgettext('global', 'Name'),
      'is_default'              => pgettext('profile', 'Default profile'),
      'interface'               => pgettext('profile', "Profile's interface"),
      'create_ticket_on_login'  => pgettext('profile', 'Ticket creation form on login'),
      'comment'                 => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at'              => pgettext('global', 'Last update'),
      'created_at'              => pgettext('global', 'Creation date'),
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
    return [
      'central' => [
        'title' => pgettext('profile', 'Standard interface'),
      ],
      'helpdesk' => [
        'title' => pgettext('profile', 'Simplified interface'),
      ]
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Profile', 'Profiles', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('profile', 'Assets'),
        'icon' => 'laptop house',
        'link' => $rootUrl . '/assets',
      ],
      [
        'title' => pgettext('profile', 'Assistance'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/assistance',
      ],
      [
        'title' => npgettext('global', 'Form', 'Forms', 2),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/forms',
      ],
      [
        'title' => pgettext('global', 'Management'),
        'icon' => 'block layout',
        'link' => $rootUrl . '/management',
      ],
      [
        'title' => pgettext('profile', 'Tools'),
        'icon' => 'toolbox',
        'link' => $rootUrl . '/tools',
      ],
      [
        'title' => pgettext('profile', 'Administration'),
        'icon' => 'screwdriver',
        'link' => $rootUrl . '/administration',
      ],
      [
        'title' => pgettext('profile', 'Setup'),
        'icon' => 'tools',
        'link' => $rootUrl . '/setup',
      ],
      [
        'title' => npgettext('global', 'User', 'Users', 2),
        'icon' => 'users',
        'link' => $rootUrl . '/users',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
