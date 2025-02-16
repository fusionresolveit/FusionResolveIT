<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostSolutiontemplate extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Solutiontype */
  public $type;

  /** @var ?string */
  public $content;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Solutiontemplate');
    $solutiontemplate = new \App\Models\Solutiontemplate();
    $this->definitions = $solutiontemplate->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Solutiontype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Solutiontype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('content')->isValid($data) &&
        isset($data->content)
    )
    {
      $this->content = $data->content;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, type?: \App\Models\Solutiontype, content?: string,
   *               comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, type?: \App\Models\Solutiontype, content?: string,
   *                  comment?: string, is_recursive?: bool} $data
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
