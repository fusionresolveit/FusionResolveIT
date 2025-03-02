<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostPassivedcequipmentmodel extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $product_number;

  /** @var ?int */
  public $weight;

  /** @var ?int */
  public $required_units;

  /** @var ?float */
  public $depth;

  /** @var ?int */
  public $power_connections;

  /** @var ?int */
  public $power_consumption;

  /** @var ?bool */
  public $is_half_rack;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Passivedcequipmentmodel');
    $passivedcequipmentmodel = new \App\Models\Passivedcequipmentmodel();
    $this->definitions = $passivedcequipmentmodel->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('product_number')->isValid($data) &&
        isset($data->product_number)
    )
    {
      $this->product_number = $data->product_number;
    }

    if (
        Validation::attrNumericVal('weight')->isValid($data) &&
        isset($data->weight)
    )
    {
      $this->weight = intval($data->weight);
    }

    if (
        Validation::attrNumericVal('required_units')->isValid($data) &&
        isset($data->required_units)
    )
    {
      $this->required_units = intval($data->required_units);
    }

    if (
        Validation::attrFloatVal('depth')->isValid($data) &&
        isset($data->depth)
    )
    {
      $this->depth = floatval($data->depth);
    }

    if (
        Validation::attrNumericVal('power_connections')->isValid($data) &&
        isset($data->power_connections)
    )
    {
      $this->power_connections = intval($data->power_connections);
    }

    if (
        Validation::attrNumericVal('power_consumption')->isValid($data) &&
        isset($data->power_consumption)
    )
    {
      $this->power_consumption = intval($data->power_consumption);
    }

    if (
        Validation::attrStr('is_half_rack')->isValid($data) &&
        isset($data->is_half_rack) &&
        $data->is_half_rack == 'on'
    )
    {
      $this->is_half_rack = true;
    } else {
      $this->is_half_rack = true;
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, product_number?: string, weight?: int, required_units?: int, depth?: float,
   *               power_connections?: int, power_consumption?: int, is_half_rack?: bool, comment?: string}
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
   * @param-out array{name?: string, product_number?: string, weight?: int, required_units?: int, depth?: float,
   *                  power_connections?: int, power_consumption?: int, is_half_rack?: bool, comment?: string} $data
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
