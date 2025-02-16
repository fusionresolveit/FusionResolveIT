<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostSoftwarelicensetype extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Softwarelicensetype */
  public $softwarelicensetype;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Softwarelicensetype');
    $softwarelicensetype = new \App\Models\Softwarelicensetype();
    $this->definitions = $softwarelicensetype->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('softwarelicensetype')->isValid($data) &&
        isset($data->softwarelicensetype)
    )
    {
      $softwarelicensetype = \App\Models\Softwarelicensetype::where('id', $data->softwarelicensetype)->first();
      if (!is_null($softwarelicensetype))
      {
        $this->softwarelicensetype = $softwarelicensetype;
      }
      elseif (intval($data->softwarelicensetype) == 0)
      {
        $emptySoftwarelicensetype = new \App\Models\Softwarelicensetype();
        $emptySoftwarelicensetype->id = 0;
        $this->softwarelicensetype = $emptySoftwarelicensetype;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->is_recursive = $this->setIsrecursive($data);

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, softwarelicensetype?: \App\Models\Softwarelicensetype,
   *               is_recursive?: bool, comment?: string}
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
   * @param-out array{name?: string, softwarelicensetype?: \App\Models\Softwarelicensetype,
   *                  is_recursive?: bool, comment?: string} $data
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
