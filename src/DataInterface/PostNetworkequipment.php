<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostNetworkequipment extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Networkequipmenttype */
  public $type;

  /** @var ?\App\Models\Networkequipmentmodel */
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
  public $ram;

  /** @var ?\App\Models\Network */
  public $network;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Networkequipment');
    $networkequipment = new \App\Models\Networkequipment();
    $this->definitions = $networkequipment->getDefinitions();

    $this->name = $this->setName($data);

    $this->location = $this->setLocation($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Networkequipmenttype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Networkequipmenttype();
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
      $model = \App\Models\Networkequipmentmodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Networkequipmentmodel();
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
        Validation::attrStrNotempty('ram')->isValid($data) &&
        isset($data->ram)
    )
    {
      $this->ram = $data->ram;
    }

    if (
        Validation::attrNumericVal('network')->isValid($data) &&
        isset($data->network)
    )
    {
      $network = \App\Models\Network::where('id', $data->network)->first();
      if (!is_null($network))
      {
        $this->network = $network;
      }
      elseif (intval($data->network) == 0)
      {
        $emptyNetwork = new \App\Models\Network();
        $emptyNetwork->id = 0;
        $this->network = $emptyNetwork;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->manufacturer = $this->setManufacturer($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);
  }

  /**
   * @return array{name?: string, location?: \App\Models\Location, state?: \App\Models\State,
   *               type?: \App\Models\Networkequipmenttype, model?: \App\Models\Networkequipmentmodel,
   *               serial?: string, otherserial?: string, contact?: string, contact_num?: string,
   *               user?: \App\Models\User, group?: \App\Models\Group, comment?: string, ram?: string,
   *               network?: \App\Models\Network, manufacturer?: \App\Models\Manufacturer,
   *               usertech?: \App\Models\User, grouptech?: \App\Models\Group}
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
   *                  type?: \App\Models\Networkequipmenttype, model?: \App\Models\Networkequipmentmodel,
   *                  serial?: string, otherserial?: string, contact?: string, contact_num?: string,
   *                  user?: \App\Models\User, group?: \App\Models\Group, comment?: string, ram?: string,
   *                  network?: \App\Models\Network, manufacturer?: \App\Models\Manufacturer,
   *                  usertech?: \App\Models\User, grouptech?: \App\Models\Group} $data
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
