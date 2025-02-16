<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Appliance
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name'          => $translator->translate('Name'),
      'state'         => $translator->translate('Status'),
      'location'      => $translator->translatePlural('Location', 'Locations', 1),
      'manufacturer'  => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
      'appliancetype' => $translator->translatePlural('Appliance type', 'Appliance types', 1),
      'environment'   => $translator->translatePlural('Appliance environment', 'Appliance environments', 1),
      'usertech'     => $translator->translate('Technician in charge'),
      'grouptech'    => $translator->translate('Group in charge'),
      'user'          => $translator->translatePlural('User', 'Users', 1),
      'group'         => $translator->translatePlural('Group', 'Groups', 1),
      'serial'        => $translator->translate('Serial number'),
      'otherserial'   => $translator->translate('Inventory number'),
      'associable'    => $translator->translate('Associable to a ticket'),
      'comment'       => $translator->translate('Comments'),
      'recursive'     => $translator->translate('Child entities'),
      'updated'       => $translator->translate('Last update'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      32,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true,
    ));
    $defColl->add(new Def(
      3,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true,
    ));
    $defColl->add(new Def(
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer',
      fillable: true,
    ));
    $defColl->add(new Def(
      11,
      $t['appliancetype'],
      'dropdown_remote',
      'type',
      dbname: 'appliancetype_id',
      itemtype: '\App\Models\Appliancetype',
      fillable: true,
    ));
    $defColl->add(new Def(
      10,
      $t['environment'],
      'dropdown_remote',
      'environment',
      dbname: 'applianceenvironment_id',
      itemtype: '\App\Models\Applianceenvironment',
      fillable: true,
    ));
    $defColl->add(new Def(
      24,
      $t['usertech'],
      'dropdown_remote',
      'usertech',
      dbname: 'user_id_tech',
      itemtype: '\App\Models\User',
      fillable: true,
    ));
    $defColl->add(new Def(
      49,
      $t['grouptech'],
      'dropdown_remote',
      'grouptech',
      dbname: 'group_id_tech',
      itemtype: '\App\Models\Group',
      fillable: true,
    ));
    $defColl->add(new Def(
      6,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true,
    ));
    $defColl->add(new Def(
      8,
      $t['group'],
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true,
    ));
    $defColl->add(new Def(12, $t['serial'], 'input', 'serial', fillable: true));
    $defColl->add(new Def(13, $t['otherserial'], 'input', 'otherserial', fillable: true));
    $defColl->add(new Def(61, $t['associable'], 'boolean', 'is_helpdesk_visible', fillable: true));
    $defColl->add(new Def(4, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(7, $t['recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(9, $t['updated'], 'datetime', 'updated_at', readonly: true));
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

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    $tab[] = [
      'id'            => '5',
      'table'         =>  Appliance_Item::getTable(),
      'field'         => 'items_id',
      'name'               => _n('Associated item', 'Associated items', 2),
      'nosearch'           => true,
      'massiveaction' => false,
      'forcegroupby'  =>  true,
      'additionalfields'   => ['itemtype'],
      'joinparams'    => ['jointype' => 'child']
    ];

    $tab[] = [
      'id'            => '31',
      'table'         => self::getTable(),
      'field'         => 'id',
      'name'          => __('ID'),
      'datatype'      => 'number',
      'massiveaction' => false
    ];

    $tab[] = [
      'id'            => '81',
      'table'         => Entity::getTable(),
      'field'         => 'entities_id',
      'name'          => sprintf('%s-%s', Entity::getTypeName(1), __('ID'))
    ];

    $tab = array_merge($tab, Certificate::rawSearchOptionsToAdd());
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
        'title' => $translator->translatePlural('Appliance', 'Appliances', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Impact analysis'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
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
        'title' => $translator->translate('Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => $translator->translatePlural('Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => $translator->translatePlural('Domain', 'Domains', 2),
        'icon' => 'globe americas',
        'link' => $rootUrl . '/domains',
      ],
      [
        'title' => $translator->translate('Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowbaseitems',
      ],
      [
        'title' => $translator->translate('ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
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
