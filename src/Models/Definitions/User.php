<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class User
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Login'),
      'id' => $translator->translate('ID'),
      'lastname' => $translator->translate('Last name'),
      'firstname' => $translator->translate('First name'),
      'is_active' => $translator->translate('Active'),
      'begin_date' => $translator->translate('Valid since'),
      'end_date' => $translator->translate('Valid until'),
      'phone' => $translator->translatePlural('Phone', 'Phones', 1),
      'phone2' => $translator->translate('Phone 2'),
      'mobile' => $translator->translate('Mobile phone'),
      'registration_number' => $translator->translate('Administrative number'),
      'category' => $translator->translate('Category'),
      'comment' => $translator->translate('Comments'),
      'title' => $translator->translate('Title'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'profile' => $translator->translate('Default profile'),
      'defaultgroup' => $translator->translate('Default group'),
      'entity' => $translator->translate('Default entity'),
      'supervisor' => $translator->translate('Responsible'),
      'user_dn' => $translator->translate('User DN'),
      'is_deleted_ldap' => $translator->translate('Deleted user in LDAP directory'),
      'personal_token' => $translator->translate('Personal token'),
      'api_token' => $translator->translate('API token'),
      'sync_field' => $translator->translate('Synchronization field'),
      'synchronized_at' => $translator->translate('Last synchronization'),
      'last_login' => $translator->translate('Last login'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
      'completename' => $translator->translate('Complete name'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false));
    $defColl->add(new Def(34, $t['lastname'], 'input', 'lastname', fillable: true));
    $defColl->add(new Def(9, $t['firstname'], 'input', 'firstname', fillable: true));
    $defColl->add(new Def(8, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(62, $t['begin_date'], 'datetime', 'begin_date', fillable: true));
    $defColl->add(new Def(63, $t['end_date'], 'datetime', 'end_date', fillable: true));
    $defColl->add(new Def(6, $t['phone'], 'input', 'phone', fillable: true));
    $defColl->add(new Def(10, $t['phone2'], 'input', 'phone2', fillable: true));
    $defColl->add(new Def(11, $t['mobile'], 'input', 'mobile', fillable: true));
    $defColl->add(new Def(22, $t['registration_number'], 'input', 'registration_number', fillable: true));
    $defColl->add(new Def(
      82,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'usercategory_id',
      itemtype: '\App\Models\Usercategory',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      81,
      $t['title'],
      'dropdown_remote',
      'title',
      dbname: 'usertitle_id',
      itemtype: '\App\Models\Usertitle',
      fillable: true
    ));
    $defColl->add(new Def(
      3,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));
    $defColl->add(new Def(
      79,
      $t['profile'],
      'dropdown_remote',
      'profile',
      dbname: 'profile_id',
      itemtype: '\App\Models\Profile',
      fillable: true
    ));
    $defColl->add(new Def(
      277,
      $t['defaultgroup'],
      'dropdown_remote',
      'defaultgroup',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(
      77,
      $t['entity'],
      'dropdown_remote',
      'entity',
      dbname: 'entity_id',
      itemtype: '\App\Models\Entity',
      fillable: true
    ));
    $defColl->add(new Def(
      99,
      $t['supervisor'],
      'dropdown_remote',
      'supervisor',
      dbname: 'user_id_supervisor',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(21, $t['user_dn'], 'textarea', 'user_dn', fillable: true));
    $defColl->add(new Def(24, $t['is_deleted_ldap'], 'boolean', 'is_deleted_ldap', fillable: true));
    $defColl->add(new Def(224, $t['personal_token'], 'input', 'personal_token', fillable: true));
    $defColl->add(new Def(225, $t['api_token'], 'input', 'api_token', fillable: true));
    $defColl->add(new Def(28, $t['sync_field'], 'input', 'sync_field', fillable: true));
    $defColl->add(new Def(23, $t['synchronized_at'], 'datetime', 'synchronized_at', readonly: true));
    $defColl->add(new Def(14, $t['last_login'], 'datetime', 'last_login', readonly: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));
    $defColl->add(new Def(400, $t['completename'], 'input', 'completename', fillable: false, readonly: true));

    return $defColl;
    // [
    //   'id'    => 150,
    //   'title' => $translator->translate('Picture'),
    //   'type'  => 'file',
    //   'name'  => 'picture',
    //   'fillable' => true,
    // ],


    /*

    $tab[] = [
        'id'                 => 'common',
        'name'               => __('Characteristics')
    ];
    $tab[] = [
        'id'                 => '2',
        'table'              => $this->getTable(),
        'field'              => 'id',
        'name'               => __('ID'),
        'massiveaction'      => false,
        'datatype'           => 'number'
    ];

    $tab[] = [
        'id'                 => '5',
        'table'              => 'glpi_useremails',
        'field'              => 'email',
        'name'               => _n('Email', 'Emails', Session::getPluralNumber()),
        'datatype'           => 'email',
        'joinparams'         => [
          'jointype'           => 'child'
        ],
        'forcegroupby'       => true,
        'massiveaction'      => false
    ];

    $tab[] = [
        'id'                 => '150',
        'table'              => $this->getTable(),
        'field'              => 'picture',
        'name'               => __('Picture'),
        'datatype'           => 'specific',
        'nosearch'           => true,
        'massiveaction'      => false
    ];




    $tab[] = [
        'id'                 => '13',
        'table'              => 'glpi_groups',
        'field'              => 'completename',
        'name'               => Group::getTypeName(Session::getPluralNumber()),
        'forcegroupby'       => true,
        'datatype'           => 'itemlink',
        'massiveaction'      => false,
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_groups_users',
              'joinparams'         => [
                'jointype'           => 'child'
              ]
          ]
        ]
    ];


    $tab[] = [
        'id'                 => '15',
        'table'              => $this->getTable(),
        'field'              => 'authtype',
        'name'               => __('Authentication'),
        'massiveaction'      => false,
        'datatype'           => 'specific',
        'searchtype'         => 'equals',
        'additionalfields'   => [
          '0'                  => 'auths_id'
        ]
    ];

    $tab[] = [
        'id'                 => '30',
        'table'              => 'glpi_authldaps',
        'field'              => 'name',
        'linkfield'          => 'auths_id',
        'name'               => __('LDAP directory for authentication'),
        'massiveaction'      => false,
        'joinparams'         => [
            'condition'          => 'AND REFTABLE.`authtype` = ' . Auth::LDAP
        ],
        'datatype'           => 'dropdown'
    ];

    $tab[] = [
        'id'                 => '31',
        'table'              => 'glpi_authmails',
        'field'              => 'name',
        'linkfield'          => 'auths_id',
        'name'               => __('Email server for authentication'),
        'massiveaction'      => false,
        'joinparams'         => [
          'condition'          => 'AND REFTABLE.`authtype` = ' . Auth::MAIL
        ],
        'datatype'           => 'dropdown'
    ];


    $tab[] = [
        'id'                 => '17',
        'table'              => $this->getTable(),
        'field'              => 'language',
        'name'               => __('Language'),
        'datatype'           => 'language',
        'display_emptychoice' => true,
        'emptylabel'         => 'Default value'
    ];


    $tab[] = [
        'id'                 => '20',
        'table'              => 'glpi_profiles',
        'field'              => 'name',
        'name'               => sprintf(__('%1$s (%2$s)'), Profile::getTypeName(Session::getPluralNumber()),
                                                Entity::getTypeName(1)),
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'datatype'           => 'dropdown',
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_profiles_users',
              'joinparams'         => [
                'jointype'           => 'child'
              ]
          ]
        ]
    ];


    $tab[] = [
        'id'                 => '80',
        'table'              => 'glpi_entities',
        'linkfield'          => 'entities_id',
        'field'              => 'completename',
        'name'               => sprintf(__('%1$s (%2$s)'), Entity::getTypeName(Session::getPluralNumber()),
                                                Profile::getTypeName(1)),
        'forcegroupby'       => true,
        'datatype'           => 'dropdown',
        'massiveaction'      => false,
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_profiles_users',
              'joinparams'         => [
                'jointype'           => 'child'
              ]
          ]
        ]
    ];


    $tab[] = [
        'id'                 => '60',
        'table'              => 'glpi_tickets',
        'field'              => 'id',
        'name'               => __('Number of tickets as requester'),
        'forcegroupby'       => true,
        'usehaving'          => true,
        'datatype'           => 'count',
        'massiveaction'      => false,
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_tickets_users',
              'joinparams'         => [
                'jointype'           => 'child',
                'condition'          => 'AND NEWTABLE.`type` = ' . CommonITILActor::REQUESTER
              ]
          ]
        ]
    ];

    $tab[] = [
        'id'                 => '61',
        'table'              => 'glpi_tickets',
        'field'              => 'id',
        'name'               => __('Number of written tickets'),
        'forcegroupby'       => true,
        'usehaving'          => true,
        'datatype'           => 'count',
        'massiveaction'      => false,
        'joinparams'         => [
          'jointype'           => 'child',
          'linkfield'          => 'users_id_recipient'
        ]
    ];

    $tab[] = [
        'id'                 => '64',
        'table'              => 'glpi_tickets',
        'field'              => 'id',
        'name'               => __('Number of assigned tickets'),
        'forcegroupby'       => true,
        'usehaving'          => true,
        'datatype'           => 'count',
        'massiveaction'      => false,
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_tickets_users',
              'joinparams'         => [
                'jointype'           => 'child',
                'condition'          => 'AND NEWTABLE.`type` = '.CommonITILActor::ASSIGN
              ]
          ]
        ]
    ];


    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

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
        'title' => $translator->translatePlural('User', 'Users', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Authorization', 'Authorizations', 2),
        'icon' => 'user lock',
        'link' => $rootUrl . '/authorization',
      ],
      [
        'title' => $translator->translatePlural('Group', 'Groups', 2),
        'icon' => 'users',
        'link' => $rootUrl . '/groups',
      ],
      [
        'title' => $translator->translate('Settings'),
        'icon' => 'caret square down outline',
        'link' => '',
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
        'title' => $translator->translate('Created tickets'),
        'icon' => 'caret square down outline',
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
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('Reservation', 'Reservations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/reservations',
      ],
      [
        'title' => $translator->translate('Synchronization'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => $translator->translatePlural('Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
