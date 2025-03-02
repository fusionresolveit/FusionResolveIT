<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Notificationtemplatetranslation
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'language' => $translator->translate('Language'),
      'subject' => $translator->translate('Subject'),
      'content_text' => $translator->translate('Text format'),
      'content_html' => $translator->translate('HTML format'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['language'], 'input', 'language', fillable: true));
    $defColl->add(new Def(2, $t['subject'], 'input', 'subject', fillable: true));
    $defColl->add(new Def(3, $t['content_text'], 'textarea', 'content_text', fillable: true));
    $defColl->add(new Def(4, $t['content_html'], 'textarea', 'content_html', fillable: true));

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
