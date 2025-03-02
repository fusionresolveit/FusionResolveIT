<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostLocation extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?string */
  public $address;

  /** @var ?string */
  public $postcode;

  /** @var ?string */
  public $town;

  /** @var ?string */
  public $state;

  /** @var ?string */
  public $country;

  /** @var ?string */
  public $building;

  /** @var ?string */
  public $room;

  /** @var ?string */
  public $latitude;

  /** @var ?string */
  public $longitude;

  /** @var ?string */
  public $altitude;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Location');
    $location = new \App\Models\Location();
    $this->definitions = $location->getDefinitions();

    $this->name = $this->setName($data);

    $this->location = $this->setLocation($data);

    if (
        Validation::attrStrNotempty('address')->isValid($data) &&
        isset($data->address)
    )
    {
      $this->address = $data->address;
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
        Validation::attrStrNotempty('building')->isValid($data) &&
        isset($data->building)
    )
    {
      $this->building = $data->building;
    }


    if (
        Validation::attrStrNotempty('room')->isValid($data) &&
        isset($data->room)
    )
    {
      $this->room = $data->room;
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

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, location?: \App\Models\Location, address?: string, postcode?: string,
   *               town?: string, state?: string, country?: string, building?: string, room?: string,
   *               latitude?: string, longitude?: string, altitude?: string, comment?: string,
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
   * @param-out array{name?: string, location?: \App\Models\Location, address?: string, postcode?: string,
   *                  town?: string, state?: string, country?: string, building?: string, room?: string,
   *                  latitude?: string, longitude?: string, altitude?: string, comment?: string,
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
