<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Budget
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'value' => $translator->translate('price' . "\004" . 'Value'),
      'begin_date' => $translator->translate('Start date'),
      'end_date' => $translator->translate('End date'),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'comment' => $translator->translate('Comments'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(7, $t['value'], 'input', 'value', fillable: true));
    $defColl->add(new Def(5, $t['begin_date'], 'date', 'begin_date', fillable: true));
    $defColl->add(new Def(6, $t['end_date'], 'date', 'end_date', fillable: true));
    $defColl->add(new Def(
      4,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'budgettype_id',
      itemtype: '\App\Models\Budgettype',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      3,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;


    // [
    //   'id'    => 50,
    //   'title' => $translator->translate('Template name'),
    //   'type'  => 'input',
    //   'name'  => 'template_name',
    // ],
    // [
    //   'id'    => 80,
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $tab[] = [
      'id'                 => 'common',
      'name'               => __('Characteristics')
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Budget', 'Budget', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Main'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/budgetmain',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/attacheditems',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translate('Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowbaseitems',
      ],
      [
        'title' => $translator->translatePlural('External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
