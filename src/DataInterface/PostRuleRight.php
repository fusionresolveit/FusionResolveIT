<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

/**
 * @phpstan-type dataFormat array{name?: string, ranking?: int, description?: string, match?: string, is_active?: bool,
 *                                 comment?: string}
 */

class PostRuleRight extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?int */
  public $ranking;

  /** @var ?string */
  public $description;

  /** @var ?string */
  public $match;

  /** @var ?bool */
  public $is_active;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Rules\User');
    $rule = new \App\Models\Rules\User();
    $this->definitions = $rule->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('ranking')->isValid($data) &&
        isset($data->ranking)
    )
    {
      $this->ranking = intval($data->ranking);
    }

    if (
        Validation::attrStrNotempty('description')->isValid($data) &&
        isset($data->description)
    )
    {
      $this->description = $data->description;
    }

    if (
        Validation::attrStrNotempty('match')->isValid($data) &&
        isset($data->match)
    )
    {
      $this->match = $data->match;
    }

    if (
        Validation::attrStr('is_active')->isValid($data) &&
        isset($data->is_active) &&
        $data->is_active == 'on'
    )
    {
      $this->is_active = true;
    } else {
      $this->is_active = true;
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return dataFormat
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
   * @param-out dataFormat $data
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
