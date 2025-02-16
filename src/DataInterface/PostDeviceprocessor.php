<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostDeviceprocessor extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?int */
  public $frequency_default;

  /** @var ?int */
  public $frequence;

  /** @var ?int */
  public $nbcores_default;

  /** @var ?int */
  public $nbthreads_default;

  /** @var ?\App\Models\Deviceprocessormodel */
  public $model;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?string */
  public $cpuid;

  /** @var ?int */
  public $stepping;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Deviceprocessor');
    $deviceprocessor = new \App\Models\Deviceprocessor();
    $this->definitions = $deviceprocessor->getDefinitions();

    $this->name = $this->setName($data);

    $this->manufacturer = $this->setManufacturer($data);

    if (
        Validation::attrNumericVal('frequency_default')->isValid($data) &&
        isset($data->frequency_default)
    )
    {
      $this->frequency_default = intval($data->frequency_default);
    }

    if (
        Validation::attrNumericVal('frequence')->isValid($data) &&
        isset($data->frequence)
    )
    {
      $this->frequence = intval($data->frequence);
    }

    if (
        Validation::attrNumericVal('nbcores_default')->isValid($data) &&
        isset($data->nbcores_default)
    )
    {
      $this->nbcores_default = intval($data->nbcores_default);
    }

    if (
        Validation::attrNumericVal('nbthreads_default')->isValid($data) &&
        isset($data->nbthreads_default)
    )
    {
      $this->nbthreads_default = intval($data->nbthreads_default);
    }

    if (
        Validation::attrNumericVal('model')->isValid($data) &&
        isset($data->model)
    )
    {
      $model = \App\Models\Deviceprocessormodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Deviceprocessormodel();
        $emptyModel->id = 0;
        $this->model = $emptyModel;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);

    if (
        Validation::attrStrNotempty('cpuid')->isValid($data) &&
        isset($data->cpuid)
    )
    {
      $this->cpuid = $data->cpuid;
    }

    if (
        Validation::attrNumericVal('stepping')->isValid($data) &&
        isset($data->stepping)
    )
    {
      $this->stepping = intval($data->stepping);
    }
  }

  /**
   * @return array{name?: string, manufacturer?: \App\Models\Manufacturer, frequency_default?: int,
   *               frequence?: int, nbcores_default?: int, nbthreads_default?: int,
   *               model?: \App\Models\Deviceprocessormodel, comment?: string, is_recursive?: bool,
   *               cpuid?: string, stepping?: int}
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
   * @param-out array{name?: string, manufacturer?: \App\Models\Manufacturer, frequency_default?: int,
   *                  frequence?: int, nbcores_default?: int, nbthreads_default?: int,
   *                  model?: \App\Models\Deviceprocessormodel, comment?: string, is_recursive?: bool,
   *                  cpuid?: string, stepping?: int} $data
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
