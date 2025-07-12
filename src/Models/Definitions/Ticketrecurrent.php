<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Ticketrecurrent
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Title'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
      'is_active' => pgettext('global', 'Active'),
      'begin_date' => pgettext('global', 'Start date'),
      'end_date' => pgettext('global', 'End date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));
    $defColl->add(new Def(11, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(13, $t['begin_date'], 'datetime', 'begin_date', fillable: true));
    $defColl->add(new Def(17, $t['end_date'], 'datetime', 'end_date', fillable: true));

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



    $tab[] = [
        'id'                 => '12',
        'table'              => 'glpi_tickettemplates',
        'field'              => 'name',
        'name'               => _n('Ticket template', 'Ticket templates', 1),
        'datatype'           => 'itemlink'
    ];

    $tab[] = [
        'id'                 => '15',
        'table'              => $this->getTable(),
        'field'              => 'periodicity',
        'name'               => __('Periodicity'),
        'datatype'           => 'specific'
    ];

    $tab[] = [
        'id'                 => '14',
        'table'              => $this->getTable(),
        'field'              => 'create_before',
        'name'               => __('Preliminary creation'),
        'datatype'           => 'timestamp'
    ];

    $tab[] = [
        'id'                 => '18',
        'table'              => 'glpi_calendars',
        'field'              => 'name',
        'name'               => _n('Calendar', 'Calendars', 1),
        'datatype'           => 'itemlink'
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
        'title' => npgettext('global', 'Recurrent ticket', 'Recurrent tickets', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('ticket', 'Information', 'Information', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
