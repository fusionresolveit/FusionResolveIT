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
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'comment' => $translator->translate('Comments'),
      'category' => $translator->translate('Category'),
      'manufacturer' => $translator->translatePlural('Publisher', 'Publishers', 1),
      'usertech' => $translator->translate('Technician in charge of the hardware'),
      'grouptech' => $translator->translate('Group in charge of the hardware'),
      'user' => $translator->translatePlural('User', 'Users', 1),
      'group' => $translator->translatePlural('Group', 'Groups', 1),
      'is_helpdesk_visible' => $translator->translate('Associable to a ticket'),
      'is_valid' => $translator->translate('Valid licenses'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translate('Template name'),
    //   'type'  => 'input',
    //   'name'  => 'template_name',
    // ],
    // [
    //   'id'    => 80,
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
      'title' => $translator->translate('Comments'),
      'type'  => 'textarea',
      'name'  => 'comment',
    ],
    [
      'id'    => 23,
      'title' => $translator->translate('Publisher'),
      'type'  => 'dropdown_remote',
      'name'  => 'manufacturer',
      'dbname' => 'manufacturers_id',
      'itemtype' => '\App\Models\Manufacturer',
    ],
    [
      'id'    => 24,
      'title' => $translator->translate('Technician in charge of the hardware'),
      'type'  => 'dropdown_remote',
      'name'  => 'usertech',
      'dbname' => 'users_id_tech',
      'itemtype' => '\App\Models\User',
    ],
    [
      'id'    => 49,
      'title' => $translator->translate('Group in charge of the hardware'),
      'type'  => 'dropdown_remote',
      'name'  => 'grouptech',
      'dbname' => 'groups_id_tech',
      'itemtype' => '\App\Models\Group',
    ],
    [
      'id'    => 70,
      'title' => $translator->translatePlural('User', 'Users', 1),
      'type'  => 'dropdown_remote',
      'name'  => 'user',
      'dbname' => 'users_id',
      'itemtype' => '\App\Models\User',
    ],
    [
      'id'    => 71,
      'title' => $translator->translatePlural('Group', 'Groups', 1),
      'type'  => 'dropdown_remote',
      'name'  => 'group',
      'dbname' => 'groups_id',
      'itemtype' => '\App\Models\Group',
    ],

    // [
    //   'id'    => 72,
    //   'title' => $translator->translate('Number of installations'),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'nbinstallation',
    //   'itemtype' => '\App\Models\Softwareversion',
    //   'count' => 'devices_count',
    // ],
    // [
    //   'id'    => 5,
    //   'title' => $translator->translate('Versions'),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Software', 'Softwares', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Analysis impact'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Version', 'Versions', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/versions',
      ],
      [
        'title' => $translator->translatePlural('License', 'Licenses', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/licenses',
      ],
      [
        'title' => $translator->translatePlural('Installation', 'Installations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/softwareinstall',
      ],
      [
        'title' => $translator->translate('Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => $translator->translatePlural('Contract', 'Contract', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translate('Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowbaseitems',
      ],
      [
        'title' => $translator->translate('ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
      ],
      [
        'title' => $translator->translatePlural('External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translatePlural('Reservation', 'Reservations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/reservations',
      ],
      [
        'title' => $translator->translatePlural('Domain', 'Domains', 2),
        'icon' => 'globe americas',
        'link' => $rootUrl . '/domains',
      ],
      [
        'title' => $translator->translatePlural('Appliance', 'Appliances', 2),
        'icon' => 'cubes',
        'link' => $rootUrl . '/appliances',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
