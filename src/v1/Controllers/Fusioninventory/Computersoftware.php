<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

final class Computersoftware extends \App\v1\Controllers\Common
{
  public static function parse(object $dataObj, \App\Models\Computer $computer)
  {
    $vs = [
      'ARCH'              => Validation::attrStrNotempty('ARCH'),
      'FROM'              => Validation::attrStrNotempty('FROM'),
      'GUID'              => Validation::attrStrNotempty('GUID'),
      'HELPLINK'          => Validation::attrStrNotempty('HELPLINK'),
      'INSTALLDATE'       => Validation::attrDate('INSTALLDATE', 'd/m/Y'),
      'NAME'              => Validation::attrStrNotempty('NAME'),
      'COMMENTS'          => Validation::attrStrNotempty('COMMENTS'),
      'PUBLISHER'         => Validation::attrStrNotempty('PUBLISHER'),
      'UNINSTALL_STRING'  => Validation::attrStrNotempty('UNINSTALL_STRING'),
      'VERSION'           => Validation::attrStrNotempty('VERSION'),
    ];

    if (property_exists($dataObj->CONTENT, 'SOFTWARES'))
    {
      $versionIds = [];
      $content = Common::getArrayData($dataObj->CONTENT->SOFTWARES);

      foreach ($content as $contentSoftware)
      {
        if ($vs['NAME']->isValid($contentSoftware))
        {
          $version = 'N/A';
          if ($vs['VERSION']->isValid($contentSoftware))
          {
            $version = Common::cleanString($contentSoftware->VERSION);
          }

          $manufacturer_id = 0;
          if ($vs['PUBLISHER']->isValid($contentSoftware))
          {
            $manufacturer = \App\Models\Manufacturer::withoutEagerLoads()->firstOrCreate(
              [
                'name' => Common::cleanString($contentSoftware->PUBLISHER),
              ],
            );
            $manufacturer_id = $manufacturer->id;
          }

          $comments = null;
          if ($vs['COMMENTS']->isValid($contentSoftware))
          {
            $comments = Common::cleanString($contentSoftware->COMMENTS);
          }

          $software = \App\Models\Software::withoutEagerLoads()->firstOrCreate(
            [
              'name'      => Common::cleanString($contentSoftware->NAME),
              'entity_id' => 1
            ],
            [
              'manufacturer_id' => $manufacturer_id,
              'comment'         => $comments,
            ]
          );

          $softwareversion = \App\Models\Softwareversion::withoutEagerLoads()->firstOrCreate(
            [
              'name'        => $version,
              'entity_id'   => 1,
              'software_id' => $software->id,
            ],
            [] // operatingsystem?
          );
          $dateInstall = null;
          if ($vs['INSTALLDATE']->isValid($contentSoftware))
          {
            $dates = explode('/', $contentSoftware->INSTALLDATE);
            $dateInstall = $dates[2] . '-' . $dates[1] . '-' . $dates[0];
          }
          $versionIds[$softwareversion->id] = [
            'is_dynamic'   => true,
            'date_install' => $dateInstall,
            // TODO add field in DB + fill with UNINSTALL_STRING
          ];
        }
      }
      $computer->softwareversions()->wherePivot('is_dynamic', true)->sync($versionIds);
    }
  }
}
