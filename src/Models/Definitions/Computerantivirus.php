<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Computerantivirus
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'manufacturer' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
      'antivirus_version' => $translator->translate('Antivirus version'),
      'date_expiration' => $translator->translate('Expiration date'),
      'signature_version' => $translator->translate('Signature database version'),
      'is_active' => $translator->translate('Active'),
      'is_uptodate' => $translator->translate('Up to date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      2,
      $t['manufacturer'],
      'dropdown_remote',
      'manufacturer',
      dbname: 'manufacturer_id',
      itemtype: '\App\Models\Manufacturer',
      fillable: true
    ));
    $defColl->add(new Def(3, $t['antivirus_version'], 'input', 'antivirus_version', fillable: true));
    $defColl->add(new Def(4, $t['date_expiration'], 'date', 'date_expiration', fillable: true));
    $defColl->add(new Def(5, $t['signature_version'], 'input', 'signature_version', fillable: true));
    $defColl->add(new Def(6, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(7, $t['is_uptodate'], 'boolean', 'is_uptodate', fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [];
  }
}
