<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Group
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'completename' => $translator->translate('Complete name'),
      'id' => $translator->translate('ID'),
      'child' => $translator->translate('As child of'),
      'comment' => $translator->translate('Comments'),
      'is_requester' => $translator->translatePlural('Requester', 'Requesters', 1),
      'is_watcher' => $translator->translatePlural('Watcher', 'Watchers', 1),
      'is_assign' => $translator->translate('Assigned to'),
      'is_task' => $translator->translatePlural('Task', 'Tasks', 1),
      'is_notify' => $translator->translate('Can be notified'),
      'is_manager' => $translator->translate('Can be manager'),
      'is_itemgroup' => sprintf(
        $translator->translate('%1$s %2$s'),
        $translator->translate('Can contain'),
        $translator->translatePlural('Item', 'Items', 2)
      ),
      'is_usergroup' => sprintf(
        $translator->translate('%1$s %2$s'),
        $translator->translate('Can contain'),
        $translator->translatePlural('User', 'Users', 2)
      ),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Group', 'Groups', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Child groups'),
        'icon' => 'users',
        'link' => $rootUrl . '/groups',
      ],
      [
        'title' => $translator->translate('Used items'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Managed items'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('User', 'Users', 2),
        'icon' => 'user',
        'link' => $rootUrl . '/users',
      ],
      [
        'title' => $translator->translatePlural('Notification', 'Notifications', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Created tickets'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/tickets',
      ],
      [
        'title' => $translator->translatePlural('Problem', 'Problems', 2),
        'icon' => 'drafting compass',
        'link' => $rootUrl . '/problems',
        'rightModel' => '\App\Models\Problem',
      ],
      [
        'title' => $translator->translatePlural('Change', 'Changes', 2),
        'icon' => 'paint roller',
        'link' => $rootUrl . '/changes',
        'rightModel' => '\App\Models\Change',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
