<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostSoftwareversion extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Software */
  public $software;

  /** @var ?\App\Models\Operatingsystem */
  public $operatingsystem;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Softwareversion');
    $softwareversion = new \App\Models\Softwareversion();
    $this->definitions = $softwareversion->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('software')->isValid($data) &&
        isset($data->software)
    )
    {
      $software = \App\Models\Software::where('id', $data->software)->first();
      if (!is_null($software))
      {
        $this->software = $software;
      }
      elseif (intval($data->software) == 0)
      {
        $emptySoftware = new \App\Models\Software();
        $emptySoftware->id = 0;
        $this->software = $emptySoftware;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('operatingsystem')->isValid($data) &&
        isset($data->operatingsystem)
    )
    {
      $operatingsystem = \App\Models\Operatingsystem::where('id', $data->operatingsystem)->first();
      if (!is_null($operatingsystem))
      {
        $this->operatingsystem = $operatingsystem;
      }
      elseif (intval($data->operatingsystem) == 0)
      {
        $emptyOS = new \App\Models\Operatingsystem();
        $emptyOS->id = 0;
        $this->operatingsystem = $emptyOS;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->state = $this->setState($data);

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, software?: \App\Models\Software, operatingsystem?: \App\Models\Operatingsystem,
   *               state?: \App\Models\State, comment?: string}
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
   * @param-out array{name?: string, software?: \App\Models\Software, operatingsystem?: \App\Models\Operatingsystem,
   *                  state?: \App\Models\State, comment?: string} $data
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
