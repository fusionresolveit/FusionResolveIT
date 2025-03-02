<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostAuthsso extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_active;

  /** @var ?string */
  public $provider;

  /** @var ?string */
  public $callbackid;

  /** @var ?string */
  public $applicationid;

  /** @var ?string */
  public $applicationsecret;

  /** @var ?string */
  public $applicationpublic;

  /** @var ?string */
  public $directoryid;

  /** @var ?string */
  public $baseurl;

  /** @var ?string */
  public $realm;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Authsso');
    $authsso = new \App\Models\Authsso();
    $this->definitions = $authsso->getDefinitions();

    $this->name = $this->setName($data);

    $this->comment = $this->setComment($data);

    if (
        Validation::attrStr('is_active')->isValid($data) &&
        isset($data->is_active) &&
        $data->is_active == 'on'
    )
    {
      $this->is_active = true;
    } else {
      $this->is_active = false;
    }

    if (
        Validation::attrStr('provider')->isValid($data) &&
        isset($data->provider)
    )
    {
      $this->provider = $data->provider;
    }

    if (
        Validation::attrStr('callbackid')->isValid($data) &&
        isset($data->callbackid)
    )
    {
      $this->callbackid = $data->callbackid;
    }

    if (
        Validation::attrStr('applicationid')->isValid($data) &&
        isset($data->applicationid)
    )
    {
      $this->applicationid = $data->applicationid;
    }

    if (
        Validation::attrStr('applicationsecret')->isValid($data) &&
        isset($data->applicationsecret)
    )
    {
      $this->applicationsecret = $data->applicationsecret;
    }

    if (
        Validation::attrStr('applicationpublic')->isValid($data) &&
        isset($data->applicationpublic)
    )
    {
      $this->applicationpublic = $data->applicationpublic;
    }

    if (
        Validation::attrStr('directoryid')->isValid($data) &&
        isset($data->directoryid)
    )
    {
      $this->directoryid = $data->directoryid;
    }

    if (
        Validation::attrStr('baseurl')->isValid($data) &&
        isset($data->baseurl)
    )
    {
      $this->baseurl = $data->baseurl;
    }

    if (
        Validation::attrStr('realm')->isValid($data) &&
        isset($data->realm)
    )
    {
      $this->realm = $data->realm;
    }
  }

  /**
   * @return array{name?: string, comment?: string, is_active?: bool, provider?: string, callbackid?: string,
   *               applicationid?: string, applicationsecret?: string, applicationpublic?: string,
   *               directoryid?: string, baseurl?: string, realm?: string}
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
   * @param-out array{name?: string, comment?: string, is_active?: bool, provider?: string, callbackid?: string,
   *                  applicationid?: string, applicationsecret?: string, applicationpublic?: string,
   *                  directoryid?: string, baseurl?: string, realm?: string} $data
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
