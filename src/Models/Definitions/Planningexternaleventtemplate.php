<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Planningexternaleventtemplate
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'state' => pgettext('global', 'Status'),
      'category' =>  npgettext('global', 'Type', 'Types', 1),
      'background' => pgettext('planning', 'Background event'),
      'duration' => pgettext('planning', 'Period'),
      'before_time' => npgettext('global', 'Reminder', 'Reminders', 1),
      'rrule' => pgettext('planning', 'Repeat'),
      'text' => pgettext('global', 'Description'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      4,
      $t['state'],
      'dropdown',
      'state',
      dbname: 'state_id',
      values: self::getStateArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      5,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'planningeventcategory_id',
      itemtype: '\App\Models\Planningeventcategory',
      fillable: true
    ));
    $defColl->add(new Def(201, $t['background'], 'boolean', 'background', fillable: true));
    $defColl->add(new Def(211, $t['duration'], 'input', 'duration', fillable: true));
    $defColl->add(new Def(212, $t['before_time'], 'input', 'before_time', fillable: true));
    $defColl->add(new Def(202, $t['rrule'], 'input', 'rrule', fillable: true));
    $defColl->add(new Def(203, $t['text'], 'textarea', 'text', fillable: true));
    $defColl->add(new Def(204, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<int, mixed>
   */
  public static function getStateArray(): array
  {
    return [
      0 => [
        'title' => npgettext('planning', 'Information', 'Information', 1),
      ],
      1 => [
        'title' => pgettext('planning status', 'To do'),
      ],
      2 => [
        'title' => pgettext('planning status', 'Done'),
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
        'title' => npgettext('global', 'External events template', 'External events templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
