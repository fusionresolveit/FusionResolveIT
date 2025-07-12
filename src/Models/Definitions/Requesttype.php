<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Requesttype
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_active' => pgettext('global', 'Active'),
      'is_helpdesk_default' => pgettext('ticket', 'Default for tickets'),
      'is_followup_default' => pgettext('ticket', 'Default for followups'),
      'is_mail_default' => pgettext('ticket', 'Default for mail recipients'),
      'is_mailfollowup_default' => pgettext('ticket', 'Default for followup mail recipients'),
      'is_ticketheader' => pgettext('ticket', 'Request source visible for tickets'),
      'is_itilfollowup' => pgettext('ticket', 'Request source visible for followups'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(8, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(14, $t['is_helpdesk_default'], 'boolean', 'is_helpdesk_default', fillable: true));
    $defColl->add(new Def(182, $t['is_followup_default'], 'boolean', 'is_followup_default', fillable: true));
    $defColl->add(new Def(15, $t['is_mail_default'], 'boolean', 'is_mail_default', fillable: true));
    $defColl->add(new Def(183, $t['is_mailfollowup_default'], 'boolean', 'is_mailfollowup_default', fillable: true));
    $defColl->add(new Def(180, $t['is_ticketheader'], 'boolean', 'is_ticketheader', fillable: true));
    $defColl->add(new Def(181, $t['is_itilfollowup'], 'boolean', 'is_itilfollowup', fillable: true));
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
        'title' => npgettext('global', 'Request source', 'Request sources', 1),
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
