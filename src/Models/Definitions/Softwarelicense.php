<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;
use App\Traits\Definitions\Infocoms;

class Softwarelicense
{
  use Infocoms;

  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'software' => $translator->translatePlural('Software', 'Softwares', 1),
      'name' => $translator->translate('Name'),
      'location' => $translator->translatePlural('Location', 'Locations', 1),
      'serial' => $translator->translate('Serial number'),
      'number' => $translator->translate('Number'),
      'softwarelicensetype' => $translator->translatePlural('Type', 'Types', 1),
      'expire' => $translator->translate('Expiration'),
      'is_valid' => $translator->translate('Valid'),
      'allow_overquota' => $translator->translate('Allow Over-Quota'),
      'comment' => $translator->translate('Comments'),
      'usertech' => $translator->translate('Technician in charge of the hardware'),
      'grouptech' => $translator->translate('Group in charge of the hardware'),
      'user' => $translator->translatePlural('User', 'Users', 1),
      'group' => $translator->translatePlural('Group', 'Groups', 1),
      'state' => $translator->translate('Status'),
      'otherserial' => $translator->translate('Inventory number'),
      'is_recursive' => $translator->translate('Child entities'),
      'softwareversionsBuy' => $translator->translate('Purchase version'),
      'softwareversionsUse' => $translator->translate('Version in use'),
      'manufacturer' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(
      10,
      $t['software'],
      'dropdown_remote',
      'software',
      dbname: 'software_id',
      itemtype: '\App\Models\Software',
      readonly: true
    ));
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      3,
      $t['location'],
      'dropdown_remote',
      'location',
      dbname: 'location_id',
      itemtype: '\App\Models\Location',
      fillable: true
    ));
    $defColl->add(new Def(11, $t['serial'], 'input', 'serial', fillable: true));
    $defColl->add(new Def(
      4,
      $t['number'],
      'dropdown',
      'number',
      dbname: 'number',
      values: \App\v1\Controllers\Dropdown::generateNumbers(
        0,
        10000,
        1,
        ['-1' => $translator->translate('Unlimited')],
        ''
      ),
      fillable: true
    ));
    $defColl->add(new Def(
      5,
      $t['softwarelicensetype'],
      'dropdown_remote',
      'softwarelicensetype',
      dbname: 'softwarelicensetype_id',
      itemtype: '\App\Models\Softwarelicensetype',
      fillable: true
    ));
    $defColl->add(new Def(8, $t['expire'], 'date', 'expire', fillable: true));
    $defColl->add(new Def(9, $t['is_valid'], 'boolean', 'is_valid', fillable: true));
    $defColl->add(new Def(168, $t['allow_overquota'], 'boolean', 'allow_overquota', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      24,
      $t['usertech'],
      'dropdown_remote',
      'usertech',
      dbname: 'user_id_tech',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      49,
      $t['grouptech'],
      'dropdown_remote',
      'grouptech',
      dbname: 'group_id_tech',
      itemtype: '\App\Models\Group',
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
    $defColl->add(new Def(
      31,
      $t['state'],
      'dropdown_remote',
      'state',
      dbname: 'state_id',
      itemtype: '\App\Models\State',
      fillable: true
    ));
    $defColl->add(new Def(162, $t['otherserial'], 'input', 'otherserial', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(
      6,
      $t['softwareversionsBuy'],
      'dropdown_remote',
      'softwareversionsBuy',
      dbname: 'softwareversion_id_buy',
      itemtype: '\App\Models\Softwareversion',
      fillable: true
    ));
    $defColl->add(new Def(
      7,
      $t['softwareversionsUse'],
      'dropdown_remote',
      'softwareversionsUse',
      dbname: 'softwareversion_id_use',
      itemtype: '\App\Models\Softwareversion',
      fillable: true
    ));
    $defColl->add(new Def(
      23,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer',
      fillable: true
    ));

    return $defColl;

    // [
    //   'id'    => 61,
    //   'title' => $translator->translate('Template name'),
    //   'type'  => 'input',
    //   'name'  => 'template_name',
    // ],
    // [
    //   'id'    => 80,
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],




/*


    $tab[] = [
        'id'                 => '13',
        'table'              => $this->getTable(),
        'field'              => 'completename',
        'name'               => __('Father'),
        'datatype'           => 'itemlink',
        'forcegroupby'       => true,
        'joinparams'        => ['condition' => "AND 1=1"]
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
        'title' => $translator->translatePlural('License', 'Licenses', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('License', 'Licenses', 2),
        'icon' => 'key',
        'link' => $rootUrl . '/licenses',
      ],
      [
        'title' => $translator->translate('Summary'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
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
        'title' => $translator->translate('Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => $translator->translate('ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translatePlural('Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
