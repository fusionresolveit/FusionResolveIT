<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Form
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'content' => pgettext('global', 'Description'),
      'comment' => npgettext('global', 'Comment', 'Comments', 1),
      'category' => npgettext('global', 'Category', 'Categories', 1),
      'is_active' => pgettext('global', 'Active'),
      'is_recursive' => pgettext('global', 'Child entities'),
      'is_homepage' => pgettext('form', 'Display on home page'),
      'icon' => pgettext('form', 'Icon'),
      'icon_color' => pgettext('form', 'Icon color'),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(2, $t['content'], 'textarea', 'content', fillable: true));
    $defColl->add(new Def(3, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      4,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'category_id',
      itemtype: '\App\Models\Category',
      fillable: true
    ));
    $defColl->add(new Def(5, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(7, $t['is_recursive'], 'boolean', 'is_recursive', fillable: true));
    $defColl->add(new Def(9, $t['is_homepage'], 'boolean', 'is_homepage', fillable: true));
    $defColl->add(new Def(10, $t['icon'], 'input', 'icon', fillable: true));
    $defColl->add(new Def(11, $t['icon_color'], 'input', 'icon_color', fillable: true));
    $defColl->add(new Def(16, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(15, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
    // [
    //   'id'    => 6,
    //   'title' => npgettext('global', 'Entity', 'Entities', 1),
    //   'type'  => 'dropdown_remote',
    //   'name'  => 'completename',
    //   'itemtype' => '\App\Models\Entity',
    // ],
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Form', 'Forms', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Section', 'Sections', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/sections',
      ],
      [
        'title' => npgettext('global', 'Question', 'Questions', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/questions',
      ],
      [
        'title' => npgettext('form', 'Answer', 'Answers', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/answers',
      ],
      // [
      //   'title' => 'Access types',
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
      // [
      //   'title' => npgettext('target', 'Target', 'Targets', 2),
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
      // [
      //   'title' => 'Preview',
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
      // [
      //   'title' => 'Form answer',
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
      // [
      //   'title' => 'Boutique',
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
    ];
  }
}
