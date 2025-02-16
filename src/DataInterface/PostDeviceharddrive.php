<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostDeviceharddrive extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?string */
  public $capacity_default;

  /** @var ?string */
  public $rpm;

  /** @var ?string */
  public $cache;

  /** @var ?\App\Models\Deviceharddrivemodel */
  public $model;

  /** @var ?\App\Models\Interfacetype */
  public $interface;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Deviceharddrive');
    $deviceharddrive = new \App\Models\Deviceharddrive();
    $this->definitions = $deviceharddrive->getDefinitions();

    $this->name = $this->setName($data);

    $this->manufacturer = $this->setManufacturer($data);

    if (
        Validation::attrStrNotempty('capacity_default')->isValid($data) &&
        isset($data->capacity_default)
    )
    {
      $this->capacity_default = $data->capacity_default;
    }

    if (
        Validation::attrStrNotempty('rpm')->isValid($data) &&
        isset($data->rpm)
    )
    {
      $this->rpm = $data->rpm;
    }

    if (
        Validation::attrStrNotempty('cache')->isValid($data) &&
        isset($data->cache)
    )
    {
      $this->cache = $data->cache;
    }

    if (
        Validation::attrNumericVal('model')->isValid($data) &&
        isset($data->model)
    )
    {
      $model = \App\Models\Deviceharddrivemodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Deviceharddrivemodel();
        $emptyModel->id = 0;
        $this->model = $emptyModel;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('interface')->isValid($data) &&
        isset($data->interface)
    )
    {
      $interface = \App\Models\Interfacetype::where('id', $data->interface)->first();
      if (!is_null($interface))
      {
        $this->interface = $interface;
      }
      elseif (intval($data->interface) == 0)
      {
        $emptyInterface = new \App\Models\Interfacetype();
        $emptyInterface->id = 0;
        $this->interface = $emptyInterface;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, manufacturer?: \App\Models\Manufacturer, capacity_default?: string,
   *               rpm?: string, cache?: string, model?: \App\Models\Deviceharddrivemodel,
   *               interface?: \App\Models\Interfacetype, comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, manufacturer?: \App\Models\Manufacturer, capacity_default?: string,
   *                  rpm?: string, cache?: string, model?: \App\Models\Deviceharddrivemodel,
   *                  interface?: \App\Models\Interfacetype, comment?: string, is_recursive?: bool} $data
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
