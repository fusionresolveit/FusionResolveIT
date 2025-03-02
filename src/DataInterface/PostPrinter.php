<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostPrinter extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Printertype */
  public $type;

  /** @var ?\App\Models\Printermodel */
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

  /** @var ?bool */
  public $have_serial;

  /** @var ?bool */
  public $have_parallel;

  /** @var ?bool */
  public $have_usb;

  /** @var ?bool */
  public $have_ethernet;

  /** @var ?bool */
  public $have_wifi;

  /** @var ?int */
  public $memory_size;

  /** @var ?int */
  public $init_pages_counter;

  /** @var ?int */
  public $last_pages_counter;

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
    $this->loadRights('App\Models\Printer');
    $printer = new \App\Models\Printer();
    $this->definitions = $printer->getDefinitions();

    $this->name = $this->setName($data);

    $this->location = $this->setLocation($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Printertype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Printertype();
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
      $model = \App\Models\Printermodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Printermodel();
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
        Validation::attrStr('have_serial')->isValid($data) &&
        isset($data->have_serial) &&
        $data->have_serial == 'on'
    )
    {
      $this->have_serial = true;
    } else {
      $this->have_serial = false;
    }

    if (
        Validation::attrStr('have_parallel')->isValid($data) &&
        isset($data->have_parallel) &&
        $data->have_parallel == 'on'
    )
    {
      $this->have_parallel = true;
    } else {
      $this->have_parallel = false;
    }

    if (
        Validation::attrStr('have_usb')->isValid($data) &&
        isset($data->have_usb) &&
        $data->have_usb == 'on'
    )
    {
      $this->have_usb = true;
    } else {
      $this->have_usb = false;
    }

    if (
        Validation::attrStr('have_ethernet')->isValid($data) &&
        isset($data->have_ethernet) &&
        $data->have_ethernet == 'on'
    )
    {
      $this->have_ethernet = true;
    } else {
      $this->have_ethernet = false;
    }

    if (
        Validation::attrStr('have_wifi')->isValid($data) &&
        isset($data->have_wifi) &&
        $data->have_wifi == 'on'
    )
    {
      $this->have_wifi = true;
    } else {
      $this->have_wifi = false;
    }

    if (
        Validation::attrNumericVal('memory_size')->isValid($data) &&
        isset($data->memory_size)
    )
    {
      $this->memory_size = intval($data->memory_size);
    }

    if (
        Validation::attrNumericVal('init_pages_counter')->isValid($data) &&
        isset($data->init_pages_counter)
    )
    {
      $this->init_pages_counter = intval($data->init_pages_counter);
    }

    if (
        Validation::attrNumericVal('last_pages_counter')->isValid($data) &&
        isset($data->last_pages_counter)
    )
    {
      $this->last_pages_counter = intval($data->last_pages_counter);
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
   *               type?: \App\Models\Printertype, model?: \App\Models\Printermodel,
   *               serial?: string, otherserial?: string, contact?: string, contact_num?: string,
   *               user?: \App\Models\User, group?: \App\Models\Group, comment?: string,
   *               have_serial?: bool, have_parallel?: bool, have_usb?: bool, have_ethernet?: bool,
   *               have_wifi?: bool, memory_size?: int, init_pages_counter?: int, last_pages_counter?: int,
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
   *                  type?: \App\Models\Printertype, model?: \App\Models\Printermodel,
   *                  serial?: string, otherserial?: string, contact?: string, contact_num?: string,
   *                  user?: \App\Models\User, group?: \App\Models\Group, comment?: string,
   *                  have_serial?: bool, have_parallel?: bool, have_usb?: bool, have_ethernet?: bool,
   *                  have_wifi?: bool, memory_size?: int, init_pages_counter?: int, last_pages_counter?: int,
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
