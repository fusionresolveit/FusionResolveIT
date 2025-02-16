<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostDevicegraphiccard extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?string */
  public $chipset;

  /** @var ?string */
  public $memory_default;

  /** @var ?\App\Models\Interfacetype */
  public $interface;

  /** @var ?\App\Models\Devicegraphiccardmodel */
  public $model;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Devicegraphiccard');
    $devicegraphiccard = new \App\Models\Devicegraphiccard();
    $this->definitions = $devicegraphiccard->getDefinitions();

    $this->name = $this->setName($data);

    $this->manufacturer = $this->setManufacturer($data);

    if (
        Validation::attrStrNotempty('chipset')->isValid($data) &&
        isset($data->chipset)
    )
    {
      $this->chipset = $data->chipset;
    }

    if (
        Validation::attrStrNotempty('memory_default')->isValid($data) &&
        isset($data->memory_default)
    )
    {
      $this->memory_default = $data->memory_default;
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
      $model = \App\Models\Devicegraphiccardmodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Devicegraphiccardmodel();
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
   * @return array{name?: string, manufacturer?: \App\Models\Manufacturer, chipset?: string,
   *               memory_default?: string, interface?: \App\Models\Interfacetype,
   *               model?: \App\Models\Devicegraphiccardmodel, comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, manufacturer?: \App\Models\Manufacturer, chipset?: string,
   *                  memory_default?: string, interface?: \App\Models\Interfacetype,
   *                  model?: \App\Models\Devicegraphiccardmodel, comment?: string, is_recursive?: bool} $data
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
