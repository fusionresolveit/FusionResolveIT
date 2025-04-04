<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Knowbaseitem
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'category' => $translator->translate('Category'),

      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      4,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'category_id',
      itemtype: '\App\Models\Category',
      fillable: true
    ));

    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));


    // `knowbaseitemcategory_id` int(11) NOT NULL DEFAULT 0,
    // `answer` longtext DEFAULT NULL,
    // `is_faq` tinyint(1) NOT NULL DEFAULT 0,
    // `user_id` int(11) NOT NULL DEFAULT 0,
    // `view` int(11) NOT NULL DEFAULT 0,
    // `date` datetime DEFAULT NULL,
    // `begin_date` datetime DEFAULT NULL,
    // `end_date` datetime DEFAULT NULL,

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
    ];
  }
}
