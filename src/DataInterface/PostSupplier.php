<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostSupplier extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $address;

  /** @var ?string */
  public $fax;

  /** @var ?string */
  public $town;

  /** @var ?string */
  public $postcode;

  /** @var ?string */
  public $state;

  /** @var ?string */
  public $country;

  /** @var ?string */
  public $website;

  /** @var ?string */
  public $phonenumber;

  /** @var ?string */
  public $email;

  /** @var ?\App\Models\Suppliertype */
  public $type;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Supplier');
    $supplier = new \App\Models\Supplier();
    $this->definitions = $supplier->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('address')->isValid($data) &&
        isset($data->address)
    )
    {
      $this->address = $data->address;
    }

    if (
        Validation::attrStrNotempty('fax')->isValid($data) &&
        isset($data->fax)
    )
    {
      $this->fax = $data->fax;
    }

    if (
        Validation::attrStrNotempty('town')->isValid($data) &&
        isset($data->town)
    )
    {
      $this->town = $data->town;
    }

    if (
        Validation::attrStrNotempty('postcode')->isValid($data) &&
        isset($data->postcode)
    )
    {
      $this->postcode = $data->postcode;
    }

    if (
        Validation::attrStrNotempty('state')->isValid($data) &&
        isset($data->state)
    )
    {
      $this->state = $data->state;
    }

    if (
        Validation::attrStrNotempty('country')->isValid($data) &&
        isset($data->country)
    )
     {
      $this->country = $data->country;
    }

    if (
        Validation::attrStrNotempty('website')->isValid($data) &&
        isset($data->website)
    )
    {
      $this->website = $data->website;
    }

    if (
        Validation::attrStrNotempty('phonenumber')->isValid($data) &&
        isset($data->phonenumber)
    )
    {
      $this->phonenumber = $data->phonenumber;
    }

    if (
        Validation::attrStrNotempty('email')->isValid($data) &&
        isset($data->email)
    )
    {
      $this->email = $data->email;
    }

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Suppliertype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Suppliertype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, address?: string, fax?: string, town?: string, postcode?: string,
   *               state?: string, country?: string, website?: string, phonenumber?: string, email?: string,
   *               type?: \App\Models\Suppliertype, comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, address?: string, fax?: string, town?: string, postcode?: string,
   *                  state?: string, country?: string, website?: string, phonenumber?: string, email?: string,
   *                  type?: \App\Models\Suppliertype, comment?: string, is_recursive?: bool} $data
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
