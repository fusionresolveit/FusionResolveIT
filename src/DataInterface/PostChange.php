<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostChange extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $content;

  /** @var ?int */
  public $status;

  /** @var ?int */
  public $urgency;

  /** @var ?int */
  public $impact;

  /** @var ?int */
  public $priority;

  /** @var ?int */
  public $time_to_resolve;

  /** @var ?\App\Models\Category */
  public $category;

  /** @var ?int */
  public $actiontime;

  /** @var ?\App\Models\User */
  public $usersidlastupdater;

  /** @var ?\App\Models\User */
  public $usersidrecipient;

  /** @var ?string */
  public $impactcontent;

  /** @var ?string */
  public $controlistcontent;

  /** @var ?string */
  public $rolloutplancontent;

  /** @var ?string */
  public $backoutplancontent;

  /** @var ?string */
  public $checklistcontent;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Change');
    $change = new \App\Models\Change();
    $this->definitions = $change->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('content')->isValid($data) &&
        isset($data->content)
    )
    {
      $this->content = $data->content;
    }

    if (
        Validation::attrNumericVal('status')->isValid($data) &&
        isset($data->status)
    )
    {
      $this->status = intval($data->status);
    }

    if (
        Validation::attrNumericVal('urgency')->isValid($data) &&
        isset($data->urgency)
    )
    {
      $this->urgency = intval($data->urgency);
    }

    if (
        Validation::attrNumericVal('impact')->isValid($data) &&
        isset($data->impact)
    )
    {
      $this->impact = intval($data->impact);
    }

    if (
        Validation::attrNumericVal('priority')->isValid($data) &&
        isset($data->priority)
    )
    {
      $this->priority = intval($data->priority);
    }

    // time_to_resolve

    $this->category = $this->setCategory($data);

    if (
        Validation::attrNumericVal('actiontime')->isValid($data) &&
        isset($data->actiontime)
    )
    {
      $this->actiontime = intval($data->actiontime);
    }

    // usersidlastupdater

    // usersidrecipient

    if (
        Validation::attrStr('impactcontent')->isValid($data) &&
        isset($data->impactcontent)
    )
    {
      $this->impactcontent = $data->impactcontent;
    }

    if (
        Validation::attrStr('controlistcontent')->isValid($data) &&
        isset($data->controlistcontent)
    )
    {
      $this->controlistcontent = $data->controlistcontent;
    }

    if (
        Validation::attrStr('rolloutplancontent')->isValid($data) &&
        isset($data->rolloutplancontent)
    )
    {
      $this->rolloutplancontent = $data->rolloutplancontent;
    }

    if (
        Validation::attrStr('backoutplancontent')->isValid($data) &&
        isset($data->backoutplancontent)
    )
    {
      $this->backoutplancontent = $data->backoutplancontent;
    }

    if (
        Validation::attrStr('checklistcontent')->isValid($data) &&
        isset($data->checklistcontent)
    )
    {
      $this->checklistcontent = $data->checklistcontent;
    }
  }

  /**
   * @return array{name?: string, content?: string, status?: int, urgency?: int, impact?: int, priority?: int,
   *               time_to_resolve?: int, category?: \App\Models\Category, actiontime?: int,
   *               usersidlastupdater?: \App\Models\User, usersidrecipient?: \App\Models\User, impactcontent?: string,
   *               controlistcontent?: string, rolloutplancontent?: string, backoutplancontent?: string,
   *               checklistcontent?: string}
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
   * @param-out array{name?: string, content?: string, status?: int, urgency?: int, impact?: int, priority?: int,
   *                  time_to_resolve?: int, category?: \App\Models\Category, actiontime?: int,
   *                  usersidlastupdater?: \App\Models\User, usersidrecipient?: \App\Models\User,
   *                  impactcontent?: string, controlistcontent?: string, rolloutplancontent?: string,
   *                  backoutplancontent?: string, checklistcontent?: string} $data
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
