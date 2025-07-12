<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\v1\Controllers\Dropdown;

class Contract
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'num' => pgettext('user parameter', 'Phone number'),
      'state' => pgettext('inventory device', 'Status'),
      'type' => npgettext('contract', 'Contract type', 'Contract types', 1),
      'begin_date' => pgettext('global', 'Start date'),
      'accounting_number' => pgettext('contract', 'Account number'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'renewal' => pgettext('contract', 'Renewal'),
      'duration' => pgettext('contract', 'Initial contract period'),
      'notice' => pgettext('event', 'Notice'),
      'periodicity' => pgettext('contract', 'Contract renewal period'),
      'billing' => pgettext('contract', 'Invoice period'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(3, $t['num'], 'input', 'num', fillable: true));
    $defColl->add(new Def(
      31,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true
    ));
    $defColl->add(new Def(
      4,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'contracttype_id',
      itemtype: '\App\Models\Contracttype',
      fillable: true
    ));
    $defColl->add(new Def(5, $t['begin_date'], 'date', 'begin_date', fillable: true));
    $defColl->add(new Def(10, $t['accounting_number'], 'input', 'accounting_number', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(
      23,
      $t['renewal'],
      'dropdown',
      'renewal',
      dbname: 'renewal',
      values: self::getContractRenewalArray(),
      fillable: true
    ));
    $defColl->add(new Def(
      6,
      $t['duration'],
      'dropdown',
      'duration',
      dbname: 'duration',
      values: \App\v1\Controllers\Dropdown::generateNumbers(1, 120, 1, [0 => '-----'], 'month'),
      fillable: true
    ));
    $defColl->add(new Def(
      7,
      $t['notice'],
      'dropdown',
      'notice',
      dbname: 'notice',
      values: \App\v1\Controllers\Dropdown::generateNumbers(0, 120, 1, [], 'month'),
      fillable: true
    ));
    $defColl->add(new Def(
      21,
      $t['periodicity'],
      'dropdown',
      'periodicity',
      dbname: 'periodicity',
      values: \App\v1\Controllers\Dropdown::generateNumbers(
        12,
        60,
        12,
        [
          0 => '-----',
          1 => sprintf(npgettext('global', '%d month', '%d months', 1), 1),
          2 => sprintf(npgettext('global', '%d month', '%d months', 2), 2),
          3 => sprintf(npgettext('global', '%d month', '%d months', 3), 3),
          6 => sprintf(npgettext('global', '%d month', '%d months', 6), 6)
        ],
        'month'
      ),
      fillable: true
    ));
    $defColl->add(new Def(
      22,
      $t['billing'],
      'dropdown',
      'billing',
      dbname: 'billing',
      values: \App\v1\Controllers\Dropdown::generateNumbers(
        12,
        60,
        12,
        [
          0 => '-----',
          1 => sprintf(npgettext('global', '%d month', '%d months', 1), 1),
          2 => sprintf(npgettext('global', '%d month', '%d months', 2), 2),
          3 => sprintf(npgettext('global', '%d month', '%d months', 3), 3),
          6 => sprintf(npgettext('global', '%d month', '%d months', 6), 6)
        ],
        'month'
      ),
      fillable: true
    ));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;

    // [
    //    'id'    => 80,
    //    'title' => npgettext('global', 'Entity', 'Entities', 1),
    //    'type'  => 'dropdown_remote',
    //    'name'  => 'completename',
    //    'itemtype' => '\App\Models\Entity',
    // ],


    /*
    $tab[] = [
      'id'                 => 'common',
      'name'               => __('Characteristics')
    ];

    $tab[] = [
      'id'                 => '20',
      'table'              => $this->getTable(),
      'field'              => 'end_date',
      'name'               => __('End date'),
      'datatype'           => 'date_delay',
      'datafields'         => [
      '1'                  => 'begin_date',
      '2'                  => 'duration'
      ],
      'searchunit'         => 'MONTH',
      'delayunit'          => 'MONTH',
      'maybefuture'        => true,
      'massiveaction'      => false
    ];

    $tab[] = [
      'id'                 => '12',
      'table'              => $this->getTable(),
      'field'              => 'expire',
      'name'               => __('Expiration'),
      'datatype'           => 'date_delay',
      'datafields'         => [
      '1'                  => 'begin_date',
      '2'                  => 'duration'
      ],
      'searchunit'         => 'DAY',
      'delayunit'          => 'MONTH',
      'maybefuture'        => true,
      'massiveaction'      => false
    ];

    $tab[] = [
      'id'                 => '13',
      'table'              => $this->getTable(),
      'field'              => 'expire_notice',
      'name'               => __('Expiration date + notice'),
      'datatype'           => 'date_delay',
      'datafields'         => [
      '1'                  => 'begin_date',
      '2'                  => 'duration',
      '3'                  => 'notice'
      ],
      'searchunit'         => 'DAY',
      'delayunit'          => 'MONTH',
      'maybefuture'        => true,
      'massiveaction'      => false
    ];

    $tab[] = [
      'id'                 => '59',
      'table'              => $this->getTable(),
      'field'              => 'alert',
      'name'               => __('Email alarms'),
      'datatype'           => 'specific',
      'searchtype'         => ['equals', 'notequals']
    ];

    $tab[] = [
      'id'                 => '72',
      'table'              => 'glpi_contracts_items',
      'field'              => 'id',
      'name'               => _x('quantity', 'Number of items'),
      'forcegroupby'       => true,
      'usehaving'          => true,
      'datatype'           => 'count',
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '29',
      'table'              => 'glpi_suppliers',
      'field'              => 'name',
      'name'               => _n('Associated supplier', 'Associated suppliers',
      Session::getPluralNumber()),
      'forcegroupby'       => true,
      'datatype'           => 'itemlink',
      'massiveaction'      => false,
      'joinparams'         => [
      'beforejoin'         => [
      'table'              => 'glpi_contracts_suppliers',
      'joinparams'         => [
      'jointype'           => 'child'
      ]
      ]
      ]
    ];

    $tab[] = [
      'id'                 => '50',
      'table'              => $this->getTable(),
      'field'              => 'template_name',
      'name'               => __('Template name'),
      'datatype'           => 'text',
      'massiveaction'      => false,
      'nosearch'           => true,
      'nodisplay'          => true,
      'autocomplete'       => true,
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

    $tab[] = [
      'id'                 => 'cost',
      'name'               => _n('Cost', 'Costs', 1)
    ];

    $tab[] = [
      'id'                 => '11',
      'table'              => 'glpi_contractcosts',
      'field'              => 'totalcost',
      'name'               => __('Total cost'),
      'datatype'           => 'decimal',
      'forcegroupby'       => true,
      'usehaving'          => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ],
      'computation'        =>
      '(SUM(' . $DB->quoteName('TABLE.cost') . ') / COUNT(' .
      $DB->quoteName('TABLE.id') . ')) * COUNT(DISTINCT ' .
      $DB->quoteName('TABLE.id') . ')',
      'nometa'             => true, // cannot GROUP_CONCAT a SUM
    ];

    $tab[] = [
      'id'                 => '41',
      'table'              => 'glpi_contractcosts',
      'field'              => 'cost',
      'name'               => _n('Cost', 'Costs', Session::getPluralNumber()),
      'datatype'           => 'decimal',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '42',
      'table'              => 'glpi_contractcosts',
      'field'              => 'begin_date',
      'name'               => sprintf(__('%1$s - %2$s'), _n('Cost', 'Costs', 1), __('Begin date')),
      'datatype'           => 'date',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '43',
      'table'              => 'glpi_contractcosts',
      'field'              => 'end_date',
      'name'               => sprintf(__('%1$s - %2$s'), _n('Cost', 'Costs', 1), __('End date')),
      'datatype'           => 'date',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '44',
      'table'              => 'glpi_contractcosts',
      'field'              => 'name',
      'name'               => sprintf(__('%1$s - %2$s'), _n('Cost', 'Costs', 1), __('Name')),
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ],
      'datatype'           => 'dropdown'
    ];

    $tab[] = [
      'id'                 => '45',
      'table'              => 'glpi_budgets',
      'field'              => 'name',
      'name'               => sprintf(__('%1$s - %2$s'), _n('Cost', 'Costs', 1), Budget::getTypeName(1)),
      'datatype'           => 'dropdown',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'beforejoin'         => [
      'table'              => 'glpi_contractcosts',
      'joinparams'         => [
      'jointype'           => 'child'
      ]
      ]
      ]
    ];
    */
  }

  /**
   * @return array<int, mixed>
   */
  public static function getContractRenewalArray(): array
  {
    return [
      0 => [
        'title' => pgettext('contract renewal', 'Never'),
      ],
      1 => [
        'title' => pgettext('contract renewal', 'Tacit'),
      ],
      2 => [
        'title' => pgettext('contract renewal', 'Express'),
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
        'title' => npgettext('global', 'Contract', 'Contracts', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Cost', 'Costs', 2),
        'icon' => 'money bill alternate',
        'link' => $rootUrl . '/costs',
      ],
      [
        'title' => npgettext('global', 'Supplier', 'Suppliers', 2),
        'icon' => 'dolly',
        'link' => $rootUrl . '/suppliers',
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/attacheditems',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('global', 'External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => npgettext('global', 'Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => pgettext('global', 'Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
