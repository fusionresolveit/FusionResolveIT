<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Domain
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'usertech' => pgettext('inventory device', 'Technician in charge'),
      'grouptech' => pgettext('inventory device', 'Group in charge'),
      'created_at' => pgettext('global', 'Creation date'),
      'date_expiration' => pgettext('global', 'Expiration date'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'others' => pgettext('inventory device', 'Others'),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      2,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'domaintype_id',
      itemtype: '\App\Models\Domaintype',
      fillable: true
    ));
    $defColl->add(new Def(
      3,
      $t['usertech'],
      'dropdown_remote',
      'usertech',
      dbname: 'user_id_tech',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      10,
      $t['grouptech'],
      'dropdown_remote',
      'grouptech',
      dbname: 'group_id_tech',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(5, $t['created_at'], 'date', 'created_at', readonly: true));
    $defColl->add(new Def(6, $t['date_expiration'], 'date', 'date_expiration', fillable: true));
    $defColl->add(new Def(7, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(9, $t['others'], 'input', 'others', fillable: true));
    $defColl->add(new Def(18, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(12, $t['updated_at'], 'datetime', 'updated_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $tab = [];

    $tab[] = [
      'id'                 => 'common',
      'name'               => self::getTypeName(2)
    ];

    $tab[] = [
    'id'                 => '8',
      'table'              => 'glpi_domains_items',
      'field'              => 'items_id',
      'nosearch'           => true,
      'massiveaction'      => false,
      'name'               => _n('Associated item', 'Associated items', Session::getPluralNumber()),
      'forcegroupby'       => true,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '30',
      'table'              => $this->getTable(),
      'field'              => 'id',
      'name'               => __('ID'),
      'datatype'           => 'number'
    ];

    $tab[] = [
      'id'                 => '81',
      'table'              => 'glpi_entities',
      'field'              => 'entities_id',
      'name'               => __('Entity-ID')
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
        'title' => npgettext('global', 'Domain', 'Domains', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('inventory device', 'Impact analysis'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('inventory device', 'Record', 'Records', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/records',
      ],
      [
        'title' => npgettext('global', 'Associated item', 'Associated items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/attacheditems',
      ],
      [
        'title' => pgettext('global', 'Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => pgettext('global', 'ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
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
        'title' => npgettext('global', 'Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => npgettext('global', 'External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
