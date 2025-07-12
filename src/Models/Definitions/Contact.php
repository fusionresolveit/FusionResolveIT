<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Contact
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('user parameter', 'Last name'),
      'firstname' => pgettext('user parameter', 'First name'),
      'phone' => npgettext('user parameter', 'Phone number', 'Phone numbers', 1),
      'phone2' => pgettext('user parameter', 'Second phone number'),
      'mobile' => pgettext('user parameter', 'Mobile phone number'),
      'fax' => pgettext('global', 'Fax'),
      'email' => npgettext('global', 'Email', 'Emails', 1),
      'address' => pgettext('location', 'Address'),
      'postcode' => pgettext('location', 'Postal code'),
      'town' => pgettext('location', 'City'),
      'state' => pgettext('location', 'State'),
      'country' => pgettext('location', 'Country'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'title' => pgettext('user parameter', 'Title'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(11, $t['firstname'], 'input', 'firstname', fillable: true));
    $defColl->add(new Def(3, $t['phone'], 'input', 'phone', fillable: true));
    $defColl->add(new Def(4, $t['phone2'], 'input', 'phone2', fillable: true));
    $defColl->add(new Def(10, $t['mobile'], 'input', 'mobile', fillable: true));
    $defColl->add(new Def(5, $t['fax'], 'input', 'fax', fillable: true));
    $defColl->add(new Def(6, $t['email'], 'email', 'email', fillable: true));
    $defColl->add(new Def(82, $t['address'], 'textarea', 'address', fillable: true));
    $defColl->add(new Def(83, $t['postcode'], 'input', 'postcode', fillable: true));
    $defColl->add(new Def(84, $t['town'], 'input', 'town', fillable: true));
    $defColl->add(new Def(85, $t['state'], 'input', 'state', fillable: true));
    $defColl->add(new Def(87, $t['country'], 'input', 'country', fillable: true));
    $defColl->add(new Def(
      9,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'contacttype_id',
      itemtype: '\App\Models\Contacttype',
      fillable: true
    ));
    $defColl->add(new Def(
      119,
      $t['title'],
      'dropdown_remote',
      'title',
      dbname: 'usertitle_id',
      itemtype: '\App\Models\Usertitle',
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

    $tab[] = [
      'id'                 => '8',
      'table'              => 'glpi_suppliers',
      'field'              => 'name',
      'name'               => _n('Associated supplier', 'Associated suppliers', Session::getPluralNumber()),
      'forcegroupby'       => true,
      'datatype'           => 'itemlink',
      'joinparams'         => [
      'beforejoin'         => [
      'table'              => 'glpi_contacts_suppliers',
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
        'title' => npgettext('global', 'Contact', 'Contacts', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Supplier', 'Suppliers', 2),
        'icon' => 'dolly',
        'link' => $rootUrl . '/suppliers',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
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
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
