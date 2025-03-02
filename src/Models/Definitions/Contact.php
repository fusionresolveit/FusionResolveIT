<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Contact
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Last name'),
      'firstname' => $translator->translate('First name'),
      'phone' => $translator->translatePlural('Phone', 'Phones', 1),
      'phone2' => $translator->translate('Phone 2'),
      'mobile' => $translator->translate('Mobile phone'),
      'fax' => $translator->translate('Fax'),
      'email' => $translator->translatePlural('Email', 'Emails', 1),
      'address' => $translator->translate('Address'),
      'postcode' => $translator->translate('Postal code'),
      'town' => $translator->translate('City'),
      'state' => $translator->translate('location' . "\004" . 'State'),
      'country' => $translator->translate('Country'),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'title' => $translator->translate('person' . "\004" . 'Title'),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Contact', 'Contacts', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Supplier', 'Suppliers', 2),
        'icon' => 'dolly',
        'link' => $rootUrl . '/suppliers',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
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
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
