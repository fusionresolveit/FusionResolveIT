<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostDevicepowersupply extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?bool */
  public $is_atx;

  /** @var ?string */
  public $power;

  /** @var ?\App\Models\Devicepowersupplymodel */
  public $model;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Devicepowersupply');
    $devicepowersupply = new \App\Models\Devicepowersupply();
    $this->definitions = $devicepowersupply->getDefinitions();

    $this->name = $this->setName($data);

    $this->manufacturer = $this->setManufacturer($data);

    if (
        Validation::attrStr('is_atx')->isValid($data) &&
        isset($data->is_atx) &&
        $data->is_atx == 'on'
    )
    {
      $this->is_atx = true;
    } else {
      $this->is_atx = false;
    }

    if (
        Validation::attrStrNotempty('power')->isValid($data) &&
        isset($data->power)
    )
    {
      $this->power = $data->power;
    }

    if (
        Validation::attrNumericVal('model')->isValid($data) &&
        isset($data->model)
    )
    {
      $model = \App\Models\Devicepowersupplymodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Devicepowersupplymodel();
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
   * @return array{name?: string, manufacturer?: \App\Models\Manufacturer, is_atx?: bool, power?: string,
   *               model?: \App\Models\Devicepowersupplymodel, comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, manufacturer?: \App\Models\Manufacturer, is_atx?: bool, power?: string,
   *                  model?: \App\Models\Devicepowersupplymodel, comment?: string, is_recursive?: bool} $data
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
