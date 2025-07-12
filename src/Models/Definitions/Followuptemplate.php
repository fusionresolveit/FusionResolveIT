<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Followuptemplate
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'id' => pgettext('global', 'Id'),
      'content' => pgettext('global', 'Content'),
      'source' => pgettext('ITIL', 'Source of followup'),
      'is_private' => pgettext('global', 'Private'),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false));
    $defColl->add(new Def(4, $t['content'], 'textarea', 'content', fillable: true));
    $defColl->add(new Def(
      5,
      $t['source'],
      'dropdown_remote',
      'source',
      dbname: 'requesttype_id',
      itemtype: '\App\Models\Requesttype',
      fillable: true
    ));
    $defColl->add(new Def(6, $t['is_private'], 'boolean', 'is_private', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

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

    $tab[] = [
      'id'                => '2',
      'table'             => $this->getTable(),
      'field'             => 'id',
      'name'              => __('ID'),
      'massiveaction'     => false,
      'datatype'          => 'number'
    ];

    if ($DB->fieldExists($this->getTable(), 'product_number'))
    {
    $tab[] = [
      'id'  => '3',
      'table'  => $this->getTable(),
      'field'  => 'product_number',
      'name'   => __('Product number'),
      'autocomplete' => true,
    ];
    }
    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Followup template', 'Followup templates', 1),
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
