<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostDocumenttype extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $ext;

  /** @var ?string */
  public $mime;

  /** @var ?bool */
  public $is_uploadable;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Documenttype');
    $documenttype = new \App\Models\Documenttype();
    $this->definitions = $documenttype->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('ext')->isValid($data) &&
        isset($data->ext)
    )
    {
      $this->ext = $data->ext;
    }

    if (
        Validation::attrStrNotempty('mime')->isValid($data) &&
        isset($data->mime)
    )
    {
      $this->mime = $data->mime;
    }

    if (
        Validation::attrStr('is_uploadable')->isValid($data) &&
        isset($data->is_uploadable) &&
        $data->is_uploadable == 'on'
    )
    {
      $this->is_uploadable = true;
    } else {
      $this->is_uploadable = false;
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, ext?: string, mime?: string, is_uploadable?: bool, comment?: string}
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
   * @param-out array{name?: string, ext?: string, mime?: string, is_uploadable?: bool, comment?: string} $data
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
