<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostComputerantivirus extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?string */
  public $antivirus_version;

  /** @var ?string */
  public $date_expiration;

  /** @var ?string */
  public $signature_version;

  /** @var ?bool */
  public $is_active;

  /** @var ?bool */
  public $is_uptodate;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Computerantivirus');
    $computerantivirus = new \App\Models\Computerantivirus();
    $this->definitions = $computerantivirus->getDefinitions();

    $this->name = $this->setName($data);

    $this->manufacturer = $this->setManufacturer($data);

    if (
        Validation::attrStrNotempty('antivirus_version')->isValid($data) &&
        isset($data->antivirus_version)
    )
    {
      $this->antivirus_version = $data->antivirus_version;
    }

    if (
        Validation::attrDate('date_expiration')->isValid($data) &&
        isset($data->date_expiration)
    )
    {
      $this->date_expiration = $data->date_expiration;
    }

    if (
        Validation::attrStrNotempty('signature_version')->isValid($data) &&
        isset($data->signature_version)
    )
    {
      $this->signature_version = $data->signature_version;
    }

    if (
        Validation::attrStr('is_active')->isValid($data) &&
        isset($data->is_active) &&
        $data->is_active == 'on'
    )
    {
      $this->is_active = true;
    } else {
      $this->is_active = false;
    }

    if (
        Validation::attrStr('is_uptodate')->isValid($data) &&
        isset($data->is_uptodate) &&
        $data->is_uptodate == 'on'
    )
    {
      $this->is_uptodate = true;
    } else {
      $this->is_uptodate = false;
    }
  }

  /**
   * @return array{name?: string, manufacturer?: \App\Models\Manufacturer, antivirus_version?: string,
   *               date_expiration?: string, signature_version?: string, is_active?: bool, is_uptodate?: bool}
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
   * @param-out array{name?: string, manufacturer?: \App\Models\Manufacturer, antivirus_version?: string,
   *                  date_expiration?: string, signature_version?: string, is_active?: bool, is_uptodate?: bool} $data
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
