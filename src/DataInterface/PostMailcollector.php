<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostMailcollector extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?bool */
  public $is_active;

  /** @var ?string */
  public $accepted;

  /** @var ?string */
  public $refused;

  /** @var ?bool */
  public $use_mail_date;

  /** @var ?bool */
  public $requester_field;

  /** @var ?bool */
  public $add_cc_to_observer;

  /** @var ?bool */
  public $collect_only_unread;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_oauth;

  /** @var ?string */
  public $oauth_provider;

  /** @var ?string */
  public $oauth_applicationid;

  /** @var ?string */
  public $oauth_directoryid;

  /** @var ?string */
  public $oauth_applicationsecret;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Mailcollector');
    $mailcollector = new \App\Models\Mailcollector();
    $this->definitions = $mailcollector->getDefinitions();

    $this->name = $this->setName($data);

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
        Validation::attrStrNotempty('accepted')->isValid($data) &&
        isset($data->accepted)
    )
    {
      $this->accepted = $data->accepted;
    }

    if (
        Validation::attrStrNotempty('refused')->isValid($data) &&
        isset($data->refused)
    )
    {
      $this->refused = $data->refused;
    }

    if (
        Validation::attrStr('use_mail_date')->isValid($data) &&
        isset($data->use_mail_date) &&
        $data->use_mail_date == 'on'
    )
    {
      $this->use_mail_date = true;
    } else {
      $this->use_mail_date = false;
    }

    if (
        Validation::attrStr('requester_field')->isValid($data) &&
        isset($data->requester_field) &&
        $data->requester_field == 'on'
    )
    {
      $this->requester_field = true;
    } else {
      $this->requester_field = false;
    }

    if (
        Validation::attrStr('add_cc_to_observer')->isValid($data) &&
        isset($data->add_cc_to_observer) &&
        $data->add_cc_to_observer == 'on'
    )
    {
      $this->add_cc_to_observer = true;
    } else {
      $this->add_cc_to_observer = false;
    }

    if (
        Validation::attrStr('collect_only_unread')->isValid($data) &&
        isset($data->collect_only_unread) &&
        $data->collect_only_unread == 'on'
    )
    {
      $this->collect_only_unread = true;
    } else {
      $this->collect_only_unread = false;
    }

    if (
        Validation::attrStrNotempty('comment')->isValid($data) &&
        isset($data->comment)
    )
    {
      $this->comment = $data->comment;
    }

    if (
        Validation::attrStr('is_oauth')->isValid($data) &&
        isset($data->is_oauth) &&
        $data->is_oauth == 'on'
    )
    {
      $this->is_oauth = true;
    } else {
      $this->is_oauth = false;
    }

    if (
        Validation::attrStrNotempty('oauth_provider')->isValid($data) &&
        isset($data->oauth_provider)
    )
    {
      $this->oauth_provider = $data->oauth_provider;
    }

    if (
        Validation::attrStrNotempty('oauth_applicationid')->isValid($data) &&
        isset($data->oauth_applicationid)
    )
    {
      $this->oauth_applicationid = $data->oauth_applicationid;
    }

    if (
        Validation::attrStrNotempty('oauth_directoryid')->isValid($data) &&
        isset($data->oauth_directoryid)
    )
    {
      $this->oauth_directoryid = $data->oauth_directoryid;
    }

    if (
        Validation::attrStrNotempty('oauth_applicationsecret')->isValid($data) &&
        isset($data->oauth_applicationsecret)
    )
    {
      $this->oauth_applicationsecret = $data->oauth_applicationsecret;
    }
  }

  /**
   * @return array{name?: string, is_active?: bool, accepted?: string, refused?: string, use_mail_date?: bool,
   *               requester_field?: bool, add_cc_to_observer?: bool, collect_only_unread?: bool, comment?: string,
   *               is_oauth?: bool, oauth_provider?: string, oauth_applicationid?: string,
   *               oauth_directoryid?: string, oauth_applicationsecret?: string}
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
   * @param-out array{name?: string, is_active?: bool, accepted?: string, refused?: string, use_mail_date?: bool,
   *                  requester_field?: bool, add_cc_to_observer?: bool, collect_only_unread?: bool, comment?: string,
   *                  is_oauth?: bool, oauth_provider?: string, oauth_applicationid?: string,
   *                  oauth_directoryid?: string, oauth_applicationsecret?: string} $data
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
