<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Document
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'categorie' => $translator->translate('Heading'),
      'filename' => $translator->translate('File'),
      'link' => $translator->translate('Web link'),
      'mime' => $translator->translate('MIME type'),
      'tag' => $translator->translate('Tag'),
      'sha1sum' => sprintf(
        $translator->translate('%1$s (%2$s)'),
        $translator->translate('Checksum'),
        $translator->translate('SHA1')
      ),
      'comment' => $translator->translate('Comments'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Document', 'Documents', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Associated item', 'Associated items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/associateditems',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
