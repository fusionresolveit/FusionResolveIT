<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostInfocom extends Post
{
  /** @var ?string */
  public $item_type;

  /** @var ?int */
  public $item_id;

  /** @var ?string */
  public $order_date;

  /** @var ?string */
  public $buy_date;

  /** @var ?string */
  public $delivery_date;

  /** @var ?string */
  public $use_date;

  /** @var ?string */
  public $inventory_date;

  /** @var ?string */
  public $decommission_date;

  /** @var ?\App\Models\Supplier */
  public $supplier;

  /** @var ?\App\Models\Budget */
  public $budget;

  /** @var ?string */
  public $order_number;

  /** @var ?string */
  public $immo_number;

  /** @var ?string */
  public $bill;

  /** @var ?string */
  public $delivery_number;

  /** @var ?float */
  public $value;

  /** @var ?float */
  public $warranty_value;

  /** @var ?int */
  public $sink_type;

  /** @var ?int */
  public $sink_time;

  /** @var ?float */
  public $sink_coeff;

  /** @var ?\App\Models\Businesscriticity */
  public $businesscriticity;

  /** @var ?string */
  public $comment;

  /** @var ?string */
  public $warranty_date;

  /** @var ?int */
  public $warranty_duration;

  /** @var ?string */
  public $warranty_info;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Infocom');
    $this->definitions = \App\Models\Definitions\Infocom::getDefinitionInfocom();

    if (
        Validation::attrStr('item_type')->isValid($data) &&
        isset($data->item_type) &&
        class_exists($data->item_type)
    )
    {
      $this->item_type = $data->item_type;
    }

    if (
        Validation::attrNumericVal('item_id')->isValid($data) &&
        isset($data->item_id)
    )
    {
      $this->item_id = intval($data->item_id);
    }

    if (is_null($this->item_id) || is_null($this->item_type))
    {
      throw new \Exception('Wrong data request', 400);
    }

    if (
        Validation::attrDate('order_date')->isValid($data) &&
        isset($data->order_date)
    )
    {
      $this->order_date = $data->order_date;
    }

    if (
        Validation::attrDate('buy_date')->isValid($data) &&
        isset($data->buy_date)
    )
    {
      $this->buy_date = $data->buy_date;
    }

    if (
        Validation::attrDate('delivery_date')->isValid($data) &&
        isset($data->delivery_date)
    )
    {
      $this->delivery_date = $data->delivery_date;
    }

    if (
        Validation::attrDate('use_date')->isValid($data) &&
        isset($data->use_date)
    )
    {
      $this->use_date = $data->use_date;
    }

    if (
        Validation::attrDate('inventory_date')->isValid($data) &&
        isset($data->inventory_date)
    )
    {
      $this->inventory_date = $data->inventory_date;
    }

    if (
        Validation::attrDate('decommission_date')->isValid($data) &&
        isset($data->decommission_date)
    )
    {
      $this->decommission_date = $data->decommission_date;
    }

    if (
        Validation::attrNumericVal('supplier')->isValid($data) &&
        isset($data->supplier)
    )
    {
      $supplier = \App\Models\Supplier::where('id', $data->supplier)->first();
      if (!is_null($supplier))
      {
        $this->supplier = $supplier;
      }
      elseif (intval($data->supplier) == 0)
      {
        $emptySupplier = new \App\Models\Supplier();
        $emptySupplier->id = 0;
        $this->supplier = $emptySupplier;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('budget')->isValid($data) &&
        isset($data->budget)
    )
    {
      $budget = \App\Models\Budget::where('id', $data->budget)->first();
      if (!is_null($budget))
      {
        $this->budget = $budget;
      }
      elseif (intval($data->budget) == 0)
      {
        $emptyBudget = new \App\Models\Budget();
        $emptyBudget->id = 0;
        $this->budget = $emptyBudget;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('order_number')->isValid($data) &&
        isset($data->order_number)
    )
    {
      $this->order_number = $data->order_number;
    }

    if (
        Validation::attrStr('immo_number')->isValid($data) &&
        isset($data->immo_number)
    )
    {
      $this->immo_number = $data->immo_number;
    }

    if (
        Validation::attrStr('bill')->isValid($data) &&
        isset($data->bill)
    )
    {
      $this->bill = $data->bill;
    }

    if (
        Validation::attrStr('delivery_number')->isValid($data) &&
        isset($data->delivery_number)
    )
    {
      $this->delivery_number = $data->delivery_number;
    }

    if (
        Validation::attrFloatVal('value')->isValid($data) &&
        isset($data->value)
    )
    {
      $this->value = floatval($data->value);
    }

    if (
        Validation::attrFloatVal('warranty_value')->isValid($data) &&
        isset($data->warranty_value)
    )
    {
      $this->warranty_value = floatval($data->warranty_value);
    }

    // if (
    //   Validation::attrNumericVal('sink_type')->isValid($data) &&
    //   isset($data->sink_type)
    // )
    // {
    //   $this->sink_type = intval($data->sink_type);
    // }

    // if (
    //   Validation::attrNumericVal('sink_time')->isValid($data) &&
    //   isset($data->sink_time)
    // )
    // {
    //   $this->sink_time = intval($data->sink_time);
    // }

    if (
        Validation::attrFloatVal('sink_coeff')->isValid($data) &&
        isset($data->sink_coeff)
    )
    {
      $this->sink_coeff = floatval($data->sink_coeff);
    }

    if (
        Validation::attrNumericVal('businesscriticity')->isValid($data) &&
        isset($data->businesscriticity)
    )
    {
      $businesscriticity = \App\Models\Businesscriticity::where('id', $data->businesscriticity)->first();
      if (!is_null($businesscriticity))
      {
        $this->businesscriticity = $businesscriticity;
      }
      elseif (intval($data->businesscriticity) == 0)
      {
        $emptyBusinesscriticity = new \App\Models\Businesscriticity();
        $emptyBusinesscriticity->id = 0;
        $this->businesscriticity = $emptyBusinesscriticity;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    if (
        Validation::attrDate('warranty_date')->isValid($data) &&
        isset($data->warranty_date)
    )
    {
      $this->warranty_date = $data->warranty_date;
    }

    // if (
    //   Validation::attrNumericVal('warranty_duration')->isValid($data) &&
    //   isset($data->warranty_duration)
    // )
    // {
    //   $this->warranty_duration = intval($data->warranty_duration);
    // }

    if (
        Validation::attrStr('warranty_info')->isValid($data) &&
        isset($data->warranty_info)
    )
    {
      $this->warranty_info = $data->warranty_info;
    }
  }

  /**
   * @return array{order_date?: string, buy_date?: string, delivery_date?: string, use_date?: string,
   *               inventory_date?: string, decommission_date?: string, supplier?: \App\Models\Supplier,
   *               budget?: \App\Models\Budget, order_number?: string, immo_number?: string, bill?: string,
   *               delivery_number?: string, value?: float, warranty_value?: float, sink_type?: int,
   *               sink_time?: int, sink_coeff?: float, businesscriticity?: \App\Models\Businesscriticity,
   *               comment?: string, warranty_date?: string, warranty_duration?: int, warranty_info?: string}
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
   * @param-out array{order_date?: string, buy_date?: string, delivery_date?: string, use_date?: string,
   *                  inventory_date?: string, decommission_date?: string, supplier?: \App\Models\Supplier,
   *                  budget?: \App\Models\Budget, order_number?: string, immo_number?: string, bill?: string,
   *                  delivery_number?: string, value?: float, warranty_value?: float, sink_type?: int,
   *                  sink_time?: int, sink_coeff?: float, businesscriticity?: \App\Models\Businesscriticity,
   *                  comment?: string, warranty_date?: string, warranty_duration?: int, warranty_info?: string} $data
   */
  private function getFieldForArray(string $key, mixed &$data): void
  {
    if ($key == 'item_type' || $key == 'item_id')
    {
      $data[$key] = $this->{$key};
      return;
    }
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
