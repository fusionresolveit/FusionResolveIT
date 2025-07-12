<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Documenttype
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'ext' => pgettext('document', 'Extension'),
      'mime' => pgettext('document', 'MIME type'),
      'is_uploadable' => pgettext('document', 'Authorized upload'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(3, $t['ext'], 'input', 'ext', fillable: true));
    $defColl->add(new Def(4, $t['mime'], 'input', 'mime', fillable: true));
    $defColl->add(new Def(5, $t['is_uploadable'], 'boolean', 'is_uploadable', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 6,
    //   'title' => 'Icon',
    //   'type'  => 'input',
    //   'name'  => 'icon',
    // ],
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Document type', 'Document types', 1),
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
