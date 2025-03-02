<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostAppliance extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?\App\Models\Appliancetype */
  public $type;

  /** @var ?\App\Models\Applianceenvironment */
  public $environment;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?string */
  public $serial;

  /** @var ?string */
  public $otherserial;

  /** @var ?bool */
  public $is_helpdesk_visible;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Appliance');
    $appliance = new \App\Models\Appliance();
    $this->definitions = $appliance->getDefinitions();

    $this->name = $this->setName($data);

    $this->state = $this->setState($data);

    $this->location = $this->setLocation($data);

    $this->manufacturer = $this->setManufacturer($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Appliancetype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Appliancetype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('environment')->isValid($data) &&
        isset($data->environment)
    )
    {
      $environment = \App\Models\Applianceenvironment::where('id', $data->environment)->first();
      if (!is_null($environment))
      {
        $this->environment = $environment;
      }
      elseif (intval($data->environment) == 0)
      {
        $emptyEnvironment = new \App\Models\Applianceenvironment();
        $emptyEnvironment->id = 0;
        $this->environment = $emptyEnvironment;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

    $this->user = $this->setUser($data);

    $this->serial = $this->setSerial($data);

    $this->otherserial = $this->setOtherserial($data);

    if (
        Validation::attrStr('is_helpdesk_visible')->isValid($data) &&
        isset($data->is_helpdesk_visible) &&
        $data->is_helpdesk_visible == 'on'
    )
    {
      $this->is_helpdesk_visible = true;
    } else {
      $this->is_helpdesk_visible = false;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, state?: \App\Models\State, location?: \App\Models\Location,
   *               manufacturer?: \App\Models\Manufacturer, type?: \App\Models\Appliancetype,
   *               environment?: \App\Models\Applianceenvironment, usertech?: \App\Models\User,
   *               grouptech?: \App\Models\Group, user?:\App\Models\User, group?: \App\Models\Group,
   *               serial?: string, otherserial?: string, is_helpdesk_visible?: bool, comment?: string,
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
   * @param-out array{name?: string, state?: \App\Models\State, location?: \App\Models\Location,
   *                  manufacturer?: \App\Models\Manufacturer, type?: \App\Models\Appliancetype,
   *                  environment?: \App\Models\Applianceenvironment, usertech?: \App\Models\User,
   *                  grouptech?: \App\Models\Group, user?:\App\Models\User, group?: \App\Models\Group,
   *                  serial?: string, otherserial?: string, is_helpdesk_visible?: bool, comment?: string,
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
