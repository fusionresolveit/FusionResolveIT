<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Softwareversion
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'software' => npgettext('global', 'Software', 'Software', 1),
      'name' => pgettext('global', 'Name'),
      'operatingsystem' => npgettext('inventory device', 'Operating System', 'Operating Systems', 1),
      'state' => pgettext('inventory device', 'Status'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(
      10,
      $t['software'],
      'dropdown_remote',
      'software',
      dbname: 'software_id',
      itemtype: '\App\Models\Software',
      readonly: true
    ));
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      11,
      $t['operatingsystem'],
      'dropdown_remote',
      'operatingsystem',
      dbname: 'operatingsystem_id',
      itemtype: '\App\Models\Operatingsystem',
      fillable: true
    ));
    $defColl->add(new Def(
      12,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'License', 'Licenses', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('software', 'Summary'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('software', 'Installation', 'Installations', 2),
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
