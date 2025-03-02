<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostIpnetwork extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $address;

  /** @var ?string */
  public $netmask;

  /** @var ?string */
  public $gateway;

  /** @var ?bool */
  public $addressable;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Ipnetwork');
    $ipnetwork = new \App\Models\Ipnetwork();
    $this->definitions = $ipnetwork->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('address')->isValid($data) &&
        isset($data->address)
    )
    {
      $this->address = $data->address;
    }

    if (
        Validation::attrStrNotempty('netmask')->isValid($data) &&
        isset($data->netmask)
    )
    {
      $this->netmask = $data->netmask;
    }

    if (
        Validation::attrStrNotempty('gateway')->isValid($data) &&
        isset($data->gateway)
    )
    {
      $this->gateway = $data->gateway;
    }

    if (
        Validation::attrStr('addressable')->isValid($data) &&
        isset($data->addressable) &&
        $data->addressable == 'on'
    )
    {
      $this->addressable = true;
    } else {
      $this->addressable = false;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, address?: string, netmask?: string, gateway?: string, addressable?: bool,
   *               comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, address?: string, netmask?: string, gateway?: string, addressable?: bool,
   *                  comment?: string, is_recursive?: bool} $data
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
