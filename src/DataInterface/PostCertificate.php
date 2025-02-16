<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostCertificate extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?string */
  public $serial;

  /** @var ?string */
  public $otherserial;

  /** @var ?\App\Models\Certificatetype */
  public $type;

  /** @var ?string */
  public $dns_suffix;

  /** @var ?string */
  public $dns_name;

  /** @var ?bool */
  public $is_autosign;

  /** @var ?string */
  public $date_expiration;

  /** @var ?string */
  public $command;

  /** @var ?string */
  public $certificate_request;

  /** @var ?string */
  public $certificate_item;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?string */
  public $contact;

  /** @var ?string */
  public $contact_num;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Certificate');
    $certificate = new \App\Models\Certificate();
    $this->definitions = $certificate->getDefinitions();

    $this->name = $this->setName($data);

    $this->state = $this->setState($data);

    $this->location = $this->setLocation($data);

    $this->serial = $this->setSerial($data);

    $this->otherserial = $this->setOtherserial($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Certificatetype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyCertificatetype = new \App\Models\Certificatetype();
        $emptyCertificatetype->id = 0;
        $this->type = $emptyCertificatetype;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('dns_suffix')->isValid($data) &&
        isset($data->dns_suffix)
    )
    {
      $this->dns_suffix = $data->dns_suffix;
    }

    if (
        Validation::attrStr('dns_name')->isValid($data) &&
        isset($data->dns_name)
    )
    {
      $this->dns_name = $data->dns_name;
    }

    if (
        Validation::attrStr('is_autosign')->isValid($data) &&
        isset($data->is_autosign) &&
        $data->is_autosign == 'on'
    )
    {
      $this->is_autosign = true;
    } else {
      $this->is_autosign = false;
    }

    if (
        Validation::attrDate('date_expiration')->isValid($data) &&
        isset($data->date_expiration)
    )
    {
      $this->date_expiration = $data->date_expiration;
    }

    if (
        Validation::attrStr('command')->isValid($data) &&
        isset($data->command)
    )
    {
      $this->command = $data->command;
    }

    if (
        Validation::attrStr('certificate_request')->isValid($data) &&
        isset($data->certificate_request)
    )
    {
      $this->certificate_request = $data->certificate_request;
    }

    if (
        Validation::attrStr('certificate_item')->isValid($data) &&
        isset($data->certificate_item)
    )
    {
      $this->certificate_item = $data->certificate_item;
    }

    $this->comment = $this->setComment($data);

    $this->manufacturer = $this->setManufacturer($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

    $this->user = $this->setUser($data);

    $this->group = $this->setGroup($data);

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

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, state?: \App\Models\State, location?: \App\Models\Location, serial?: string,
   *               otherserial?: string, type?: \App\Models\Certificatetype, dns_suffix?: string,
   *               dns_name?: string, is_autosign?: bool, date_expiration?: string, command?: string,
   *               certificate_request?: string, certificate_item?: string, comment?: string,
   *               manufacturer?: \App\Models\Manufacturer, usertech?: \App\Models\User,
   *               grouptech?: \App\Models\Group, user?: \App\Models\User, group?: \App\Models\Group,
   *               contact?: string, contact_num?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, state?: \App\Models\State, location?: \App\Models\Location, serial?: string,
   *                  otherserial?: string, type?: \App\Models\Certificatetype, dns_suffix?: string,
   *                  dns_name?: string, is_autosign?: bool, date_expiration?: string, command?: string,
   *                  certificate_request?: string, certificate_item?: string, comment?: string,
   *                  manufacturer?: \App\Models\Manufacturer, usertech?: \App\Models\User,
   *                  grouptech?: \App\Models\Group, user?: \App\Models\User, group?: \App\Models\Group,
   *                  contact?: string, contact_num?: string, is_recursive?: bool} $data
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
