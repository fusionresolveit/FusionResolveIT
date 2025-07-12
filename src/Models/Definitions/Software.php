<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Software
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'location' => npgettext('global', 'Location', 'Locations', 1),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'category' => npgettext('global', 'Category', 'Categories', 1),
      'manufacturer' => npgettext('software', 'Publisher', 'Publishers', 1),
      'usertech' => pgettext('inventory device', 'Technician in charge of the hardware'),
      'grouptech' => pgettext('inventory device', 'Group in charge of the hardware'),
      'user' => npgettext('global', 'User', 'Users', 1),
      'group' => npgettext('global', 'Group', 'Groups', 1),
      'is_helpdesk_visible' => pgettext('software', 'Associable to a ticket'),
      'is_valid' => pgettext('software', 'Valid licenses'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      3,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      62,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'softwarecategory_id',
      itemtype: '\App\Models\Softwarecategory',
      fillable: true
    ));
    $defColl->add(new Def(
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer',
      fillable: true
    ));
    $defColl->add(new Def(
      24,
      $t['usertech'],
      'dropdown_remote',
      'usertech',
      dbname: 'user_id_tech',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      49,
      $t['grouptech'],
      'dropdown_remote',
      'grouptech',
      dbname: 'group_id_tech',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(
      70,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      71,
      $t['group'],
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(61, $t['is_helpdesk_visible'], 'boolean', 'is_helpdesk_visible', fillable: true));
    $defColl->add(new Def(63, $t['is_valid'], 'boolean', 'is_valid', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 64,
    //   'title' => pgettext('global', 'Template name'),
    //   'type'  => 'input',
    //   'name'  => 'template_name',
    // ],
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $newtab = [
        'id'                 => '72',
        'table'              => 'glpi_items_softwareversions',
        'field'              => 'id',
        'name'               => _x('quantity', 'Number of installations'),
        'forcegroupby'       => true,
        'usehaving'          => true,
        'datatype'           => 'count',
        'massiveaction'      => false,
        'joinparams'         => [
          'jointype'   => 'child',
          'beforejoin' => [
              'table'      => 'glpi_softwareversions',
              'joinparams' => ['jointype' => 'child'],
          ],
          'condition'  => "AND NEWTABLE.`is_deleted_item` = 0
                            AND NEWTABLE.`is_deleted` = 0
                            AND NEWTABLE.`is_template_item` = 0",
        ]
    ];

    if (Session::getLoginUserID())
    {
        $newtab['joinparams']['condition'] .= getEntitiesRestrictRequest(' AND', 'NEWTABLE');
    }
    $tab[] = $newtab;

    $tab[] = [
        'id'                 => '73',
        'table'              => 'glpi_items_softwareversions',
        'field'              => 'date_install',
        'name'               => __('Installation date'),
        'datatype'           => 'date',
        'massiveaction'      => false,
        'joinparams'         => [
          'jointype'   => 'child',
          'beforejoin' => [
              'table'      => 'glpi_softwareversions',
              'joinparams' => ['jointype' => 'child'],
          ],
          'condition'  => "AND NEWTABLE.`is_deleted_item` = 0
                            AND NEWTABLE.`is_deleted` = 0
                            AND NEWTABLE.`is_template_item` = 0",
        ]
    ];

    $tab = array_merge($tab, Softwarelicense::rawSearchOptionsToAdd());

    $name = _n('Version', 'Versions', Session::getPluralNumber());
    $tab[] = [
        'id'                 => 'versions',
        'name'               => $name
    ];

    $tab[] = [
        'id'                 => '5',
        'table'              => 'glpi_softwareversions',
        'field'              => 'name',
        'name'               => __('Name'),
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'displaywith'        => ['softwares_id'],
        'joinparams'         => [
          'jointype'           => 'child'
        ],
        'datatype'           => 'dropdown'
    ];

    $tab[] = [
        'id'                 => '31',
        'table'              => 'glpi_states',
        'field'              => 'completename',
        'name'               => __('Status'),
        'datatype'           => 'dropdown',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_softwareversions',
              'joinparams'         => [
                'jointype'           => 'child'
              ]
          ]
        ],
    ];

    $tab[] = [
        'id'                 => '170',
        'table'              => 'glpi_softwareversions',
        'field'              => 'comment',
        'name'               => __('Comments'),
        'forcegroupby'       => true,
        'datatype'           => 'text',
        'massiveaction'      => false,
        'joinparams'         => [
          'jointype'           => 'child'
        ]
    ];

    $tab[] = [
        'id'                 => '4',
        'table'              => 'glpi_operatingsystems',
        'field'              => 'name',
        'datatype'           => 'dropdown',
        'name'               => OperatingSystem::getTypeName(1),
        'forcegroupby'       => true,
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_softwareversions',
              'joinparams'         => [
                'jointype'           => 'child'
              ]
          ]
        ],
    ];

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());
    $tab = array_merge($tab, Certificate::rawSearchOptionsToAdd());

*/



/*



    [
      'id'    => 16,
      'title' => npgettext('global', 'Comment', 'Comments', 2),
      'type'  => 'textarea',
      'name'  => 'comment',
    ],
    [
      'id'    => 23,
      'title' => 'Publisher',
      'type'  => 'dropdown_remote',
      'name'  => 'manufacturer',
      'dbname' => 'manufacturers_id',
      'itemtype' => '\App\Models\Manufacturer',
    ],
    [
      'id'    => 24,
      'title' => pgettext('inventory device', 'Technician in charge of the hardware'),
      'type'  => 'dropdown_remote',
      'name'  => 'usertech',
      'dbname' => 'users_id_tech',
      'itemtype' => '\App\Models\User',
    ],
    [
      'id'    => 49,
      'title' => pgettext('inventory device', 'Group in charge of the hardware'),
      'type'  => 'dropdown_remote',
      'name'  => 'grouptech',
      'dbname' => 'groups_id_tech',
      'itemtype' => '\App\Models\Group',
    ],
    [
      'id'    => 70,
      'title' => npgettext('global', 'User', 'Users', 1),
      'type'  => 'dropdown_remote',
      'name'  => 'user',
      'dbname' => 'users_id',
      'itemtype' => '\App\Models\User',
    ],
    [
      'id'    => 71,
      'title' => npgettext('global', 'Group', 'Groups', 1),
      'type'  => 'dropdown_remote',
      'name'  => 'group',
      'dbname' => 'groups_id',
      'itemtype' => '\App\Models\Group',
    ],

    // [
    //   'id'    => 72,
    //   'title' => 'Number of installations',
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'nbinstallation',
    //   'itemtype' => '\App\Models\Softwareversion',
    //   'count' => 'devices_count',
    // ],
    // [
    //   'id'    => 5,
    //   'title' => 'Versions',
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'versions',
    //   'itemtype' => '\App\Models\Softwareversion',
    //   'multiple' => true,
    // ],

    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Software', 'Software', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('global', 'Analysis impact'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Version', 'Versions', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/versions',
      ],
      [
        'title' => npgettext('global', 'License', 'Licenses', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/licenses',
      ],
      [
        'title' => npgettext('software', 'Installation', 'Installations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/softwareinstall',
      ],
      [
        'title' => pgettext('global', 'Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => npgettext('global', 'Contract', 'Contracts', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => pgettext('global', 'Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => pgettext('global', 'ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
      ],
      [
        'title' => npgettext('global', 'External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => npgettext('global', 'Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => npgettext('global', 'Reservation', 'Reservations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/reservations',
      ],
      [
        'title' => npgettext('global', 'Domain', 'Domains', 2),
        'icon' => 'globe americas',
        'link' => $rootUrl . '/domains',
      ],
      [
        'title' => npgettext('global', 'Appliance', 'Appliances', 2),
        'icon' => 'cubes',
        'link' => $rootUrl . '/appliances',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
