<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostMonitor extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Monitortype */
  public $type;

  /** @var ?\App\Models\Monitormodel */
  public $model;

  /** @var ?\App\Models\Manufacturer */
  public $manufacturer;

  /** @var ?string */
  public $serial;

  /** @var ?string */
  public $otherserial;

  /** @var ?string */
  public $contact;

  /** @var ?string */
  public $contact_num;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?string */
  public $comment;

  /** @var ?string */
  public $size;

  /** @var ?bool */
  public $have_micro;

  /** @var ?bool */
  public $have_speaker;

  /** @var ?bool */
  public $have_subd;

  /** @var ?bool */
  public $have_bnc;

  /** @var ?bool */
  public $have_dvi;

  /** @var ?bool */
  public $have_pivot;

  /** @var ?bool */
  public $have_hdmi;

  /** @var ?bool */
  public $have_displayport;

  /** @var ?bool */
  public $is_global;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Monitor');
    $monitor = new \App\Models\Monitor();
    $this->definitions = $monitor->getDefinitions();

    $this->name = $this->setName($data);

    $this->location = $this->setLocation($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Monitortype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Monitortype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('model')->isValid($data) &&
        isset($data->model)
    )
    {
      $model = \App\Models\Monitormodel::where('id', $data->model)->first();
      if (!is_null($model))
      {
        $this->model = $model;
      }
      elseif (intval($data->model) == 0)
      {
        $emptyModel = new \App\Models\Monitormodel();
        $emptyModel->id = 0;
        $this->model = $emptyModel;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->manufacturer = $this->setManufacturer($data);

    $this->serial = $this->setSerial($data);

    $this->otherserial = $this->setOtherserial($data);

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

    $this->user = $this->setUser($data);

    $this->group = $this->setGroup($data);

    $this->comment = $this->setComment($data);

    if (
        Validation::attrStrNotempty('size')->isValid($data) &&
        isset($data->size)
    )
    {
      $this->size = $data->size;
    }

    if (
        Validation::attrStr('have_micro')->isValid($data) &&
        isset($data->have_micro) &&
        $data->have_micro == 'on'
    )
    {
      $this->have_micro = true;
    } else {
      $this->have_micro = true;
    }

    if (
        Validation::attrStr('have_speaker')->isValid($data) &&
        isset($data->have_speaker) &&
        $data->have_speaker == 'on'
    )
    {
      $this->have_speaker = true;
    } else {
      $this->have_speaker = true;
    }

    if (
        Validation::attrStr('have_subd')->isValid($data) &&
        isset($data->have_subd) &&
        $data->have_subd == 'on'
    )
    {
      $this->have_subd = true;
    } else {
      $this->have_subd = true;
    }

    if (
        Validation::attrStr('have_bnc')->isValid($data) &&
        isset($data->have_bnc) &&
        $data->have_bnc == 'on'
    )
    {
      $this->have_bnc = true;
    } else {
      $this->have_bnc = true;
    }

    if (
        Validation::attrStr('have_dvi')->isValid($data) &&
        isset($data->have_dvi) &&
        $data->have_dvi == 'on'
    )
    {
      $this->have_dvi = true;
    } else {
      $this->have_dvi = true;
    }

    if (
        Validation::attrStr('have_pivot')->isValid($data) &&
        isset($data->have_pivot) &&
        $data->have_pivot == 'on'
    )
    {
      $this->have_pivot = true;
    } else {
      $this->have_pivot = true;
    }

    if (
        Validation::attrStr('have_hdmi')->isValid($data) &&
        isset($data->have_hdmi) &&
        $data->have_hdmi == 'on'
    )
    {
      $this->have_hdmi = true;
    } else {
      $this->have_hdmi = true;
    }

    if (
        Validation::attrStr('have_displayport')->isValid($data) &&
        isset($data->have_displayport) &&
        $data->have_displayport == 'on'
    )
    {
      $this->have_displayport = true;
    } else {
      $this->have_displayport = true;
    }

    if (
        Validation::attrStr('is_global')->isValid($data) &&
        isset($data->is_global) &&
        $data->is_global == 'on'
    )
    {
      $this->is_global = true;
    } else {
      $this->is_global = true;
    }

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);
  }

  /**
   * @return array{name?: string, location?: \App\Models\Location, state?: \App\Models\State,
   *               type?: \App\Models\Monitortype, model?: \App\Models\Monitormodel,
   *               manufacturer?: \App\Models\Manufacturer, serial?: string, otherserial?: string,
   *               contact?: string, contact_num?: string, user?: \App\Models\User, group?: \App\Models\Group,
   *               comment?: string, size?: string, have_micro?: bool, have_speaker?: bool, have_subd?: bool,
   *               have_bnc?: bool, have_dvi?: bool, have_pivot?: bool, have_hdmi?: bool, have_displayport?: bool,
   *               is_global?: bool, usertech?: \App\Models\User, grouptech?: \App\Models\Group}
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
   * @param-out array{name?: string, location?: \App\Models\Location, state?: \App\Models\State,
   *                  type?: \App\Models\Monitortype, model?: \App\Models\Monitormodel,
   *                  manufacturer?: \App\Models\Manufacturer, serial?: string, otherserial?: string,
   *                  contact?: string, contact_num?: string, user?: \App\Models\User, group?: \App\Models\Group,
   *                  comment?: string, size?: string, have_micro?: bool, have_speaker?: bool, have_subd?: bool,
   *                  have_bnc?: bool, have_dvi?: bool, have_pivot?: bool, have_hdmi?: bool, have_displayport?: bool,
   *                  is_global?: bool, usertech?: \App\Models\User, grouptech?: \App\Models\Group} $data
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
