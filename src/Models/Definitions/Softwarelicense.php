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
    $t = [
      'software' => npgettext('global', 'Software', 'Software', 1),
      'name' => pgettext('global', 'Name'),
      'location' => npgettext('global', 'Location', 'Locations', 1),
      'serial' => pgettext('inventory device', 'Serial number'),
      'number' => pgettext('software', 'Number'),
      'softwarelicensetype' =>  npgettext('global', 'Type', 'Types', 1),
      'expire' => pgettext('software', 'Expiration'),
      'is_valid' => pgettext('software', 'Valid'),
      'allow_overquota' => pgettext('software', 'Allow Over-Quota'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'usertech' => pgettext('inventory device', 'Technician in charge of the hardware'),
      'grouptech' => pgettext('inventory device', 'Group in charge of the hardware'),
      'user' => npgettext('global', 'User', 'Users', 1),
      'group' => npgettext('global', 'Group', 'Groups', 1),
      'state' => pgettext('inventory device', 'Status'),
      'otherserial' => pgettext('inventory device', 'Inventory number'),
      'is_recursive' => pgettext('global', 'Child entities'),
      'softwareversionsBuy' => pgettext('software', 'Purchase version'),
      'softwareversionsUse' => pgettext('software', 'Version in use'),
      'manufacturer' => npgettext('global', 'Manufacturer', 'Manufacturers', 1),
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
        ['-1' => pgettext('software', 'Unlimited')],
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
    //   'title' => pgettext('global', 'Template name'),
    //   'type'  => 'input',
    //   'name'  => 'template_name',
    // ],
    // [
    //   'id'    => 80,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
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
    return [
      [
        'title' => npgettext('global', 'License', 'Licenses', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'License', 'Licenses', 2),
        'icon' => 'key',
        'link' => $rootUrl . '/licenses',
      ],
      [
        'title' => pgettext('software', 'Summary'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => '',
      ],
      [
        'title' => pgettext('global', 'Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => npgettext('global', 'Contract', 'Contracts', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => pgettext('global', 'Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => pgettext('global', 'ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
      ],
      [
        'title' => npgettext('global', 'Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => npgettext('global', 'Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
