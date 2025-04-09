<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Form
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'content' => $translator->translate('Description'),
      'comment' => $translator->translatePlural('Comment', 'Comments', 1),
      'category' => $translator->translate('Category'),
      'is_active' => $translator->translate('Active'),
      'is_recursive' => $translator->translate('Child entities'),
      'is_homepage' => $translator->translate('Display on home page'),
      'icon' => $translator->translate('Icon'),
      'icon_color' => $translator->translate('Icon color'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
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
    //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Form', 'Forms', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Section', 'Sections', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/sections',
      ],
      [
        'title' => $translator->translatePlural('Question', 'Questions', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/questions',
      ],
      [
        'title' => $translator->translatePlural('Answer', 'Answers', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/answers',
      ],
      // [
      //   'title' => $translator->translatePlural('Access type', 'Access types', 2),
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
      // [
      //   'title' => $translator->translatePlural('Target', 'Targets', 2),
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
      // [
      //   'title' => $translator->translate('Preview'),
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
      // [
      //   'title' => $translator->translatePlural('Form answer', 'Form answers', 1),
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
      // [
      //   'title' => $translator->translate('Boutique'),
      //   'icon' => 'caret square down outline',
      //   'link' => '',
      // ],
    ];
  }
}
