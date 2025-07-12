<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Certificate
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'state' => pgettext('inventory device', 'Status'),
      'location' => npgettext('global', 'Location', 'Locations', 1),
      'serial' => pgettext('inventory device', 'Serial number'),
      'otherserial' => pgettext('inventory device', 'Inventory number'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'dns_suffix' => pgettext('certificate', 'DNS suffix'),
      'dns_name' => pgettext('certificate', 'DNS name'),
      'is_autosign' => pgettext('certificate', 'Self-signed'),
      'date_expiration' => pgettext('global', 'Expiration date'),
      'command' => pgettext('certificate', 'Command used'),
      'certificate_request' => pgettext('certificate', 'Certificate request (CSR)'),
      'certificate_item' => npgettext('global', 'Certificate', 'Certificates', 1),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'manufacturer' => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        npgettext('global', 'Manufacturer', 'Manufacturers', 1),
        pgettext('certificate', 'Root CA')
      ),
      'usertech' => pgettext('inventory device', 'Technician in charge of the hardware'),
      'grouptech' => pgettext('inventory device', 'Group in charge of the hardware'),
      'user' => npgettext('global', 'User', 'Users', 1),
      'group' => npgettext('global', 'Group', 'Groups', 1),
      'contact' => pgettext('inventory device', 'Alternate username'),
      'contact_num' => pgettext('inventory device', 'Alternate username number'),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      31,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
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
    $defColl->add(new Def(5, $t['serial'], 'input', 'serial', fillable: true));
    $defColl->add(new Def(6, $t['otherserial'], 'input', 'otherserial', fillable: true));
    $defColl->add(new Def(
      7,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'certificatetype_id',
      itemtype: '\App\Models\Certificatetype',
      fillable: true
    ));
    $defColl->add(new Def(8, $t['dns_suffix'], 'input', 'dns_suffix', fillable: true));
    $defColl->add(new Def(18, $t['dns_name'], 'input', 'dns_name', fillable: true));
    $defColl->add(new Def(9, $t['is_autosign'], 'boolean', 'is_autosign', fillable: true));
    $defColl->add(new Def(10, $t['date_expiration'], 'date', 'date_expiration', fillable: true));
    $defColl->add(new Def(11, $t['command'], 'textarea', 'command', fillable: true));
    $defColl->add(new Def(12, $t['certificate_request'], 'textarea', 'certificate_request', fillable: true));
    $defColl->add(new Def(13, $t['certificate_item'], 'textarea', 'certificate_item', fillable: true));
    $defColl->add(new Def(15, $t['comment'], 'textarea', 'comment', fillable: true));
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
    $defColl->add(new Def(16, $t['contact'], 'input', 'contact', fillable: true));
    $defColl->add(new Def(17, $t['contact_num'], 'input', 'contact_num', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;

    // [
    //   'id'    => 61,
    //   'title' => 'Template name',
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
    $tab[] = [
      'id'                 => 'common',
      'name'               => __('Characteristics')
    ];

    $tab[] = [
      'id'                 => '14',
      'table'              => 'glpi_certificates_items',
      'field'              => 'items_id',
      'name'               => _n('Associated item', 'Associated items', Session::getPluralNumber()),
      'nosearch'           => true,
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'additionalfields'   => ['itemtype'],
      'joinparams'         => ['jointype' => 'child']
    ];


    $tab[] = [
      'id'                 => '72',
      'table'              => 'glpi_certificates_items',
      'field'              => 'id',
      'name'               => _x('quantity', 'Number of associated items'),
      'forcegroupby'       => true,
      'usehaving'          => true,
      'datatype'           => 'count',
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
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
        'title' => npgettext('global', 'Certificate', 'Certificates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Associated item', 'Associated items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/associateditems',
      ],
      [
        'title' => npgettext('global', 'Domain', 'Domains', 2),
        'icon' => 'globe americas',
        'link' => $rootUrl . '/domains',
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
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
