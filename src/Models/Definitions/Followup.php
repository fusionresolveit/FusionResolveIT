<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Followup
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'id' => $translator->translate('ID'),
      'content' => $translator->translate('Content'),
      'source' => $translator->translate('Source of followup'),
      'is_private' => $translator->translate('Private'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
      'user' => $translator->translatePlural('User', 'Users', 1),
      'is_tech' => $translator->translate('Technician has written'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false));
    $defColl->add(new Def(4, $t['content'], 'textarea', 'content', fillable: true));
    $defColl->add(new Def(
      1000,
      $t['source'],
      'dropdown_remote',
      'source',
      dbname: 'requesttype_id',
      itemtype: '\App\Models\Requesttype',
      fillable: true
    ));
    $defColl->add(new Def(6, $t['is_private'], 'boolean', 'is_private', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));
    $defColl->add(new Def(
      5,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true,
      relationfields: ['id', 'name', 'completename']
    ));
    $defColl->add(new Def(1001, $t['is_tech'], 'boolean', 'is_private', fillable: true, display: false));
    $defColl->add(new Def(1002, '', 'input', 'item_id', fillable: true, display: false));
    $defColl->add(new Def(1003, '', 'input', 'item_type', fillable: true, display: false));

    return $defColl;
    // [
    //   'id'    => 80,
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
}
