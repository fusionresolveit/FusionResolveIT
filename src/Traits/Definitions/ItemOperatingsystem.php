<?php

declare(strict_types=1);

namespace App\Traits\Definitions;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;

trait ItemOperatingsystem
{
  public static function getDefinitionOperatingSystem(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'architecture' => npgettext('global', 'Operating system architecture', 'Operating system architectures', 1),
      'kernelversion' => npgettext('global', 'Kernel', 'Kernels', 1),
      'version' => npgettext('global', 'Version', 'Versions', 1),
      'servicepack' => npgettext('global', 'Service pack', 'Service packs', 1),
      'edition' => npgettext('global', 'Edition', 'Editions', 1),
      'lts' => pgettext('operating system', 'Long-Term Support (LTS)'),
      'licenseid' => pgettext('operating system', 'Product ID'),
      'license_number' => pgettext('inventory device', 'Serial number'),
      'hostid' => pgettext('operating system', 'Host ID'),
      'oscomment' => pgettext('operating system', 'Operating System comment'),
      'winowner' => pgettext('operating system', 'Owner'),
      'wincompany' => pgettext('operating system', 'Company'),
      'installationdate' => pgettext('global', 'Installation date'),
      'os' => npgettext('inventory device', 'Operating System', 'Operating System', 1),
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
