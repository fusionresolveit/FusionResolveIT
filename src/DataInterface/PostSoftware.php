<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostSoftware extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\Softwarecategory */
  public $category;

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

  /** @var ?bool */
  public $is_helpdesk_visible;

  /** @var ?bool */
  public $is_valid;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Software');
    $software = new \App\Models\Software();
    $this->definitions = $software->getDefinitions();

    $this->name = $this->setName($data);

    $this->location = $this->setLocation($data);

    $this->comment = $this->setComment($data);

    if (
        Validation::attrNumericVal('category')->isValid($data) &&
        isset($data->category)
    )
    {
      $category = \App\Models\Softwarecategory::where('id', $data->category)->first();
      if (!is_null($category))
      {
        $this->category = $category;
      }
      elseif (intval($data->category) == 0)
      {
        $emptyCategory = new \App\Models\Softwarecategory();
        $emptyCategory->id = 0;
        $this->category = $emptyCategory;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->manufacturer = $this->setManufacturer($data);

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

    $this->user = $this->setUser($data);

    $this->group = $this->setGroup($data);

    if (
        Validation::attrStr('is_helpdesk_visible')->isValid($data) &&
        isset($data->is_helpdesk_visible) &&
        $data->is_helpdesk_visible == 'on'
    )
    {
      $this->is_helpdesk_visible = true;
    } else {
      $this->is_helpdesk_visible = false;
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
  }

  /**
   * @return array{name?: string, location?: \App\Models\Location, comment?: string,
   *               category?: \App\Models\Softwarecategory, manufacturer?: \App\Models\Manufacturer,
   *               usertech?: \App\Models\User, grouptech?: \App\Models\Group, is_helpdesk_visible?: bool,
   *               is_valid?: bool}
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
   * @param-out array{name?: string, location?: \App\Models\Location, comment?: string,
   *                  category?: \App\Models\Softwarecategory, manufacturer?: \App\Models\Manufacturer,
   *                  usertech?: \App\Models\User, grouptech?: \App\Models\Group, is_helpdesk_visible?: bool,
   *                  is_valid?: bool} $data
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
