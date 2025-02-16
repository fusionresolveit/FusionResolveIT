<?php

declare(strict_types=1);

namespace App\Traits\Definitions;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait ItemOperatingsystem
{
  public static function getDefinitionOperatingSystem(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'architecture' => $translator->translatePlural('Architecture', 'Architectures', 1),
      'kernelversion' => $translator->translatePlural('Kernel', 'Kernels', 1),
      'version' => $translator->translatePlural('Version', 'Versions', 1),
      'servicepack' => $translator->translatePlural('Service pack', 'Service packs', 1),
      'edition' => $translator->translatePlural('Edition', 'Editions', 1),
      'lts' => $translator->translate('Long-Term Support (LTS)'),
      'licenseid' => $translator->translate('Product ID'),
      'license_number' => $translator->translate('Serial number'),
      'hostid' => $translator->translate('Host ID'),
      'oscomment' => $translator->translate('Operating System comment'),
      'winowner' => $translator->translate('Owner'),
      'wincompany' => $translator->translate('Company'),
      'installationdate' => $translator->translate('Installation date'),
      'os' => $translator->translate('Operating System'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Definition(
      4,
      $t['os'],
      'dropdown_remote',
      'id',
      dbname: 'operatingsystem_id',
      itemtype: '\App\Models\Operatingsystem'
    ));
    $defColl->add(new Definition(
      2,
      $t['architecture'],
      'dropdown_remote',
      'architecture',
      dbname: 'operatingsystemarchitecture_id',
      itemtype: '\App\Models\Operatingsystemarchitecture',
      isPivot: true
    ));
    $defColl->add(new Definition(
      3,
      $t['kernelversion'],
      'dropdown_remote',
      'kernelversion',
      dbname: 'operatingsystemkernelversion_id',
      itemtype: '\App\Models\Operatingsystemkernelversion',
      isPivot: true
    ));
    $defColl->add(new Definition(
      4,
      $t['version'],
      'dropdown_remote',
      'version',
      dbname: 'operatingsystemversion_id',
      itemtype: '\App\Models\Operatingsystemversion',
      isPivot: true
    ));
    $defColl->add(new Definition(
      5,
      $t['servicepack'],
      'dropdown_remote',
      'servicepack',
      dbname: 'operatingsystemservicepack_id',
      itemtype: '\App\Models\Operatingsystemservicepack',
      isPivot: true
    ));
    $defColl->add(new Definition(
      6,
      $t['edition'],
      'dropdown_remote',
      'edition',
      dbname: 'operatingsystemedition_id',
      itemtype: '\App\Models\Operatingsystemedition',
      isPivot: true
    ));
    $defColl->add(new Definition(7, $t['licenseid'], 'input', 'licenseid', isPivot: true));
    $defColl->add(new Definition(1001, $t['hostid'], 'input', 'hostid', isPivot: true));
    $defColl->add(new Definition(1001, $t['oscomment'], 'input', 'oscomment', isPivot: true));
    $defColl->add(new Definition(1001, $t['winowner'], 'input', 'winowner', isPivot: true));
    $defColl->add(new Definition(1001, $t['wincompany'], 'input', 'wincompany', isPivot: true));
    $defColl->add(new Definition(1001, $t['license_number'], 'input', 'license_number', isPivot: true));
    $defColl->add(new Definition(1001, $t['installationdate'], 'date', 'installationdate', isPivot: true));

    return $defColl;
  }
}
