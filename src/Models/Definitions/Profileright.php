<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Profileright
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'id' => pgettext('global', 'Id'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false));

    return $defColl;
  }
}
