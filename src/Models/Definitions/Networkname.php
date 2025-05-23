<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Networkname
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'fqdn' => $translator->translatePlural('Internet domain', 'Internet domains', 1),
      'comment' => $translator->translate('Comments'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      12,
      $t['fqdn'],
      'dropdown_remote',
      'fqdn',
      dbname: 'fqdn_id',
      itemtype: '\App\Models\Fqdn',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    /*
      $tab[] = [
        'id'                 => '13',
        'table'              => 'glpi_ipaddresses',
        'field'              => 'name',
        'name'               => IPAddress::getTypeName(1),
        'joinparams'         => [
            'jointype'           => 'itemtype_item'
        ],
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'datatype'           => 'dropdown'
      ];

      $tab[] = [
        'id'                 => '20',
        'table'              => $this->getTable(),
        'field'              => 'itemtype',
        'name'               => _n('Type', 'Types', 1),
        'datatype'           => 'itemtype',
        'massiveaction'      => false
      ];

      $tab[] = [
        'id'                 => '21',
        'table'              => $this->getTable(),
        'field'              => 'items_id',
        'name'               => __('ID'),
        'datatype'           => 'integer',
        'massiveaction'      => false
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
        'title' => $translator->translatePlural('Network name', 'Network names', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Network alias', 'Network aliases', 1),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/networkalias',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
