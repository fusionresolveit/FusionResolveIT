<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostState extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?bool */
  public $is_visible_computer;

  /** @var ?bool */
  public $is_visible_monitor;

  /** @var ?bool */
  public $is_visible_networkequipment;

  /** @var ?bool */
  public $is_visible_peripheral;

  /** @var ?bool */
  public $is_visible_phone;

  /** @var ?bool */
  public $is_visible_printer;

  /** @var ?bool */
  public $is_visible_softwarelicense;

  /** @var ?bool */
  public $is_visible_certificate;

  /** @var ?bool */
  public $is_visible_enclosure;

  /** @var ?bool */
  public $is_visible_pdu;

  /** @var ?bool */
  public $is_visible_line;

  /** @var ?bool */
  public $is_visible_rack;

  /** @var ?bool */
  public $is_visible_softwareversion;

  /** @var ?bool */
  public $is_visible_cluster;

  /** @var ?bool */
  public $is_visible_contract;

  /** @var ?bool */
  public $is_visible_appliance;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\State');
    $state = new \App\Models\State();
    $this->definitions = $state->getDefinitions();

    $this->name = $this->setName($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrStr('is_visible_computer')->isValid($data) &&
        isset($data->is_visible_computer) &&
        $data->is_visible_computer == 'on'
    )
    {
      $this->is_visible_computer = true;
    } else {
      $this->is_visible_computer = false;
    }

    if (
        Validation::attrStr('is_visible_monitor')->isValid($data) &&
        isset($data->is_visible_monitor) &&
        $data->is_visible_monitor == 'on'
    )
    {
      $this->is_visible_monitor = true;
    } else {
      $this->is_visible_monitor = false;
    }

    if (
        Validation::attrStr('is_visible_networkequipment')->isValid($data) &&
        isset($data->is_visible_networkequipment) &&
        $data->is_visible_networkequipment == 'on'
    )
    {
      $this->is_visible_networkequipment = true;
    } else {
      $this->is_visible_networkequipment = false;
    }

    if (
        Validation::attrStr('is_visible_peripheral')->isValid($data) &&
        isset($data->is_visible_peripheral) &&
        $data->is_visible_peripheral == 'on'
    )
    {
      $this->is_visible_peripheral = true;
    } else {
      $this->is_visible_peripheral = false;
    }

    if (
        Validation::attrStr('is_visible_phone')->isValid($data) &&
        isset($data->is_visible_phone) &&
        $data->is_visible_phone == 'on'
    )
    {
      $this->is_visible_phone = true;
    } else {
      $this->is_visible_phone = false;
    }

    if (
        Validation::attrStr('is_visible_printer')->isValid($data) &&
        isset($data->is_visible_printer) &&
        $data->is_visible_printer == 'on'
    )
    {
      $this->is_visible_printer = true;
    } else {
      $this->is_visible_printer = false;
    }

    if (
        Validation::attrStr('is_visible_softwarelicense')->isValid($data) &&
        isset($data->is_visible_softwarelicense) &&
        $data->is_visible_softwarelicense == 'on'
    )
     {
      $this->is_visible_softwarelicense = true;
    } else {
      $this->is_visible_softwarelicense = false;
    }

    if (
        Validation::attrStr('is_visible_certificate')->isValid($data) &&
        isset($data->is_visible_certificate) &&
        $data->is_visible_certificate == 'on'
    )
    {
      $this->is_visible_certificate = true;
    } else {
      $this->is_visible_certificate = false;
    }

    if (
        Validation::attrStr('is_visible_enclosure')->isValid($data) &&
        isset($data->is_visible_enclosure) &&
        $data->is_visible_enclosure == 'on'
    )
    {
      $this->is_visible_enclosure = true;
    } else {
      $this->is_visible_enclosure = false;
    }

    if (
        Validation::attrStr('is_visible_pdu')->isValid($data) &&
        isset($data->is_visible_pdu) &&
        $data->is_visible_pdu == 'on'
    )
    {
      $this->is_visible_pdu = true;
    } else {
      $this->is_visible_pdu = false;
    }

    if (
        Validation::attrStr('is_visible_line')->isValid($data) &&
        isset($data->is_visible_line) &&
        $data->is_visible_line == 'on'
    )
    {
      $this->is_visible_line = true;
    } else {
      $this->is_visible_line = false;
    }

    if (
        Validation::attrStr('is_visible_rack')->isValid($data) &&
        isset($data->is_visible_rack) &&
        $data->is_visible_rack == 'on'
    )
    {
      $this->is_visible_rack = true;
    } else {
      $this->is_visible_rack = false;
    }

    if (
        Validation::attrStr('is_visible_softwareversion')->isValid($data) &&
        isset($data->is_visible_softwareversion) &&
        $data->is_visible_softwareversion == 'on'
    )
    {
      $this->is_visible_softwareversion = true;
    } else {
      $this->is_visible_softwareversion = false;
    }

    if (
        Validation::attrStr('is_visible_cluster')->isValid($data) &&
        isset($data->is_visible_cluster) &&
        $data->is_visible_cluster == 'on'
    )
    {
      $this->is_visible_cluster = true;
    } else {
      $this->is_visible_cluster = false;
    }

    if (
        Validation::attrStr('is_visible_contract')->isValid($data) &&
        isset($data->is_visible_contract) &&
        $data->is_visible_contract == 'on'
    )
    {
      $this->is_visible_contract = true;
    } else {
      $this->is_visible_contract = false;
    }

    if (
        Validation::attrStr('is_visible_appliance')->isValid($data) &&
        isset($data->is_visible_appliance) &&
        $data->is_visible_appliance == 'on'
    )
    {
      $this->is_visible_appliance = true;
    } else {
      $this->is_visible_appliance = false;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, state?: \App\Models\State, is_visible_computer?: bool, is_visible_monitor?: bool,
   *               is_visible_networkequipment?: bool, is_visible_peripheral?: bool, is_visible_phone?: bool,
   *               is_visible_printer?: bool, is_visible_softwarelicense?: bool, is_visible_certificate?: bool,
   *               is_visible_enclosure?: bool, is_visible_pdu?: bool, is_visible_line?: bool,
   *               is_visible_rack?: bool, is_visible_softwareversion?: bool, is_visible_cluster?: bool,
   *               is_visible_contract?: bool, is_visible_appliance?: bool, comment?: string,
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
   * @param-out array{name?: string, state?: \App\Models\State, is_visible_computer?: bool, is_visible_monitor?: bool,
   *                  is_visible_networkequipment?: bool, is_visible_peripheral?: bool, is_visible_phone?: bool,
   *                  is_visible_printer?: bool, is_visible_softwarelicense?: bool, is_visible_certificate?: bool,
   *                  is_visible_enclosure?: bool, is_visible_pdu?: bool, is_visible_line?: bool,
   *                  is_visible_rack?: bool, is_visible_softwareversion?: bool, is_visible_cluster?: bool,
   *                  is_visible_contract?: bool, is_visible_appliance?: bool, comment?: string,
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
