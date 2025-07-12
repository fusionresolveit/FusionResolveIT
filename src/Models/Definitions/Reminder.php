<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Reminder
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'            => pgettext('global', 'Title'),
      'text'            => pgettext('global', 'Description'),
      'begin_view_date' => pgettext('reminder', 'Visibility start date'),
      'end_view_date'   => pgettext('reminder', 'Visibility end date'),
      'state'           => pgettext('global', 'Status'),
      'is_planned'      => pgettext('calendar', 'Planning'),
      'begin'           => pgettext('reminder', 'Planning start date'),
      'end'             => pgettext('reminder', 'Planning end date'),
      'user'            => pgettext('ITIL', 'Writer'),
      'updated_at'      => pgettext('global', 'Last update'),
      'created_at'      => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(4, $t['text'], 'textarea', 'text', fillable: true));
    $defColl->add(new Def(5, $t['begin_view_date'], 'datetime', 'begin_view_date', fillable: true));
    $defColl->add(new Def(6, $t['end_view_date'], 'datetime', 'end_view_date', fillable: true));
    $defColl->add(new Def(
      32,
      $t['state'],
      'dropdown',
      'state',
      dbname: 'state_id',
      values: self::getStateArray(),
      fillable: true
    ));
    $defColl->add(new Def(7, $t['is_planned'], 'boolean', 'is_planned', fillable: true));
    $defColl->add(new Def(8, $t['begin'], 'datetime', 'begin', fillable: true));
    $defColl->add(new Def(9, $t['end'], 'datetime', 'end', fillable: true));
    $defColl->add(new Def(
      2,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    /*

    $tab[] = [
        'id'                 => 'common',
        'name'               => __('Characteristics')
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    */
  }

  /**
   * @return array<mixed>
   */
  public static function getStateArray(): array
  {
    return [
      0 => [
        'title' => npgettext('reminder status', 'Information', 'Information', 1),
        'color' => 'fusionmajor',
        'icon'  => 'fire extinguisher',
      ],
      1 => [
        'title' => pgettext('reminder status', 'To do'),
        'color' => 'fusionveryhigh',
        'icon'  => 'fire alternate',
      ],
      2 => [
        'title' => pgettext('reminder status', 'Done'),
        'color' => 'fusionhigh',
        'icon'  => 'fire',
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
        'title' => npgettext('global', 'Note', 'Notes', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('target', 'Target', 'Targets', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
