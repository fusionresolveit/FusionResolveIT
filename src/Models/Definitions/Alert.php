<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Alert
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'                   => pgettext('global', 'Name'),
      'message'                => npgettext('global', 'Message', 'Messages', 1),
      'type'                   => npgettext('global', 'Type', 'Types', 1),
      'begin_date'             => pgettext('display alerts', 'Visibility start date'),
      'end_date'               => pgettext('display alerts', 'Visibility end date'),
      'is_recursive'           => pgettext('global', 'Recursive'),
      'is_displayed_onlogin'   => pgettext('display alerts', 'Show on login page'),
      'is_displayed_oncentral' => pgettext('display alerts', 'Show on home page'),
      'is_active'              => pgettext('global', 'Active'),
      'updated_at'             => pgettext('global', 'Last update'),
      'created_at'             => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(250, $t['message'], 'textarea', 'message', fillable: true));
    $defColl->add(new Def(
      255,
      $t['type'],
      'dropdown',
      'type',
      values: self::getTypeArray(),
      fillable: true
    ));
    $defColl->add(new Def(2, $t['begin_date'], 'date', 'begin_date', fillable: true));
    $defColl->add(new Def(3, $t['end_date'], 'date', 'end_date', fillable: true));
    $defColl->add(new Def(5, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(8, $t['is_displayed_onlogin'], 'boolean', 'is_displayed_onlogin', fillable: true));
    $defColl->add(new Def(9, $t['is_displayed_oncentral'], 'boolean', 'is_displayed_oncentral', fillable: true));
    $defColl->add(new Def(10, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 4,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $tab[] = [
      'id'               => 6,
      'table'            => PluginNewsAlert_Target::getTable(),
      'field'            => 'items_id',
      'name'             => PluginNewsAlert_Target::getTypename(),
      'datatype'         => 'specific',
      'forcegroupby'     => true,
      'joinparams'       => ['jointype' => 'child'],
      'additionalfields' => ['itemtype'],
    ];
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getTypeArray(): array
  {
    return [
      1 => [
        'title' => pgettext('alert status', 'General'),
        'color' => 'grey',
        'icon'  => 'comment',
      ],
      2 => [
        'title' => npgettext('alert status', 'Information', 'Information', 1),
        'color' => 'blue',
        'icon'  => 'info circle',
      ],
      3 => [
        'title' => pgettext('alert status', 'Warning'),
        'color' => 'yellow',
        'icon'  => 'exclamation triangle',
      ],
      4 => [
        'title' => npgettext('alert status', 'Problem', 'Problems', 1),
        'color' => 'red',
        'icon'  => 'times circle',
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('alert title', 'Alert', 'Alerts', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('target', 'Target', 'Targets', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => '',
      ],
    ];
  }
}
