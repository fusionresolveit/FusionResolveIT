<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Supplier
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'          => pgettext('global', 'Name'),
      'address'       => pgettext('location', 'Address'),
      'fax'           => pgettext('global', 'Fax'),
      'town'          => pgettext('location', 'City'),
      'postcode'      => pgettext('location', 'Postal code'),
      'state'         => pgettext('location', 'State'),
      'country'       => pgettext('location', 'Country'),
      'website'       => pgettext('global', 'Website'),
      'phonenumber'   => npgettext('user parameter', 'Phone number', 'Phone numbers', 1),
      'email'         => npgettext('global', 'Email', 'Emails', 1),
      'type'          => npgettext('supplier', 'Third party type', 'Third party types', 1),
      'comment'       => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive'  => pgettext('global', 'Child entities'),
      'updated_at'    => pgettext('global', 'Last update'),
      'created_at'    => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(3, $t['address'], 'textarea', 'address', fillable: true));
    $defColl->add(new Def(10, $t['fax'], 'input', 'fax', fillable: true));
    $defColl->add(new Def(11, $t['town'], 'input', 'town', fillable: true));
    $defColl->add(new Def(14, $t['postcode'], 'input', 'postcode', fillable: true));
    $defColl->add(new Def(12, $t['state'], 'input', 'state', fillable: true));
    $defColl->add(new Def(13, $t['country'], 'input', 'country', fillable: true));
    $defColl->add(new Def(4, $t['website'], 'input', 'website', fillable: true));
    $defColl->add(new Def(5, $t['phonenumber'], 'input', 'phonenumber', fillable: true));
    $defColl->add(new Def(6, $t['email'], 'email', 'email', fillable: true));
    $defColl->add(new Def(
      9,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'suppliertype_id',
      itemtype: '\App\Models\Suppliertype',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*


    $tab[] = [
        'id'                 => 'common',
        'name'               => __('Characteristics')
    ];

    if ($_SESSION["glpinames_format"] == User::FIRSTNAME_BEFORE)
    {
        $name1 = 'firstname';
        $name2 = 'name';
    }
        else
        {
        $name1 = 'name';
        $name2 = 'firstname';
    }

    $tab[] = [
        'id'                 => '8',
        'table'              => 'glpi_contacts',
        'field'              => 'completename',
        'name'               => _n('Associated contact', 'Associated contacts', Session::getPluralNumber()),
        'forcegroupby'       => true,
        'datatype'           => 'itemlink',
        'massiveaction'      => false,
        'computation'        => "CONCAT(".$DB->quoteName("TABLE.$name1").", ' ', ".$DB->quoteName("TABLE.$name2").")",
        'computationgroupby' => true,
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_contacts_suppliers',
              'joinparams'         => [
                'jointype'           => 'child'
              ]
          ]
        ]
    ];



    $tab[] = [
        'id'                 => '29',
        'table'              => 'glpi_contracts',
        'field'              => 'name',
        'name'               => _n('Associated contract', 'Associated contracts', Session::getPluralNumber()),
        'forcegroupby'       => true,
        'datatype'           => 'itemlink',
        'massiveaction'      => false,
        'joinparams'         => [
          'beforejoin'         => [
              'table'              => 'glpi_contracts_suppliers',
              'joinparams'         => [
                'jointype'           => 'child'
              ]
          ]
        ]
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Supplier', 'Suppliers', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Contact', 'Contacts', 2),
        'icon' => 'user tie',
        'link' => $rootUrl . '/contacts',
      ],
      [
        'title' => npgettext('global', 'Contract', 'Contracts', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/attacheditems',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
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
        'title' => pgettext('global', 'Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
