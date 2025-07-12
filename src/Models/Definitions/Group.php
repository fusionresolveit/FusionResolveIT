<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Group
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'completename' => pgettext('global', 'Complete name'),
      'id' => pgettext('global', 'Id'),
      'child' => pgettext('global', 'As child of'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_requester' => npgettext('ITIL', 'Requester', 'Requesters', 1),
      'is_watcher' => npgettext('ITIL', 'Watcher', 'Watchers', 1),
      'is_assign' => pgettext('group', 'Assigned to'),
      'is_task' => npgettext('ITIL', 'Task', 'Tasks', 1),
      'is_notify' => pgettext('group', 'Can be notified'),
      'is_manager' => pgettext('group', 'Can be manager'),
      'is_itemgroup' => sprintf(
        pgettext('global', '%1$s %2$s'),
        pgettext('group', 'Can contain'),
        npgettext('global', 'Item', 'Items', 2)
      ),
      'is_usergroup' => sprintf(
        pgettext('global', '%1$s %2$s'),
        pgettext('group', 'Can contain'),
        npgettext('global', 'User', 'Users', 2)
      ),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(14, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(1, $t['completename'], 'input', 'completename', fillable: false, readonly: true));
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false));
    $defColl->add(new Def(
      13,
      $t['child'],
      'dropdown_remote',
      'child',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(11, $t['is_requester'], 'boolean', 'is_requester', fillable: true));
    $defColl->add(new Def(212, $t['is_watcher'], 'boolean', 'is_watcher', fillable: true));
    $defColl->add(new Def(12, $t['is_assign'], 'boolean', 'is_assign', fillable: true));
    $defColl->add(new Def(72, $t['is_task'], 'boolean', 'is_task', fillable: true));
    $defColl->add(new Def(20, $t['is_notify'], 'boolean', 'is_notify', fillable: true));
    $defColl->add(new Def(18, $t['is_manager'], 'boolean', 'is_manager', fillable: true));
    $defColl->add(new Def(17, $t['is_itemgroup'], 'boolean', 'is_itemgroup', fillable: true));
    $defColl->add(new Def(15, $t['is_usergroup'], 'boolean', 'is_usergroup', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'entity',
    //   'dbname' => 'entities_id',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $tab[] = [
      'id'   => 'common',
      'name' => __('Characteristics')
    ];

    $tab[] = [
      'id'                => '1',
      'table'              => $this->getTable(),
      'field'              => 'completename',
      'name'               => __('Complete name'),
      'datatype'           => 'itemlink',
      'massiveaction'      => false
    ];

    $tab[] = [
      'id'                => '2',
      'table'              => $this->getTable(),
      'field'              => 'id',
      'name'               => __('ID'),
      'massiveaction'      => false,
      'datatype'           => 'number'
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    if (AuthLDAP::useAuthLdap())
    {
    $tab[] = [
      'id'                 => '3',
      'table'              => $this->getTable(),
      'field'              => 'ldap_field',
      'name'               => __('Attribute of the user containing its groups'),
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '4',
      'table'              => $this->getTable(),
      'field'              => 'ldap_value',
      'name'               => __('Attribute value'),
      'datatype'           => 'text',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '5',
      'table'              => $this->getTable(),
      'field'              => 'ldap_group_dn',
      'name'               => __('Group DN'),
      'datatype'           => 'text',
      'autocomplete'       => true,
    ];
    }

    $tab[] = [
      'id'                 => '70',
      'table'              => 'glpi_users',
      'field'              => 'name',
      'name'               => __('Manager'),
      'datatype'           => 'dropdown',
      'right'              => 'all',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'beforejoin'         => [
      'table'              => 'glpi_groups_users',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => 'AND NEWTABLE.`is_manager` = 1'
      ]
      ]
      ]
    ];

    $tab[] = [
      'id'                 => '71',
      'table'              => 'glpi_users',
      'field'              => 'name',
      'name'               => __('Delegatee'),
      'datatype'           => 'dropdown',
      'right'              => 'all',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'beforejoin'         => [
      'table'              => 'glpi_groups_users',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => 'AND NEWTABLE.`is_userdelegate` = 1'
      ]
      ]
      ]
    ];
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Group', 'Groups', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('group', 'Child groups'),
        'icon' => 'users',
        'link' => $rootUrl . '/groups',
      ],
      [
        'title' => pgettext('group', 'Used items'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => pgettext('group', 'Managed items'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'User', 'Users', 2),
        'icon' => 'user',
        'link' => $rootUrl . '/users',
      ],
      [
        'title' => npgettext('global', 'Notification', 'Notifications', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => gettext('ticket' . "\004" . 'Created tickets'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/tickets',
      ],
      [
        'title' => npgettext('problem', 'Problem', 'Problems', 2),
        'icon' => 'drafting compass',
        'link' => $rootUrl . '/problems',
        'rightModel' => '\App\Models\Problem',
      ],
      [
        'title' => npgettext('change', 'Change', 'Changes', 2),
        'icon' => 'paint roller',
        'link' => $rootUrl . '/changes',
        'rightModel' => '\App\Models\Change',
      ],
      [
        'title' => npgettext('global', 'Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
