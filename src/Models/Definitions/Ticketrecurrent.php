<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Ticketrecurrent
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Title'),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
      'is_active' => $translator->translate('Active'),
      'begin_date' => $translator->translate('Start date'),
      'end_date' => $translator->translate('End date'),
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;
    return [
      [
        'title' => $translator->translate('Recurrent tickets'),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Information', 'Information', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
