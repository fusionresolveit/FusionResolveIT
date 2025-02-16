<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostComputer extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Entity */
  public $entity;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Computertype */
  public $type;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?\App\Models\Computermodel */
  public $model;

  /** @var ?string */
  public $serial;

  /** @var ?string */
  public $otherserial;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  /** @var ?string */
  public $contact;

  /** @var ?string */
  public $contact_num;

  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?\App\Models\Network */
  public $network;

  /** @var ?string */
  public $uuid;

  /** @var ?\App\Models\Autoupdatesystem */
  public $autoupdatesystem;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Computer');
    $computer = new \App\Models\Computer();
    $this->definitions = $computer->getDefinitions();

    $this->name = $this->setName($data);

    $this->entity = $this->setEntity($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Computertype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Computertype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->user = $this->setUser($data);

    $this->manufacturer = $this->setManufacturer($data);

    if (
        Validation::attrNumericVal('model')->isValid($data) &&
        isset($data->model)
    )
    {
      $model = \App\Models\Computermodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Computermodel();
        $emptyModel->id = 0;
        $this->model = $emptyModel;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->serial = $this->setSerial($data);

    $this->otherserial = $this->setOtherserial($data);

    $this->location = $this->setLocation($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

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

    $this->group = $this->setGroup($data);

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

    if (
        Validation::attrStr('uuid')->isValid($data) &&
        isset($data->uuid)
    )
    {
      $this->uuid = $data->uuid;
    }

    if (
        Validation::attrNumericVal('autoupdatesystem')->isValid($data) &&
        isset($data->autoupdatesystem)
    )
    {
      $autoupdatesystem = \App\Models\Autoupdatesystem::where('id', $data->autoupdatesystem)->first();
      if (!is_null($autoupdatesystem))
      {
        $this->autoupdatesystem = $autoupdatesystem;
      }
      elseif (intval($data->autoupdatesystem) == 0)
      {
        $emptyAutoupdatesystem = new \App\Models\Autoupdatesystem();
        $emptyAutoupdatesystem->id = 0;
        $this->autoupdatesystem = $emptyAutoupdatesystem;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, entity?: \App\Models\Entity, state?: \App\Models\State,
   *               type?: \App\Models\Computertype, user?: \App\Models\User, manufacturer?: \App\Models\Manufacturer,
   *               model?: \App\Models\Computermodel, serial?: string, otherserial?: string,
   *               location?: \App\Models\Location, usertech?: \App\Models\User, grouptech?: \App\Models\Group,
   *               contact?: string, contact_num?: string, group?: \App\Models\Group, network?: \App\Models\Network,
   *               uuid?: string, autoupdatesystem?: \App\Models\Autoupdatesystem, comment?: string}
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
   * @param-out array{name?: string, entity?: \App\Models\Entity, state?: \App\Models\State,
   *                  type?: \App\Models\Computertype, user?: \App\Models\User, manufacturer?: \App\Models\Manufacturer,
   *                  model?: \App\Models\Computermodel, serial?: string, otherserial?: string,
   *                  location?: \App\Models\Location, usertech?: \App\Models\User, grouptech?: \App\Models\Group,
   *                  contact?: string, contact_num?: string, group?: \App\Models\Group, network?: \App\Models\Network,
   *                  uuid?: string, autoupdatesystem?: \App\Models\Autoupdatesystem, comment?: string} $data
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
