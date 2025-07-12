<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Cluster
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'state' => pgettext('inventory device', 'Status'),
      'type' =>  npgettext('global', 'Type', 'Types', 1),
      'uuid' => pgettext('global', 'UUID'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'usertech' => pgettext('inventory device', 'Technician in charge of the hardware'),
      'grouptech' => pgettext('inventory device', 'Group in charge of the hardware'),
      'version' => npgettext('global', 'Version', 'Versions', 1),
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
      4,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'clustertype_id',
      itemtype: '\App\Models\Clustertype',
      fillable: true
    ));
    $defColl->add(new Def(5, $t['uuid'], 'input', 'uuid', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
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
    $defColl->add(new Def(200, $t['version'], 'input', 'version', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;

    /*
    $tab[] = [
      'id'   => 'common',
      'name' => __('Characteristics')
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
        'title' => npgettext('global', 'Cluster', 'Clusters', 1),
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
        'title' => npgettext('inventory device', 'Network port', 'Network ports', 2),
        'icon' => 'ethernet',
        'link' => '',
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
        'title' => pgettext('global', 'ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
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
