<?php

declare(strict_types=1);

namespace App\Traits\Definitions;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;

trait Infocoms
{
  public static function getDefinitionInfocom(): DefinitionCollection
  {
    $t = [
      'order_date' => pgettext('administrative management', 'Order date'),
      'buy_date' => pgettext('administrative management', 'Date of purchase'),
      'delivery_date' => pgettext('administrative management', 'Delivery date'),
      'use_date' => pgettext('administrative management', 'Startup date'),
      'inventory_date' => pgettext('administrative management', 'Date of last physical inventory'),
      'decommission_date' => pgettext('administrative management', 'Decommission date'),
      'supplier' => npgettext('global', 'Supplier', 'Suppliers', 1),
      'budget' => npgettext('global', 'Budget', 'Budgets', 1),
      'order_number' => pgettext('administrative management', 'Order number'),
      'immo_number' => pgettext('administrative management', 'Immobilization number'),
      'bill' => pgettext('administrative management', 'Invoice number'),
      'delivery_number' => pgettext('administrative management', 'Delivery form'),
      'value' => pgettext('administrative management', 'Value'),
      'warranty_value' => pgettext('administrative management', 'Warranty extension value'),
      'sink_type' => pgettext('administrative management', 'Amortization type'),
      'sink_time' => pgettext('administrative management', 'Amortization duration'),
      'sink_coeff' => pgettext('administrative management', 'Amortization coefficient'),
      'businesscriticity' => npgettext('global', 'Business criticity', 'Business criticities', 1),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'warranty_date' => pgettext('administrative management', 'Start date of warranty'),
      'warranty_duration' => pgettext('administrative management', 'Warranty duration'),
      'warranty_info' => pgettext('administrative management', 'Warranty information'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Definition(1001, '', 'input', 'item_type', fillable: true, display: false));
    $defColl->add(new Definition(1002, '', 'input', 'item_id', fillable: true, display: false));
    $defColl->add(new Definition(1, $t['order_date'], 'date', 'order_date', fillable: true));
    $defColl->add(new Definition(2, $t['buy_date'], 'date', 'buy_date', fillable: true));
    $defColl->add(new Definition(3, $t['delivery_date'], 'date', 'delivery_date', fillable: true));
    $defColl->add(new Definition(4, $t['use_date'], 'date', 'use_date', fillable: true));
    $defColl->add(new Definition(5, $t['inventory_date'], 'date', 'inventory_date', fillable: true));
    $defColl->add(new Definition(6, $t['decommission_date'], 'date', 'decommission_date', fillable: true));
    $defColl->add(new Definition(
      7,
      $t['supplier'],
      'dropdown_remote',
      'supplier',
      dbname: 'supplier_id',
      itemtype: '\App\Models\Supplier',
      fillable: true
    ));
    $defColl->add(new Definition(
      8,
      $t['budget'],
      'dropdown_remote',
      'budget',
      dbname: 'budget_id',
      itemtype: '\App\Models\Budget',
      fillable: true
    ));
    $defColl->add(new Definition(9, $t['order_number'], 'input', 'order_number', fillable: true));
    $defColl->add(new Definition(10, $t['immo_number'], 'input', 'immo_number', fillable: true));
    $defColl->add(new Definition(11, $t['bill'], 'input', 'bill', fillable: true));
    $defColl->add(new Definition(12, $t['delivery_number'], 'input', 'delivery_number', fillable: true));
    $defColl->add(new Definition(13, $t['value'], 'input', 'value', fillable: true));
    $defColl->add(new Definition(14, $t['warranty_value'], 'input', 'warranty_value', fillable: true));
    $defColl->add(new Definition(
      15,
      $t['sink_type'],
      'dropdown',
      'sink_type',
      dbname: 'sink_type',
      values: self::getAmortType(),
      fillable: true
    ));
    $defColl->add(new Definition(
      16,
      $t['sink_time'],
      'dropdown',
      'sink_time',
      dbname: 'sink_time',
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 15, 1, [], 'year'),
      fillable: true
    ));
    $defColl->add(new Definition(17, $t['sink_coeff'], 'input', 'sink_coeff', fillable: true));
    $defColl->add(new Definition(
      18,
      $t['businesscriticity'],
      'dropdown_remote',
      'businesscriticity',
      dbname: 'businesscriticity_id',
      itemtype: '\App\Models\Businesscriticity',
      fillable: true
    ));
    $defColl->add(new Definition(19, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Definition(20, $t['warranty_date'], 'date', 'warranty_date', fillable: true));
    $defColl->add(new Definition(
      21,
      $t['warranty_duration'],
      'dropdown',
      'warranty_duration',
      dbname: 'warranty_duration',
      values: \App\v1\Controllers\Dropdown::generateNumbers(
        0,
        120,
        1,
        ['-1' => pgettext('administrative management', 'Lifelong')],
        'month'
      ),
      fillable: true
    ));
    $defColl->add(new Definition(22, $t['warranty_info'], 'input', 'warranty_info', fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getAmortType(): array
  {
    return [
      0 => [
        'title' => '',
      ],
      1 => [
        'title' => pgettext('administrative management', 'Decreasing'),
      ],
      2 => [
        'title' => pgettext('administrative management', 'Linear'),
      ],
    ];
  }
}
