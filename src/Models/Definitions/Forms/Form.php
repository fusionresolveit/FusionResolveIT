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
      'content' => $translator->translatePlural('Header', 'Headers', 1),
      'comment' => $translator->translate('Description'),
      'category' => $translator->translate('Category'),
      'is_active' => $translator->translate('Active'),
      'is_recursive' => $translator->translate('Child entities'),
      'is_homepage' => $translator->translate('Default form in service catalog'),
      'icon' => $translator->translate('Icon'),
      'icon_color' => $translator->translate('Icon color'),
      'date_creation' => $translator->translate('Creation date'),
      'date_mod' => $translator->translate('Last update'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name'));
    $defColl->add(new Def(2, $t['content'], 'textarea', 'content'));
    $defColl->add(new Def(3, $t['comment'], 'textarea', 'comment'));
    $defColl->add(new Def(
      4,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'category_id',
      itemtype: '\App\Models\Category'
    ));
    $defColl->add(new Def(5, $t['is_active'], 'boolean', 'is_active', dbname: 'is_active'));
    $defColl->add(new Def(7, $t['is_recursive'], 'boolean', 'is_recursive', dbname: 'is_recursive'));
    $defColl->add(new Def(9, $t['is_homepage'], 'boolean', 'is_homepage', dbname: 'is_homepage'));
    $defColl->add(new Def(10, $t['icon'], 'input', 'icon'));
    $defColl->add(new Def(11, $t['icon_color'], 'input', 'icon_color'));
    $defColl->add(new Def(15, $t['date_creation'], 'datetime', 'date_creation', readonly: true));
    $defColl->add(new Def(16, $t['date_mod'], 'datetime', 'date_mod', readonly: true));

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
