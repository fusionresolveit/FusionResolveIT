<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Planningexternaleventtemplate
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'state' => $translator->translate('Status'),
      'category' => $translator->translatePlural('Type', 'Types', 1),
      'background' => $translator->translate('Background event'),
      'duration' => $translator->translate('Period'),
      'before_time' => $translator->translate('Planning' . "\004" . 'Reminder'),
      'rrule' => $translator->translate('Repeat'),
      'text' => $translator->translate('Description'),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    global $translator;
    return [
      0 => [
        'title' => $translator->translatePlural('Information', 'Information', 1),
      ],
      1 => [
        'title' => $translator->translate('To do'),
      ],
      2 => [
        'title' => $translator->translate('Done'),
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('External events template', 'External events templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
