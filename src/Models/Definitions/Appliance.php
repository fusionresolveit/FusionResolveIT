<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Appliance
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'          => pgettext('global', 'Name'),
      'state'         => pgettext('inventory device', 'Status'),
      'location'      => npgettext('global', 'Location', 'Locations', 1),
      'manufacturer'  => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
      'appliancetype' => npgettext('global', 'Appliance type', 'Appliance types', 1),
      'environment'   => npgettext('global', 'Appliance environment', 'Appliance environments', 1),
      'usertech'     => pgettext('inventory device', 'Technician in charge'),
      'grouptech'    => pgettext('inventory device', 'Group in charge'),
      'user'          => npgettext('global', 'User', 'Users', 1),
      'group'         => npgettext('global', 'Group', 'Groups', 1),
      'serial'        => pgettext('inventory device', 'Serial number'),
      'otherserial'   => pgettext('inventory device', 'Inventory number'),
      'associable'    => pgettext('software', 'Associable to a ticket'),
      'comment'       => npgettext('global', 'Comment', 'Comments', 2),
      'recursive'     => pgettext('global', 'Child entities'),
      'updated'       => pgettext('global', 'Last update'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      32,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true,
    ));
    $defColl->add(new Def(
      3,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true,
    ));
    $defColl->add(new Def(
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer',
      fillable: true,
    ));
    $defColl->add(new Def(
      11,
      $t['appliancetype'],
      'dropdown_remote',
      'type',
      dbname: 'appliancetype_id',
      itemtype: '\App\Models\Appliancetype',
      fillable: true,
    ));
    $defColl->add(new Def(
      10,
      $t['environment'],
      'dropdown_remote',
      'environment',
      dbname: 'applianceenvironment_id',
      itemtype: '\App\Models\Applianceenvironment',
      fillable: true,
    ));
    $defColl->add(new Def(
      24,
      $t['usertech'],
      'dropdown_remote',
      'usertech',
      dbname: 'user_id_tech',
      itemtype: '\App\Models\User',
      fillable: true,
    ));
    $defColl->add(new Def(
      49,
      $t['grouptech'],
      'dropdown_remote',
      'grouptech',
      dbname: 'group_id_tech',
      itemtype: '\App\Models\Group',
      fillable: true,
    ));
    $defColl->add(new Def(
      6,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true,
    ));
    $defColl->add(new Def(
      8,
      $t['group'],
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true,
    ));
    $defColl->add(new Def(12, $t['serial'], 'input', 'serial', fillable: true));
    $defColl->add(new Def(13, $t['otherserial'], 'input', 'otherserial', fillable: true));
    $defColl->add(new Def(61, $t['associable'], 'boolean', 'is_helpdesk_visible', fillable: true));
    $defColl->add(new Def(4, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(7, $t['recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(9, $t['updated'], 'datetime', 'updated_at', readonly: true));
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
      'id'   => 'common',
      'name' => __('Characteristics')
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    $tab[] = [
      'id'            => '5',
      'table'         =>  Appliance_Item::getTable(),
      'field'         => 'items_id',
      'name'               => _n('Associated item', 'Associated items', 2),
      'nosearch'           => true,
      'massiveaction' => false,
      'forcegroupby'  =>  true,
      'additionalfields'   => ['itemtype'],
      'joinparams'    => ['jointype' => 'child']
    ];

    $tab[] = [
      'id'            => '31',
      'table'         => self::getTable(),
      'field'         => 'id',
      'name'          => __('ID'),
      'datatype'      => 'number',
      'massiveaction' => false
    ];

    $tab[] = [
      'id'            => '81',
      'table'         => Entity::getTable(),
      'field'         => 'entities_id',
      'name'          => sprintf('%s-%s', Entity::getTypeName(1), __('ID'))
    ];

    $tab = array_merge($tab, Certificate::rawSearchOptionsToAdd());
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Appliance', 'Appliances', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('inventory device', 'Impact analysis'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
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
        'title' => pgettext('global', 'Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => npgettext('global', 'Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => npgettext('global', 'Domain', 'Domains', 2),
        'icon' => 'globe americas',
        'link' => $rootUrl . '/domains',
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
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
