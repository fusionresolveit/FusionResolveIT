<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostFollowuptemplate extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $content;

  /** @var ?\App\Models\Requesttype */
  public $source;

  /** @var ?bool */
  public $is_private;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Followuptemplate');
    $followuptemplate = new \App\Models\Followuptemplate();
    $this->definitions = $followuptemplate->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('content')->isValid($data) &&
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

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, content?: string, source?: \App\Models\Requesttype, is_private?: bool,
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
   * @param-out array{name?: string, content?: string, source?: \App\Models\Requesttype, is_private?: bool,
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
