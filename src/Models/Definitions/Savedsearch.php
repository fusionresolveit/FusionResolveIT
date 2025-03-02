<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Savedsearch
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'user' => $translator->translatePlural('User', 'Users', 1),
      'is_private' => $translator->translate('Visibility'),
      'do_count' => $translator->translate('Do count'),
      'last_execution_time' => $translator->translate('Last duration (ms)'),
      'last_execution_date' => $translator->translate('Last execution date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      3,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      readonly: true
    ));
    $defColl->add(new Def(
      4,
      $t['is_private'],
      'dropdown',
      'is_private',
      dbname: 'is_private',
      values: self::getVisibilityArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      10,
      $t['do_count'],
      'dropdown',
      'do_count',
      dbname: 'do_count',
      values: self::getCountArray(),
      fillable: true
    ));
    $defColl->add(new Def(9, $t['last_execution_time'], 'input', 'last_execution_time', readonly: true));
    $defColl->add(new Def(13, $t['last_execution_date'], 'datetime', 'last_execution_date', readonly: true));

    return $defColl;
    /*

    $tab = [];

    $tab[] = ['id'                 => 'common',
              'name'               => __('Characteristics')
              ];


    $tab[] = ['id'                 => '2',
              'table'              => $this->getTable(),
              'field'              => 'id',
              'name'               => __('ID'),
              'massiveaction'      => false, // implicit field is id
              'datatype'           => 'number'
              ];


    $tab[] = ['id'                 => '8',
              'table'              => $this->getTable(),
              'field'              => 'itemtype',
              'name'               => __('Item type'),
              'massiveaction'      => false,
              'datatype'           => 'itemtypename',
              'types'              => self::getUsedItemtypes()
              ];
    $tab[] = [
        'id'            => 11,
        'table'         => SavedSearch_User::getTable(),
        'field'         => 'users_id',
        'name'          => __('Default'),
        'massiveaction' => false,
        'joinparams'    => [
          'jointype'  => 'child',
          'condition' => "AND NEWTABLE.users_id = " . Session::getLoginUserID()
        ],
        'datatype'      => 'specific',
        'searchtype'    => [
          0 => 'equals',
          1 => 'notequals'
        ],
    ];

    $tab[] = ['id'                 => 12,
              'table'              => $this->getTable(),
              'field'              => 'counter',
              'name'               => __('Counter'),
              'massiveaction'      => false,
              'datatype'           => 'number'
              ];

    */
  }

  /**
   * @return array<int, mixed>
   */
  public static function getVisibilityArray(): array
  {
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('Private'),
      ],
      0 => [
        'title' => $translator->translate('Public'),
      ],
    ];
  }

  /**
   * @return array<int, mixed>
   */
  public static function getCountArray(): array
  {
    global $translator;
    return [
      2 => [
        'title' => $translator->translate('Auto'),
      ],
      1 => [
        'title' => $translator->translate('Yes'),
      ],
      0 => [
        'title' => $translator->translate('No'),
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
        'title' => $translator->translatePlural('Saved search', 'Saved searches', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Saved search alert', 'Saved searches alerts', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
    ];
  }
}
