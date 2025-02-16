<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostNetworkname extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Fqdn */
  public $fqdn;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Networkname');
    $networkname = new \App\Models\Networkname();
    $this->definitions = $networkname->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('fqdn')->isValid($data) &&
        isset($data->fqdn)
    )
    {
      $fqdn = \App\Models\Fqdn::where('id', $data->fqdn)->first();
      if (!is_null($fqdn))
      {
        $this->fqdn = $fqdn;
      }
      elseif (intval($data->fqdn) == 0)
      {
        $emptyFqdn = new \App\Models\Fqdn();
        $emptyFqdn->id = 0;
        $this->fqdn = $emptyFqdn;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, fqdn?: \App\Models\Fqdn, comment?: string}
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
   * @param-out array{name?: string, fqdn?: \App\Models\Fqdn, comment?: string} $data
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
