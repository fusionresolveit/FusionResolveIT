<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Softwareversion
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'software' => $translator->translatePlural('Software', 'Softwares', 1),
      'name' => $translator->translate('Name'),
      'operatingsystem' => $translator->translatePlural('Operating system', 'Operating systems', 1),
      'state' => $translator->translate('Status'),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('License', 'Licenses', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Summary'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Installation', 'Installations', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
