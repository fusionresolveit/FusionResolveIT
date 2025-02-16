<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostDevicedrive extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?bool */
  public $is_writer;

  /** @var ?string */
  public $speed;

  /** @var ?\App\Models\Interfacetype */
  public $interface;

  /** @var ?\App\Models\Devicedrivemodel */
  public $model;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Devicedrive');
    $devicedrive = new \App\Models\Devicedrive();
    $this->definitions = $devicedrive->getDefinitions();

    $this->name = $this->setName($data);

    $this->manufacturer = $this->setManufacturer($data);

    if (
        Validation::attrStr('is_writer')->isValid($data) &&
        isset($data->is_writer) &&
        $data->is_writer == 'on'
    )
    {
      $this->is_writer = true;
    } else {
      $this->is_writer = false;
    }

    if (
        Validation::attrStrNotempty('speed')->isValid($data) &&
        isset($data->speed)
    )
    {
      $this->speed = $data->speed;
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

    if (
        Validation::attrNumericVal('model')->isValid($data) &&
        isset($data->model)
    )
    {
      $model = \App\Models\Devicedrivemodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Devicedrivemodel();
        $emptyModel->id = 0;
        $this->model = $emptyModel;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, manufacturer?: \App\Models\Manufacturer, is_writer?: bool, speed?: string,
   *               interface?: \App\Models\Interfacetype, model?: \App\Models\Devicedrivemodel, comment?: string,
   *               is_recursive?: bool}
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
   * @param-out array{name?: string, manufacturer?: \App\Models\Manufacturer, is_writer?: bool, speed?: string,
   *                  interface?: \App\Models\Interfacetype, model?: \App\Models\Devicedrivemodel, comment?: string,
   *                  is_recursive?: bool} $data
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
