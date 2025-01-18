<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

final class Computeroperatingsystem extends \App\v1\Controllers\Common
{
  public static function parse(object $dataObj, \App\Models\Computer $computer)
  {
    $vs = [
      'NAME'            => Validation::attrStrNotempty('NAME'),
      'HOSTID'          => Validation::attrStrNotempty('HOSTID'),
      'OSCOMMENTS'      => Validation::attrStrNotempty('OSCOMMENTS'),
      'WINOWNER'        => Validation::attrStrNotempty('WINOWNER'),
      'WINCOMPANY'      => Validation::attrStrNotempty('WINCOMPANY'),
      'WINPRODKEY'      => Validation::attrStrNotempty('WINPRODKEY'),
      'WINPRODID'       => Validation::attrStrNotempty('WINPRODID'),
      'INSTALL_DATE'    => Validation::attrDateTime('INSTALL_DATE'),
      'SERVICE_PACK'    => Validation::attrStrNotempty('SERVICE_PACK'),
      'ARCH'            => Validation::attrStrNotempty('ARCH'),
      'KERNEL_NAME'     => Validation::attrStrNotempty('KERNEL_NAME'),
      'KERNEL_VERSION'  => Validation::attrStrNotempty('KERNEL_VERSION'),
    ];

    $versionIds = [];
    if (property_exists($dataObj->CONTENT, 'OPERATINGSYSTEM'))
    {
      $contentOS = $dataObj->CONTENT->OPERATINGSYSTEM;
      if ($vs['NAME']->isValid($contentOS))
      {
        $osName = Common::cleanString($contentOS->NAME);

        $operatingsystem = \App\Models\Operatingsystem::withoutEagerLoads()->firstOrCreate(
          [
            'name' => $osName,
          ],
        );
        $hostid = null;
        if ($vs['HOSTID']->isValid($contentOS))
        {
          $hostid = Common::cleanString($contentOS->HOSTID);
        }

        $oscomment = null;
        $winowner = null;
        $wincompany = null;
        $license_number = null;
        $licenseid = null;
        if (property_exists($dataObj->CONTENT, 'HARDWARE'))
        {
          $contentH = $dataObj->CONTENT->HARDWARE;
          if ($vs['OSCOMMENTS']->isValid($contentH))
          {
            $oscomment = Common::cleanString($contentH->OSCOMMENTS);
          }
          if ($vs['WINOWNER']->isValid($contentH))
          {
            $winowner = Common::cleanString($contentH->WINOWNER);
          }
          if ($vs['WINCOMPANY']->isValid($contentH))
          {
            $wincompany = Common::cleanString($contentH->WINCOMPANY);
          }
          if ($vs['WINPRODKEY']->isValid($contentH))
          {
            $license_number = Common::cleanString($contentH->WINPRODKEY);
          }
          if ($vs['WINPRODID']->isValid($contentH))
          {
            $licenseid = Common::cleanString($contentH->WINPRODID);
          }
        }

        $installationdate = null;
        if ($vs['INSTALL_DATE']->isValid($contentOS))
        {
          $installationdate = $contentOS->INSTALL_DATE;
        }

        $operatingsystemservicepack_id = 0;
        if ($vs['SERVICE_PACK']->isValid($contentOS))
        {
          $operatingsystemsp = \App\Models\Operatingsystemservicepack::withoutEagerLoads()->firstOrCreate(
            [
              'name' => Common::cleanString($contentOS->SERVICE_PACK),
            ],
          );
          $operatingsystemservicepack_id = $operatingsystemsp->id;
        }
        $archId = 0;
        if ($vs['ARCH']->isValid($contentOS))
        {
          $operatingsystemarch = \App\Models\Operatingsystemarchitecture::withoutEagerLoads()->firstOrCreate(
            [
              'name' => Common::cleanString($contentOS->ARCH),
            ],
          );
          $archId = $operatingsystemarch->id;
        }

        // manage kernel
        $kernelVersionId = 0;
        if ($vs['KERNEL_NAME']->isValid($contentOS) && $vs['KERNEL_VERSION']->isValid($contentOS))
        {
          $operatingsystemkernel = \App\Models\Operatingsystemkernel::withoutEagerLoads()->firstOrCreate(
            [
              'name' => Common::cleanString($contentOS->KERNEL_NAME),
            ],
          );

          $operatingsystemkernelv = \App\Models\Operatingsystemkernelversion::withoutEagerLoads()->firstOrCreate(
            [
              'name'                     => Common::cleanString($contentOS->KERNEL_VERSION),
              'operatingsystemkernel_id' => $operatingsystemkernel->id
            ],
          );
          $kernelVersionId = $operatingsystemkernelv->id;
        }

        $versionIds[$operatingsystem->id] = [
          'hostid'                          => $hostid,
          'oscomment'                       => $oscomment,
          'winowner'                        => $winowner,
          'wincompany'                      => $wincompany,
          'operatingsystemversion_id'       => self::getVersion($contentOS),
          'license_number'                  => $license_number,
          'installationdate'                => $installationdate,
          'licenseid'                       => $licenseid,
          'operatingsystemedition_id'       => self::getEdition($contentOS),
          'operatingsystemservicepack_id'   => $operatingsystemservicepack_id,
          'operatingsystemarchitecture_id'  => $archId,
          'operatingsystemkernelversion_id' => $kernelVersionId,
          //                      is_dynamic: 0
        ];
      }
    }
    $computer->operatingsystems()->sync($versionIds);
  }

  public static function getVersion(object $dataObj)
  {
    $version = null;
    $vs = [
      'NAME'      => Validation::attrStrNotempty('NAME'),
      'FULL_NAME' => Validation::attrStrNotempty('FULL_NAME'),
      'VERSION'   => Validation::attrStrNotempty('VERSION'),
    ];
    if ($vs['VERSION']->isValid($dataObj))
    {
      $version = Common::cleanString($dataObj->VERSION);
    }
    if ($vs['NAME']->isValid($dataObj) && $dataObj->NAME == 'Windows')
    {
      // we do this for Windows because we have a version like 3109 instead 10
      $version = null;
    }

    // clean version when have something after
    if (!is_null($version))
    {
      preg_match("/^([\d\.]+)/", $version, $matches);
      if (count($matches) == 2) {
        $version = $matches[1];
      }
    }

    if ($vs['FULL_NAME']->isValid($dataObj))
    {
      $full_name = Common::cleanString($dataObj->FULL_NAME);
      $matches = [];
      if (is_null($version))
      {
        preg_match("/\w[\s\S]{0,4} (?:Windows[\s\S]{0,4} |)(?:.*) (\d{4} R2|[\d\.]+|Vista|XP)/", $full_name, $matches);
        if (count($matches) == 2) {
          $version = $matches[1];
        }
      }
      if (is_null($version))
      {
        preg_match("/^(.*) GNU\/Linux (\d{1,2}|\d{1,2}\.\d{1,2}) \((.*)\)$/", $full_name, $matches);
        if (count($matches) == 4) {
          $version = $matches[2] . " (" . $matches[3] . ")";
        }
      }
      if (is_null($version))
      {
        preg_match("/Linux (.*) (\d{1,2}|\d{1,2}\.\d{1,2}) \((.*)\)$/", $full_name, $matches);
        if (count($matches) == 4) {
          $version = $matches[2];
        }
      }
      if (is_null($version))
      {
        preg_match("/^(?:\w+) ([\d\.]+)/", $full_name, $matches);
        if (count($matches) == 2) {
          $version = $matches[1];
        }
      }
      if (is_null($version))
      {
        preg_match("/^([\d\.]+)/", $full_name, $matches);
        if (count($matches) == 2) {
          $version = $matches[1];
        }
      }
    }

    if (is_null($version))
    {
      return 0;
    }
    $operatingsystemversion = \App\Models\Operatingsystemversion::withoutEagerLoads()->firstOrCreate(
      [
        'name'   => $version,
        'is_lts' => self::getLts($dataObj),
      ],
    );
    return $operatingsystemversion->id;
  }

  public static function getEdition(object $dataObj)
  {
    $edition = null;
    $vs = [
      'FULL_NAME' => Validation::attrStrNotempty('FULL_NAME'),
      'VERSION'   => Validation::attrStrNotempty('VERSION'),
    ];

    if ($vs['FULL_NAME']->isValid($dataObj))
    {
      $fullName = Common::cleanString($dataObj->FULL_NAME);
      $matches = [];

      if ($fullName == 'Microsoft Windows Embedded Standard')
      {
        $edition = 'Embedded Standard';
      }

      if (is_null($edition))
      {
        preg_match("/.+ Windows (XP |\d\.\d |\d{1,4} |Vista(™)? )(.*)/", $fullName, $matches);
        if (count($matches) == 4) {
          $edition = $matches[3];
        }
        elseif (count($matches) == 2)
        {
          $edition = $matches[1];
        }
      }

      if (is_null($edition))
      {
        preg_match("/Linux (.*) (\d{1,2}|\d{1,2}\.\d{1,2}) \((.*)\)$/", $fullName, $matches);
        if (count($matches) == 4) {
          $edition = $matches[1];
        }
      }

      if (is_null($edition))
      {
        preg_match(
          "/\w[\s\S]{0,4} (?:Windows[\s\S]{0,4} |)(.*) (\d{4} R2|\d{4})(?:, | |)(.*|)(?: x64|)$/",
          $fullName,
          $matches
        );
        if (count($matches) == 4) {
          $edition = trim($matches[1] . " " . $matches[3], ' x64');
        }
      }

      if (is_null($edition))
      {
        preg_match("/\((\w+) Edition\)/", $fullName, $matches);
        if (count($matches) == 2) {
          $edition = $matches[1];
        }
      }
    }
    if (is_null($edition))
    {
      return 0;
    }
    $operatingsystemedition = \App\Models\Operatingsystemedition::withoutEagerLoads()->firstOrCreate(
      [
        'name' => $edition,
      ],
    );
    return $operatingsystemedition->id;
  }

  public static function getLts(object $dataObj)
  {
    if (Validation::attrStrNotempty('FULL_NAME')->isValid($dataObj))
    {
      $fullName = Common::cleanString($dataObj->FULL_NAME);
      preg_match("/(LTS)/", $fullName, $matches);
      if (count($matches) == 2) {
        return true;
      }
    }
    return false;
  }
}
