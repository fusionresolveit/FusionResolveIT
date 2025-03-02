<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostCluster extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Clustertype */
  public $type;

  /** @var ?string */
  public $uuid;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  /** @var ?string */
  public $version;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Cluster');
    $cluster = new \App\Models\Cluster();
    $this->definitions = $cluster->getDefinitions();

    $this->name = $this->setName($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Clustertype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type =  $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyLocation = new \App\Models\Clustertype();
        $emptyLocation->id = 0;
        $this->type = $emptyLocation;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStrNotempty('uuid')->isValid($data) &&
        isset($data->uuid)
    )
    {
      $this->uuid = $data->uuid;
    }

    $this->comment = $this->setComment($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

    if (
        Validation::attrStrNotempty('version')->isValid($data) &&
        isset($data->version)
    )
    {
      $this->version = $data->version;
    }

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, state?: \App\Models\State, type?: \App\Models\Clustertype, uuid?: string,
   *               comment?: string, usertech?: \App\Models\User, grouptech?: \App\Models\Group, version?: string,
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
   * @param-out array{name?: string, state?: \App\Models\State, type?: \App\Models\Clustertype, uuid?: string,
   *                  comment?: string, usertech?: \App\Models\User, grouptech?: \App\Models\Group, version?: string,
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
