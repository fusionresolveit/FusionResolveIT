<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostEnclosure extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Enclosuremodel */
  public $model;

  /** @var ?string */
  public $serial;

  /** @var ?string */
  public $otherserial;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Enclosure');
    $enclosure = new \App\Models\Enclosure();
    $this->definitions = $enclosure->getDefinitions();

    $this->name = $this->setName($data);

    $this->location = $this->setLocation($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrNumericVal('model')->isValid($data) &&
        isset($data->model)
    )
    {
      $model = \App\Models\Enclosuremodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Enclosuremodel();
        $emptyModel->id = 0;
        $this->model = $emptyModel;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->serial = $this->setSerial($data);

    $this->otherserial = $this->setOtherserial($data);

    $this->manufacturer = $this->setManufacturer($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, location?: \App\Models\Location, state?: \App\Models\State,
   *               model?: \App\Models\Enclosuremodel, serial?: string, otherserial?: string,
   *               manufacturer?: \App\Models\Manufacturer, usertech?: \App\Models\User,
   *               grouptech?: \App\Models\Group, comment?: string}
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
   * @param-out array{name?: string, location?: \App\Models\Location, state?: \App\Models\State,
   *                  model?: \App\Models\Enclosuremodel, serial?: string, otherserial?: string,
   *                  manufacturer?: \App\Models\Manufacturer, usertech?: \App\Models\User,
   *                  grouptech?: \App\Models\Group, comment?: string} $data
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
