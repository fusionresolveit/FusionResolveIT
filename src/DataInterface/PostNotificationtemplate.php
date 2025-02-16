<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostNotificationtemplate extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $item_type;

  /** @var ?string */
  public $comment;

  /** @var ?string */
  public $css;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Notificationtemplate');
    $notificationtemplate = new \App\Models\Notificationtemplate();
    $this->definitions = $notificationtemplate->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('item_type')->isValid($data) &&
        isset($data->item_type)
    )
    {
      $this->item_type = $data->item_type;
    }

    $this->comment = $this->setComment($data);

    if (
        Validation::attrStrNotempty('css')->isValid($data) &&
        isset($data->css)
    )
    {
      $this->css = $data->css;
    }
  }

  /**
   * @return array{name?: string, item_type?: string, comment?: string, css?: string}
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
   * @param-out array{name?: string, item_type?: string, comment?: string, css?: string} $data
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
