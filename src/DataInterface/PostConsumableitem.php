<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostConsumableitem extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $ref;

  /** @var ?string */
  public $otherserial;

  /** @var ?\App\Models\Consumableitemtype */
  public $type;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  /** @var ?int */
  public $alarm_threshold;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Consumableitem');
    $consumableitem = new \App\Models\Consumableitem();
    $this->definitions = $consumableitem->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('ref')->isValid($data) &&
        isset($data->ref)
    )
    {
      $this->ref = $data->ref;
    }

    $this->otherserial = $this->setOtherserial($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Consumableitemtype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Consumableitemtype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->manufacturer = $this->setManufacturer($data);

    $this->location = $this->setLocation($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

    if (
        Validation::attrNumericVal('alarm_threshold')->isValid($data) &&
        isset($data->alarm_threshold)
    )
    {
      $this->alarm_threshold = intval($this->alarm_threshold);
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, ref?: string, otherserial?: string, type?: \App\Models\Consumableitemtype,
   *               manufacturer?: \App\Models\Manufacturer, location?: \App\Models\Location,
   *               usertech?: \App\Models\User, grouptech?: \App\Models\Group, alarm_threshold?: string,
   *               comment?: string}
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
   * @param-out array{name?: string, ref?: string, otherserial?: string, type?: \App\Models\Consumableitemtype,
   *                  manufacturer?: \App\Models\Manufacturer, location?: \App\Models\Location,
   *                  usertech?: \App\Models\User, grouptech?: \App\Models\Group, alarm_threshold?: string,
   *                  comment?: string} $data
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
