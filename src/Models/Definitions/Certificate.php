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
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'state' => $translator->translate('Status'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'serial' => $translator->translate('Serial number'),
      'otherserial' => $translator->translate('Inventory number'),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'dns_suffix' => $translator->translate('DNS suffix'),
      'dns_name' => $translator->translate('DNS name'),
      'is_autosign' => $translator->translate('Self-signed'),
      'date_expiration' => $translator->translate('Expiration date'),
      'command' => $translator->translate('Command used'),
      'certificate_request' => $translator->translate('Certificate request (CSR)'),
      'certificate_item' => $translator->translatePlural('Certificate', 'Certificates', 1),
      'comment' => $translator->translate('Comments'),
      'manufacturer' => sprintf(
        $translator->translate('%1$s (%2$s)'),
        $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
        $translator->translate('Root CA')
      ),
      'usertech' => $translator->translate('Technician in charge of the hardware'),
      'grouptech' => $translator->translate('Group in charge of the hardware'),
      'user' => $translator->translatePlural('User', 'Users', 1),
      'group' => $translator->translatePlural('Group', 'Groups', 1),
      'contact' => $translator->translate('Alternate username'),
      'contact_num' => $translator->translate('Alternate username number'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Certificate', 'Certificates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Associated item', 'Associated items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/associateditems',
      ],
      [
        'title' => $translator->translatePlural('Domain', 'Domains', 2),
        'icon' => 'globe americas',
        'link' => $rootUrl . '/domains',
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
        'link' => $rootUrl . '/knowledgebasearticles',
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
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
