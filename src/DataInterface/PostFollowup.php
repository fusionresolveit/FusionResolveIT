<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostFollowup extends Post
{
  /** @var ?string */
  public $content;

  /** @var ?\App\Models\Requesttype */
  public $source;

  /** @var ?bool */
  public $is_private;

  /** @var ?bool */
  public $is_tech;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?int */
  protected $item_id;

  /** @var ?string */
  protected $item_type;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Followup');
    $followup = new \App\Models\Followup();
    $this->definitions = $followup->getDefinitions();

    // check have item_id and item_type in $data
    if (
        Validation::attrNumericVal('item_id')->isValid($data) &&
        isset($data->item_id) &&
        Validation::attrStr('item_type')->isValid($data) &&
        isset($data->item_type)
    )
    {
      if (class_exists($data->item_type) && str_starts_with($data->item_type, 'App\Models'))
      {
        $model = new $data->item_type();
        $parentItem = $model->where('id', $data->item_id)->first();
        if (is_null($parentItem))
        {
          throw new \Exception('Wrong data request12', 400);
        }
        $this->item_id = intval($data->item_id);
        $this->item_type = $data->item_type;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    } else {
      throw new \Exception('Wrong data request', 400);
    }

    if (
        Validation::attrStr('content')->isValid($data) &&
        isset($data->content)
    )
    {
      $this->content = $data->content;
    }

    if (
        Validation::attrNumericVal('source')->isValid($data) &&
        isset($data->source)
    )
    {
      $source = \App\Models\Requesttype::where('id', $data->source)->first();
      if (!is_null($source))
      {
        $this->source = $source;
      }
      elseif (intval($data->source) == 0)
      {
        $emptySource = new \App\Models\Requesttype();
        $emptySource->id = 0;
        $this->source = $emptySource;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('is_private')->isValid($data) &&
        isset($data->is_private) &&
        $data->is_private == 'on'
    )
    {
      $this->is_private = true;
    } else {
      $this->is_private = false;
    }

    if (
        Validation::attrStr('is_tech')->isValid($data) &&
        isset($data->is_tech) &&
        $data->is_tech == 'on'
    )
    {
      $this->is_tech = true;
    } else {
      $this->is_tech = false;
    }

    $this->user = $this->setUser($data);
  }

  /**
   * @return array{content?: string, requesttype_id?: int, is_private?: bool,
   *               is_tech?: bool, user?: \App\Models\User}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    $vars = get_object_vars($this);
    $data = [
      'item_id'   => $this->item_id,
      'item_type' => $this->item_type,
    ];
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
   * @param-out array{content?: string, requesttype_id?: int, is_private?: bool,
   *                  is_tech?: bool, user?: \App\Models\User} $data
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
