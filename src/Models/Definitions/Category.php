<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Category
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'                    => pgettext('global', 'Name'),
      'completename'            => pgettext('global', 'Complete name'),
      'id'                      => pgettext('global', 'Id'),
      'category'                => pgettext('global', 'As child of'),
      'user'                    => pgettext('inventory device', 'Technician in charge of the hardware'),
      'group'                   => pgettext('inventory device', 'Group in charge of the hardware'),
      'code'                    => pgettext('category', 'Code representing the ticket category'),
      'is_helpdeskvisible'      => pgettext('category', 'Visible in the simplified interface'),
      'is_incident'             => pgettext('category', 'Visible for an incident'),
      'is_request'              => pgettext('category', 'Visible for a request'),
      'is_problem'              => pgettext('category', 'Visible for a problem'),
      'is_change'               => pgettext('category', 'Visible for a change'),
      'tickettemplateDemand'    => pgettext('category', 'Template for a request'),
      'tickettemplateIncident'  => pgettext('category', 'Template for an incident'),
      'changetemplate'          => pgettext('category', 'Template for a change'),
      'problemtemplate'         => pgettext('category', 'Template for a problem'),
      'comment'                 => npgettext('global', 'Comment', 'Comments', 2),
      'entity'                  => npgettext('global', 'Entity', 'Entities', 1),
      'is_recursive'            => pgettext('global', 'Child entities'),
      'updated_at'              => pgettext('global', 'Last update'),
      'created_at'              => pgettext('global', 'Creation date'),
      'is_knowledge'            => pgettext('category', 'Visible for a knowledge'),
      'is_form'                 => pgettext('category', 'Visible for a form'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(14, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(1, $t['completename'], 'input', 'completename', fillable: false, readonly: true));
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false, readonly: true));
    $defColl->add(new Def(
      13,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'category_id',
      itemtype: '\App\Models\Category',
      fillable: true
    ));
    $defColl->add(new Def(
      70,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      71,
      $t['group'],
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(99, $t['code'], 'input', 'code', fillable: true));
    $defColl->add(new Def(3, $t['is_helpdeskvisible'], 'boolean', 'is_helpdeskvisible', fillable: true));
    $defColl->add(new Def(74, $t['is_incident'], 'boolean', 'is_incident', fillable: true));
    $defColl->add(new Def(75, $t['is_request'], 'boolean', 'is_request', fillable: true));
    $defColl->add(new Def(76, $t['is_problem'], 'boolean', 'is_problem', fillable: true));
    $defColl->add(new Def(85, $t['is_change'], 'boolean', 'is_change', fillable: true));
    $defColl->add(new Def(1001, $t['is_knowledge'], 'boolean', 'is_knowledge', fillable: true));
    $defColl->add(new Def(1002, $t['is_form'], 'boolean', 'is_form', fillable: true));
    $defColl->add(new Def(
      72,
      $t['tickettemplateDemand'],
      'dropdown_remote',
      'tickettemplateDemand',
      dbname: 'tickettemplate_id_demand',
      itemtype: '\App\Models\Tickettemplate',
      fillable: true
    ));
    $defColl->add(new Def(
      73,
      $t['tickettemplateIncident'],
      'dropdown_remote',
      'tickettemplateIncident',
      dbname: 'tickettemplate_id_incident',
      itemtype: '\App\Models\Tickettemplate',
      fillable: true
    ));
    $defColl->add(new Def(
      100,
      $t['changetemplate'],
      'dropdown_remote',
      'changetemplate',
      dbname: 'changetemplate_id',
      itemtype: '\App\Models\Changetemplate',
      fillable: true
    ));
    $defColl->add(new Def(
      101,
      $t['problemtemplate'],
      'dropdown_remote',
      'problemtemplate',
      dbname: 'problemtemplate_id',
      itemtype: '\App\Models\Problemtemplate',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      80,
      $t['entity'],
      'dropdown_remote',
      'entity',
      dbname: 'entity_id',
      itemtype: '\App\Models\Entity',
      display: false
    ));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;

    /*
    $tab[] = [
      'id'   => 'common',
      'name' => __('Characteristics')
    ];

    $tab[] = [
      'id'                => '1',
      'table'              => $this->getTable(),
      'field'              => 'completename',
      'name'               => __('Complete name'),
      'datatype'           => 'itemlink',
      'massiveaction'      => false
    ];

    $tab[] = [
      'id'                => '2',
      'table'              => $this->getTable(),
      'field'              => 'id',
      'name'               => __('ID'),
      'massiveaction'      => false,
      'datatype'           => 'number'
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    $tab[] = [
      'id'                 => '77',
      'table'              => 'glpi_tickets',
      'field'              => 'id',
      'name'               => _x('quantity', 'Number of tickets'),
      'datatype'           => 'count',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '78',
      'table'              => 'glpi_problems',
      'field'              => 'id',
      'name'               => _x('quantity', 'Number of problems'),
      'datatype'           => 'count',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '98',
      'table'              => 'glpi_changes',
      'field'              => 'id',
      'name'               => _x('quantity', 'Number of changes'),
      'datatype'           => 'count',
      'forcegroupby'       => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('category', 'Category', 'Categories', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('category', 'Category', 'Categories', 2),
        'icon' => 'edit',
        'link' => $rootUrl . '/categories',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
