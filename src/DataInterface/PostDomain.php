<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostDomain extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Domaintype */
  public $type;

  /** @var ?\App\Models\User */
  public $usertech;

  /** @var ?\App\Models\Group */
  public $grouptech;

  /** @var ?string */
  public $date_expiration;

  /** @var ?string */
  public $comment;

  /** @var ?string */
  public $others;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Domain');
    $domain = new \App\Models\Domain();
    $this->definitions = $domain->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Domaintype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Domaintype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->usertech = $this->setUsertech($data);

    $this->grouptech = $this->setGrouptech($data);

    if (
        Validation::attrDate('date_expiration')->isValid($data) &&
        isset($data->date_expiration)
    )
    {
      $this->date_expiration = $data->date_expiration;
    }

    $this->comment = $this->setComment($data);

    if (
        Validation::attrStrNotempty('others')->isValid($data) &&
        isset($data->others)
    )
    {
      $this->others = $data->others;
    }

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, type?: \App\Models\Domaintype, usertech?: \App\Models\User,
   *               grouptech?: \App\Models\Group, date_expiration?: string, comment?: string, others?: string,
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
   * @param-out array{name?: string, type?: \App\Models\Domaintype, usertech?: \App\Models\User,
   *                  grouptech?: \App\Models\Group, date_expiration?: string, comment?: string, others?: string,
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
