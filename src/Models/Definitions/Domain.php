<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Domain
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'type' => $translator->translatePlural('Type', 'Types', 1),
      'usertech' => $translator->translate('Technician in charge'),
      'grouptech' => $translator->translate('Group in charge'),
      'created_at' => $translator->translate('Creation date'),
      'date_expiration' => $translator->translate('Expiration date'),
      'comment' => $translator->translate('Comments'),
      'others' => $translator->translate('Others'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      2,
      $t['type'],
      'dropdown_remote',
      'type',
      dbname: 'domaintype_id',
      itemtype: '\App\Models\Domaintype',
      fillable: true
    ));
    $defColl->add(new Def(
      3,
      $t['usertech'],
      'dropdown_remote',
      'usertech',
      dbname: 'user_id_tech',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      10,
      $t['grouptech'],
      'dropdown_remote',
      'grouptech',
      dbname: 'group_id_tech',
      itemtype: '\App\Models\Group',
      fillable: true
    ));
    $defColl->add(new Def(5, $t['created_at'], 'date', 'created_at', readonly: true));
    $defColl->add(new Def(6, $t['date_expiration'], 'date', 'date_expiration', fillable: true));
    $defColl->add(new Def(7, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(9, $t['others'], 'input', 'others', fillable: true));
    $defColl->add(new Def(18, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(12, $t['updated_at'], 'datetime', 'updated_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 80,
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],

    /*
    $tab = [];

    $tab[] = [
      'id'                 => 'common',
      'name'               => self::getTypeName(2)
    ];

    $tab[] = [
    'id'                 => '8',
      'table'              => 'glpi_domains_items',
      'field'              => 'items_id',
      'nosearch'           => true,
      'massiveaction'      => false,
      'name'               => _n('Associated item', 'Associated items', Session::getPluralNumber()),
      'forcegroupby'       => true,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    $tab[] = [
      'id'                 => '30',
      'table'              => $this->getTable(),
      'field'              => 'id',
      'name'               => __('ID'),
      'datatype'           => 'number'
    ];

    $tab[] = [
      'id'                 => '81',
      'table'              => 'glpi_entities',
      'field'              => 'entities_id',
      'name'               => __('Entity-ID')
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
        'title' => $translator->translatePlural('Domain', 'Domains', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Impact analysis'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Record', 'Records', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/records',
      ],
      [
        'title' => $translator->translatePlural('Associated item', 'Associated items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/attacheditems',
      ],
      [
        'title' => $translator->translate('Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => $translator->translate('ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
      ],
      [
        'title' => $translator->translatePlural('Contract', 'Contract', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => $translator->translatePlural('External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
