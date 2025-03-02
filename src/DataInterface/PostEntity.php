<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostEntity extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Entity */
  public $entity;

  /** @var ?string */
  public $comment;

  /** @var ?string */
  public $address;

  /** @var ?string */
  public $website;

  /** @var ?string */
  public $phonenumber;

  /** @var ?string */
  public $email;

  /** @var ?string */
  public $fax;

  /** @var ?string */
  public $postcode;

  /** @var ?string */
  public $town;

  /** @var ?string */
  public $state;

  /** @var ?string */
  public $country;

  /** @var ?string */
  public $latitude;

  /** @var ?string */
  public $longitude;

  /** @var ?string */
  public $altitude;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Entity');
    $entity = new \App\Models\Entity();
    $this->definitions = $entity->getDefinitions();

    $this->name = $this->setName($data);

    $this->entity = $this->setEntity($data);

    $this->comment = $this->setComment($data);

    if (
        Validation::attrStrNotempty('address')->isValid($data) &&
        isset($data->address)
    )
    {
      $this->address = $data->address;
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
        Validation::attrStrNotempty('fax')->isValid($data) &&
        isset($data->fax)
    )
    {
      $this->fax = $data->fax;
    }

    if (
        Validation::attrStrNotempty('postcode')->isValid($data) &&
        isset($data->postcode)
    )
    {
      $this->postcode = $data->postcode;
    }

    if (
        Validation::attrStrNotempty('town')->isValid($data) &&
        isset($data->town)
    )
    {
      $this->town = $data->town;
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
        Validation::attrStrNotempty('latitude')->isValid($data) &&
        isset($data->latitude)
    )
    {
      $this->latitude = $data->latitude;
    }

    if (
        Validation::attrStrNotempty('longitude')->isValid($data) &&
        isset($data->longitude)
    )
    {
      $this->longitude = $data->longitude;
    }

    if (
        Validation::attrStrNotempty('altitude')->isValid($data) &&
        isset($data->altitude)
    )
    {
      $this->altitude = $data->altitude;
    }
  }

  /**
   * @return array{name?: string, entity?: \App\Models\Entity, comment?: string, address?: string,
   *               website?: string, phonenumber?: string, email?: string, fax?: string, postcode?: string,
   *               town?: string, state?: string, country?: string, latitude?: string, longitude?: string,
   *               altitude?: string}
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
   * @param-out array{name?: string, entity?: \App\Models\Entity, comment?: string, address?: string,
   *                  website?: string, phonenumber?: string, email?: string, fax?: string, postcode?: string,
   *                  town?: string, state?: string, country?: string, latitude?: string, longitude?: string,
   *                  altitude?: string} $data
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
