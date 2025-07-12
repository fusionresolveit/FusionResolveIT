<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Document
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'categorie' => pgettext('document', 'Heading'),
      'filename' => pgettext('document', 'File'),
      'link' => pgettext('document', 'Web link'),
      'mime' => pgettext('document', 'MIME type'),
      'tag' => pgettext('document', 'Tag'),
      'sha1sum' => sprintf(
        pgettext('global', '%1$s (%2$s)'),
        pgettext('global', 'Checksum'),
        pgettext('global', 'SHA1')
      ),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_recursive' => pgettext('global', 'Child entities'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      7,
      $t['categorie'],
      'dropdown_remote',
      'categorie',
      dbname: 'documentcategory_id',
      itemtype: '\App\Models\Documentcategory',
      fillable: true
    ));
    $defColl->add(new Def(3, $t['filename'], 'input', 'filename', readonly: true));
    $defColl->add(new Def(4, $t['link'], 'input', 'link', fillable: true));
    $defColl->add(new Def(5, $t['mime'], 'input', 'mime', fillable: true));
    $defColl->add(new Def(6, $t['tag'], 'input', 'tag', readonly: true));
    $defColl->add(new Def(20, $t['sha1sum'], 'input', 'sha1sum', readonly: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(86, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
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
      'id'                 => '2',
      'table'              => $this->getTable(),
      'field'              => 'id',
      'name'               => __('ID'),
      'massiveaction'      => false,
      'datatype'           => 'number'
    ];

    $tab[] = [
      'id'                 => '72',
      'table'              => 'glpi_documents_items',
      'field'              => 'id',
      'name'               => _x('quantity', 'Number of associated items'),
      'forcegroupby'       => true,
      'usehaving'          => true,
      'datatype'           => 'count',
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child'
      ]
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Document', 'Documents', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Associated item', 'Associated items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/associateditems',
      ],
      [
        'title' => npgettext('global', 'Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => npgettext('global', 'Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
