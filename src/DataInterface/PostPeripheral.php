<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostPeripheral extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Peripheraltype */
  public $type;

  /** @var ?\App\Models\Peripheralmodel */
  public $model;

  /** @var ?string */
  public $serial;

  /** @var ?string */
  public $otherserial;

  /** @var ?string */
  public $contact;

  /** @var ?string */
  public $contact_num;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?string */
  public $comment;

  /** @var ?string */
  public $brand;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Peripheral');
    $peripheral = new \App\Models\Peripheral();
    $this->definitions = $peripheral->getDefinitions();

    $this->name = $this->setName($data);

    $this->location = $this->setLocation($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Peripheraltype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Peripheraltype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('model')->isValid($data) &&
        isset($data->model)
    )
    {
      $model = \App\Models\Peripheralmodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Peripheralmodel();
        $emptyModel->id = 0;
        $this->model = $emptyModel;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->serial = $this->setSerial($data);

    $this->otherserial = $this->setOtherserial($data);

    if (
        Validation::attrStr('contact')->isValid($data) &&
        isset($data->contact)
    )
    {
      $this->contact = $data->contact;
    }

    if (
        Validation::attrStr('contact_num')->isValid($data) &&
        isset($data->contact_num)
    )
    {
      $this->contact_num = $data->contact_num;
    }

    $this->user = $this->setUser($data);

    $this->group = $this->setGroup($data);

    $this->comment = $this->setComment($data);

    if (
        Validation::attrStr('brand')->isValid($data) &&
        isset($data->brand)
    )
    {
      $this->brand = $data->brand;
    }

    $this->manufacturer = $this->setManufacturer($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);
  }

  /**
   * @return array{name?: string, location?: \App\Models\Location, state?: \App\Models\State,
   *               type?: \App\Models\Peripheraltype, model?: \App\Models\Peripheralmodel,
   *               serial?: string, otherserial?: string, contact?: string, contact_num?: string,
   *               user?: \App\Models\User, group?: \App\Models\Group, comment?: string, brand?: string,
   *               manufacturer?: \App\Models\Manufacturer, usertech?: \App\Models\User,
   *               grouptech?: \App\Models\Group}
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
   *                  type?: \App\Models\Peripheraltype, model?: \App\Models\Peripheralmodel,
   *                  serial?: string, otherserial?: string, contact?: string, contact_num?: string,
   *                  user?: \App\Models\User, group?: \App\Models\Group, comment?: string, brand?: string,
   *                  manufacturer?: \App\Models\Manufacturer, usertech?: \App\Models\User,
   *                  grouptech?: \App\Models\Group} $data
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
