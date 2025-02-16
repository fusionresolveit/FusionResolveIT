<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Section
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'date_creation' => $translator->translate('Creation date'),
      'date_mod' => $translator->translate('Last update'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name'));
    $defColl->add(new Def(15, $t['date_creation'], 'datetime', 'date_creation', readonly: true));
    $defColl->add(new Def(16, $t['date_mod'], 'datetime', 'date_mod', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Section', 'Sections', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Form', 'Forms', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/forms',
      ],
      [
        'title' => $translator->translatePlural('Question', 'Questions', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/questions',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
