<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostRequesttype extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_active;

  /** @var ?bool */
  public $is_helpdesk_default;

  /** @var ?bool */
  public $is_followup_default;

  /** @var ?bool */
  public $is_mail_default;

  /** @var ?bool */
  public $is_mailfollowup_default;

  /** @var ?bool */
  public $is_ticketheader;

  /** @var ?bool */
  public $is_itilfollowup;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Requesttype');
    $requesttype = new \App\Models\Requesttype();
    $this->definitions = $requesttype->getDefinitions();

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
        Validation::attrStr('is_helpdesk_default')->isValid($data) &&
        isset($data->is_helpdesk_default) &&
        $data->is_helpdesk_default == 'on'
    )
    {
      $this->is_helpdesk_default = true;
    } else {
      $this->is_helpdesk_default = false;
    }

    if (
        Validation::attrStr('is_followup_default')->isValid($data) &&
        isset($data->is_followup_default) &&
        $data->is_followup_default == 'on'
    )
    {
      $this->is_followup_default = true;
    } else {
      $this->is_followup_default = false;
    }

    if (
        Validation::attrStr('is_mail_default')->isValid($data) &&
        isset($data->is_mail_default) &&
        $data->is_mail_default == 'on'
    )
    {
      $this->is_mail_default = true;
    } else {
      $this->is_mail_default = false;
    }

    if (
        Validation::attrStr('is_mailfollowup_default')->isValid($data) &&
        isset($data->is_mailfollowup_default) &&
        $data->is_mailfollowup_default == 'on'
    )
    {
      $this->is_mailfollowup_default = true;
    } else {
      $this->is_mailfollowup_default = false;
    }

    if (
        Validation::attrStr('is_ticketheader')->isValid($data) &&
        isset($data->is_ticketheader) &&
        $data->is_ticketheader == 'on'
    )
    {
      $this->is_ticketheader = true;
    } else {
      $this->is_ticketheader = false;
    }

    if (
        Validation::attrStr('is_itilfollowup')->isValid($data) &&
        isset($data->is_itilfollowup) &&
        $data->is_itilfollowup == 'on'
    )
    {
      $this->is_itilfollowup = true;
    } else {
      $this->is_itilfollowup = false;
    }
  }

  /**
   * @return array{name?: string, comment?: string, is_active?: bool, is_helpdesk_default?: bool,
   *               is_followup_default?: bool, is_mail_default?: bool, is_mailfollowup_default?: bool,
   *               is_ticketheader?: bool, is_itilfollowup?: bool}
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
   * @param-out array{name?: string, comment?: string, is_active?: bool, is_helpdesk_default?: bool,
   *                  is_followup_default?: bool, is_mail_default?: bool, is_mailfollowup_default?: bool,
   *                  is_ticketheader?: bool, is_itilfollowup?: bool} $data
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
