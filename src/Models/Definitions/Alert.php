<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Alert
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'message' => $translator->translate('Description'),
      'type' => $translator->translate('news' . "\004" . 'Type (to add an icon before alert title)'),
      'begin_date' => $translator->translate('Visibility start date'),
      'end_date' => $translator->translate('Visibility end date'),
      'is_recursive' => $translator->translate('Recursive'),
      'is_displayed_onlogin' => $translator->translate('news' . "\004" . 'Show on login page'),
      'is_displayed_oncentral' => $translator->translate('news' . "\004" . 'Show on helpdesk page'),
      'is_active' => $translator->translate('Active'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(250, $t['message'], 'textarea', 'message', fillable: true));
    $defColl->add(new Def(
      255,
      $t['type'],
      'dropdown',
      'type',
      dbname: 'type',
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('news' . "\004" . 'General'),
      ],
      2 => [
        'title' => $translator->translate('news' . "\004" . 'Information'),
      ],
      3 => [
        'title' => $translator->translate('news' . "\004" . 'Warning'),
      ],
      4 => [
        'title' => $translator->translate('news' . "\004" . 'Problem'),
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
        'title' => $translator->translatePlural('Alert', 'Alerts', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Target', 'Targets', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => '',
      ],
    ];
  }
}
