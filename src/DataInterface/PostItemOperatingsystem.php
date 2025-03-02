<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostItemOperatingsystem extends Post
{
  /** @var ?\App\Models\Operatingsystem */
  public $id;

  /** @var ?\App\Models\Operatingsystemarchitecture */
  public $architecture;

  /** @var ?\App\Models\Operatingsystemkernelversion */
  public $kernelversion;

  /** @var ?\App\Models\Operatingsystemversion */
  public $version;

  /** @var ?\App\Models\Operatingsystemservicepack */
  public $servicepack;

  /** @var ?\App\Models\Operatingsystemedition */
  public $edition;

  /** @var ?string */
  public $licenseid;

  /** @var ?string */
  public $hostid;

  /** @var ?string */
  public $oscomment;

  /** @var ?string */
  public $winowner;

  /** @var ?string */
  public $wincompany;

  /** @var ?string */
  public $license_number;

  /** @var ?string */
  public $installationdate;

  /**
   * @param (\App\Models\Computer|
   *         \App\Models\Monitor|
   *         \App\Models\Peripheral|
   *         \App\Models\Printer|
   *         \App\Models\Phone|
   *         \App\Models\Networkequipment) $model
   */
  public function __construct(object $data, $model)
  {
    $this->loadRights(get_class($model));
    if (!method_exists($model, 'getDefinitionOperatingSystem'))
    {
      throw new \Exception('Error', 500);
    }
    $this->definitions = $model->getDefinitionOperatingSystem();

    if (
        Validation::attrNumericVal('id')->isValid($data) &&
        isset($data->id)
    )
    {
      $id = \App\Models\Operatingsystem::where('id', $data->id)->first();
      if (!is_null($id))
      {
        $this->id = $id;
      }
      elseif (intval($data->id) == 0)
      {
        $emptyOperatingsystem = new \App\Models\Operatingsystem();
        $emptyOperatingsystem->id = 0;
        $this->id = $emptyOperatingsystem;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('architecture')->isValid($data) &&
        isset($data->architecture)
    )
    {
      $architecture = \App\Models\Operatingsystemarchitecture::where('id', $data->architecture)->first();
      if (!is_null($architecture))
      {
        $this->architecture = $architecture;
      }
      elseif (intval($data->architecture) == 0)
      {
        $emptyOsarchi = new \App\Models\Operatingsystemarchitecture();
        $emptyOsarchi->id = 0;
        $this->architecture = $emptyOsarchi;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('kernelversion')->isValid($data) &&
        isset($data->kernelversion)
    )
    {
      $kernelversion = \App\Models\Operatingsystemkernelversion::where('id', $data->kernelversion)->first();
      if (!is_null($kernelversion))
      {
        $this->kernelversion = $kernelversion;
      }
      elseif (intval($data->kernelversion) == 0)
      {
        $emptyKernelversion = new \App\Models\Operatingsystemkernelversion();
        $emptyKernelversion->id = 0;
        $this->kernelversion = $emptyKernelversion;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('version')->isValid($data) &&
        isset($data->version)
    )
    {
      $version = \App\Models\Operatingsystemversion::where('id', $data->version)->first();
      if (!is_null($version))
      {
        $this->version = $version;
      }
      elseif (intval($data->version) == 0)
      {
        $emptyVersion = new \App\Models\Operatingsystemversion();
        $emptyVersion->id = 0;
        $this->version = $emptyVersion;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('servicepack')->isValid($data) &&
        isset($data->servicepack)
    )
    {
      $servicepack = \App\Models\Operatingsystemservicepack::where('id', $data->servicepack)->first();
      if (!is_null($servicepack))
      {
        $this->servicepack = $servicepack;
      }
      elseif (intval($data->servicepack) == 0)
      {
        $emptyServicepack = new \App\Models\Operatingsystemservicepack();
        $emptyServicepack->id = 0;
        $this->servicepack = $emptyServicepack;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('edition')->isValid($data) &&
        isset($data->edition)
    )
    {
      $edition = \App\Models\Operatingsystemedition::where('id', $data->edition)->first();
      if (!is_null($edition))
      {
        $this->edition = $edition;
      }
      elseif (intval($data->edition) == 0)
      {
        $emptyEdition = new \App\Models\Operatingsystemedition();
        $emptyEdition->id = 0;
        $this->edition = $emptyEdition;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('licenseid')->isValid($data) &&
        isset($data->licenseid)
    )
    {
      $this->licenseid = $data->licenseid;
    }

    if (
        Validation::attrStr('hostid')->isValid($data) &&
        isset($data->hostid)
    )
    {
      $this->hostid = $data->hostid;
    }

    if (
        Validation::attrStr('oscomment')->isValid($data) &&
        isset($data->oscomment)
    )
    {
      $this->oscomment = $data->oscomment;
    }

    if (
        Validation::attrStr('winowner')->isValid($data) &&
        isset($data->winowner)
    )
    {
      $this->winowner = $data->winowner;
    }

    if (
        Validation::attrStr('wincompany')->isValid($data) &&
        isset($data->wincompany)
    )
    {
      $this->wincompany = $data->wincompany;
    }

    if (
        Validation::attrStr('license_number')->isValid($data) &&
        isset($data->license_number)
    )
    {
      $this->license_number = $data->license_number;
    }

    if (
        Validation::attrDate('installationdate')->isValid($data) &&
        isset($data->installationdate)
    )
    {
      $this->installationdate = $data->installationdate;
    }
  }

  /**
   * @return array{operatingsystem_id?: int,
   *               operatingsystemarchitecture_id?: int,
   *               operatingsystemkernelversion_id?: int,
   *               operatingsystemversion_id?: int,
   *               operatingsystemservicepack_id?: int,
   *               operatingsystemedition_id?: int, licenseid?: string,
   *               hostid?: string, oscomment?: string, winowner?: string, wincompany?: string,
   *               license_number?: string, installationdate?: string}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    $vars = get_object_vars($this);
    $data = [];
    foreach (array_keys($vars) as $key)
    {
      if (!is_null($this->{$key}))
      {
        if (!$filterRights)
        {
          $this->getFieldForArray($key, $data);
        } else {
          // TODO filter by custom
          if (is_null($this->profileright))
          {
            return [];
          }
          elseif (count($this->profilerightcustoms) > 0)
          {
            foreach ($this->profilerightcustoms as $custom)
            {
              if ($custom->write)
              {
                $this->getFieldForArray($key, $data);
              }
            }
          } else {
            $this->getFieldForArray($key, $data);
          }
        }
      }
    }
    return $data;
  }

  /**
   * @param-out array{operatingsystem_id?: int,
   *                  operatingsystemarchitecture_id?: int,
   *                  operatingsystemkernelversion_id?: int,
   *                  operatingsystemversion_id?: int,
   *                  operatingsystemservicepack_id?: int,
   *                  operatingsystemedition_id?: int, licenseid?: string,
   *                  hostid?: string, oscomment?: string, winowner?: string, wincompany?: string,
   *                  license_number?: string, installationdate?: string} $data
   */
  private function getFieldForArray(string $key, mixed &$data): void
  {
    foreach ($this->definitions as $def)
    {
      if ($def->name == $key)
      {
        if (!is_null($def->dbname))
        {
          $data[$def->dbname] = $this->{$key}->id;
          return;
        }
        if ($def->multiple === true)
        {
          $data[$key] = [];
          foreach ($this->{$key} as $item)
          {
            $data[$key][] = $item->id;
          }
          return;
        }
        $data[$key] = $this->{$key};
        return;
      }
    }
  }
}
