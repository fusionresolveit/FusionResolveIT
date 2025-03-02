<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Link
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'link' => $translator->translate('Link or filename'),
      'open_window' => $translator->translate('Open in a new window'),
      'data' => $translator->translate('File content'),
      'is_recursive' => $translator->translate('Child entities'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(3, $t['link'], 'input', 'link', fillable: true));
    $defColl->add(new Def(103, $t['open_window'], 'boolean', 'open_window', fillable: true));
    $defColl->add(new Def(104, $t['data'], 'textarea', 'data', fillable: true));
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
  }

  public static function getValidTags(): string
  {
    global $translator;

    $tags = ['[LOGIN]', '[ID]', '[NAME]', '[LOCATION]', '[LOCATIONID]', '[IP]',
      '[MAC]', '[NETWORK]', '[DOMAIN]', '[SERIAL]', '[OTHERSERIAL]',
      '[USER]', '[GROUP]', '[REALNAME]', '[FIRSTNAME]'
    ];

    $ret = '';

    $count = count($tags);
    $i = 0;

    foreach ($tags as $tag)
    {
      $ret .= $tag;
      $ret .= "&nbsp;";
      $i++;
      if ($i % 8 == 0)
      {
        $ret = $ret . "<br>";
      }
    }

    $ret = $ret . "<br>" . $translator->translate('or') . "<br>[FIELD:<i>" .
           $translator->translate('field name in DB') . "</i>] (" . $translator->translate('Example:') .
           " [FIELD:name], [FIELD:content], ...)";

    return $ret;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('External link', 'External links', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Associated item type', 'Associated item types', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/associateditemtypes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
