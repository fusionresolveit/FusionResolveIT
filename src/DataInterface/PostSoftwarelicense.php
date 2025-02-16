<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostSoftwarelicense extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Software */
  public $software;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?string */
  public $serial;

  /** @var ?int */
  public $number;

  /** @var ?\App\Models\Softwarelicensetype */
  public $softwarelicensetype;

  /** @var ?string */
  public $expire;

  /** @var ?bool */
  public $is_valid;

  /** @var ?bool */
  public $allow_overquota;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?string */
  public $otherserial;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?\App\Models\Softwareversion */
  public $softwareversionsBuy;

  /** @var ?\App\Models\Softwareversion */
  public $softwareversionsUse;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Softwarelicense');
    $softwarelicense = new \App\Models\Softwarelicense();
    $this->definitions = $softwarelicense->getDefinitions();

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

    $this->location = $this->setLocation($data);

    $this->serial = $this->setSerial($data);

    if (
        Validation::attrNumericVal('number')->isValid($data) &&
        isset($data->number)
    )
    {
      $this->number = intval($data->number);
    }


    if (
        Validation::attrNumericVal('softwarelicensetype')->isValid($data) &&
        isset($data->softwarelicensetype)
    )
    {
      $softwarelicensetype = \App\Models\Softwarelicensetype::where('id', $data->softwarelicensetype)->first();
      if (!is_null($softwarelicensetype))
      {
        $this->softwarelicensetype = $softwarelicensetype;
      }
      elseif (intval($data->softwarelicensetype) == 0)
      {
        $emptySoftwarelicensetype = new \App\Models\Softwarelicensetype();
        $emptySoftwarelicensetype->id = 0;
        $this->softwarelicensetype = $emptySoftwarelicensetype;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrDate('expire')->isValid($data) &&
        isset($data->expire)
    )
    {
      $this->expire = $data->expire;
    }

    if (
        Validation::attrStr('is_valid')->isValid($data) &&
        isset($data->is_valid) &&
        $data->is_valid == 'on'
    )
    {
      $this->is_valid = true;
    } else {
      $this->is_valid = false;
    }

    if (
        Validation::attrStr('allow_overquota')->isValid($data) &&
        isset($data->allow_overquota) &&
        $data->allow_overquota == 'on'
    )
    {
      $this->allow_overquota = true;
    } else {
      $this->allow_overquota = false;
    }

    $this->comment = $this->setComment($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

    $this->user = $this->setUser($data);

    $this->group = $this->setGroup($data);

    $this->state = $this->setState($data);

    $this->otherserial = $this->setOtherserial($data);

    $this->is_recursive = $this->setIsrecursive($data);

    if (
        Validation::attrNumericVal('softwareversionsBuy')->isValid($data) &&
        isset($data->softwareversionsBuy)
    )
    {
      $softwareversionsBuy = \App\Models\Softwareversion::where('id', $data->softwareversionsBuy)->first();
      if (!is_null($softwareversionsBuy))
      {
        $this->softwareversionsBuy = $softwareversionsBuy;
      }
      elseif (intval($data->softwareversionsBuy) == 0)
      {
        $emptySoftwareversion = new \App\Models\Softwareversion();
        $emptySoftwareversion->id = 0;
        $this->softwareversionsBuy = $emptySoftwareversion;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('softwareversionsUse')->isValid($data) &&
        isset($data->softwareversionsUse)
    )
    {
      $softwareversionsUse = \App\Models\Softwareversion::where('id', $data->softwareversionsUse)->first();
      if (!is_null($softwareversionsUse))
      {
        $this->softwareversionsUse = $softwareversionsUse;
      }
      elseif (intval($data->softwareversionsUse) == 0)
      {
        $emptySoftwareversion = new \App\Models\Softwareversion();
        $emptySoftwareversion->id = 0;
        $this->softwareversionsUse = $emptySoftwareversion;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->manufacturer = $this->setManufacturer($data);
  }

  /**
   * @return array{name?: string, software?: \App\Models\Software, location?: \App\Models\Location,
   *               serial?: string, number?: int, softwarelicensetype?: \App\Models\Softwarelicensetype,
   *               expire?: string, is_valid?: bool, allow_overquota?: bool, comment?: string,
   *               usertech?: \App\Models\User, grouptech?: \App\Models\Group, user?: \App\Models\User,
   *               group?: \App\Models\Group, state?: \App\Models\State, otherserial?: string,
   *               is_recursive?: bool, softwareversionsBuy?: \App\Models\Softwareversion,
   *               softwareversionsUse?: \App\Models\Softwareversion, manufacturer?: \App\Models\Manufacturer}
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
   * @param-out array{name?: string, software?: \App\Models\Software, location?: \App\Models\Location,
   *                  serial?: string, number?: int, softwarelicensetype?: \App\Models\Softwarelicensetype,
   *                  expire?: string, is_valid?: bool, allow_overquota?: bool, comment?: string,
   *                  usertech?: \App\Models\User, grouptech?: \App\Models\Group, user?: \App\Models\User,
   *                  group?: \App\Models\Group, state?: \App\Models\State, otherserial?: string,
   *                  is_recursive?: bool, softwareversionsBuy?: \App\Models\Softwareversion,
   *                  softwareversionsUse?: \App\Models\Softwareversion, manufacturer?: \App\Models\Manufacturer} $data
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
