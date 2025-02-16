<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostAuthldap extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?bool */
  public $is_active;

  /** @var ?string */
  public $host;

  /** @var ?int */
  public $port;

  /** @var ?bool */
  public $is_default;

  /** @var ?string */
  public $basedn;

  /** @var ?string */
  public $rootdn;

  /** @var ?string */
  public $rootdn_passwd;

  /** @var ?string */
  public $condition;

  /** @var ?string */
  public $login_field;

  /** @var ?string */
  public $realname_field;

  /** @var ?string */
  public $firstname_field;

  /** @var ?string */
  public $phone_field;

  /** @var ?string */
  public $phone2_field;

  /** @var ?string */
  public $mobile_field;

  /** @var ?string */
  public $title_field;

  /** @var ?string */
  public $category_field;

  /** @var ?string */
  public $comment;

  /** @var ?string */
  public $email1_field;

  /** @var ?string */
  public $email2_field;

  /** @var ?string */
  public $email3_field;

  /** @var ?string */
  public $email4_field;

  /** @var ?bool */
  public $use_dn;

  /** @var ?string */
  public $language_field;

  /** @var ?string */
  public $group_field;

  /** @var ?string */
  public $group_condition;

  /** @var ?string */
  public $group_member_field;

  /** @var ?string */
  public $sync_field;

  /** @var ?string */
  public $responsible_field;

  /** @var ?string */
  public $inventory_domain;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Authldap');
    $authldap = new \App\Models\Authldap();
    $this->definitions = $authldap->getDefinitions();

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
        Validation::attrStr('host')->isValid($data) &&
        isset($data->host)
    )
    {
      $this->host = $data->host;
    }

    if (
        Validation::attrNumericVal('port')->isValid($data) &&
        isset($data->port)
    )
    {
      $this->port = intval($data->port);
    }

    if (
        Validation::attrStr('is_default')->isValid($data) &&
        isset($data->is_default) &&
        $data->is_default == 'on'
    )
    {
      $this->is_default = true;
    } else {
      $this->is_default = false;
    }

    if (
        Validation::attrStr('basedn')->isValid($data) &&
        isset($data->basedn)
    )
    {
      $this->basedn = $data->basedn;
    }

    if (
        Validation::attrStr('rootdn')->isValid($data) &&
        isset($data->rootdn)
    )
    {
      $this->rootdn = $data->rootdn;
    }

    if (
        Validation::attrStr('rootdn_passwd')->isValid($data) &&
        isset($data->rootdn_passwd)
    )
    {
      $this->rootdn_passwd = $data->rootdn_passwd;
    }

    if (
        Validation::attrStr('condition')->isValid($data) &&
        isset($data->condition)
    )
    {
      $this->condition = $data->condition;
    }

    if (
        Validation::attrStr('login_field')->isValid($data) &&
        isset($data->login_field)
    )
    {
      $this->login_field = $data->login_field;
    }

    if (
        Validation::attrStr('realname_field')->isValid($data) &&
        isset($data->realname_field)
    )
    {
      $this->realname_field = $data->realname_field;
    }

    if (
        Validation::attrStr('firstname_field')->isValid($data) &&
        isset($data->firstname_field)
    )
    {
      $this->firstname_field = $data->firstname_field;
    }

    if (
        Validation::attrStr('phone_field')->isValid($data) &&
        isset($data->phone_field)
    )
    {
      $this->phone_field = $data->phone_field;
    }

    if (
        Validation::attrStr('phone2_field')->isValid($data) &&
        isset($data->phone2_field)
    )
    {
      $this->phone2_field = $data->phone2_field;
    }

    if (
        Validation::attrStr('mobile_field')->isValid($data) &&
        isset($data->mobile_field)
    )
    {
      $this->mobile_field = $data->mobile_field;
    }

    if (
        Validation::attrStr('title_field')->isValid($data) &&
        isset($data->title_field)
    )
    {
      $this->title_field = $data->title_field;
    }

    if (
        Validation::attrStr('category_field')->isValid($data) &&
        isset($data->category_field)
    )
    {
      $this->category_field = $data->category_field;
    }

    $this->comment = $this->setComment($data);

    if (
        Validation::attrStr('email1_field')->isValid($data) &&
        isset($data->email1_field)
    )
    {
      $this->email1_field = $data->email1_field;
    }

    if (
        Validation::attrStr('email2_field')->isValid($data) &&
        isset($data->email2_field)
    )
    {
      $this->email2_field = $data->email2_field;
    }

    if (
        Validation::attrStr('email3_field')->isValid($data) &&
        isset($data->email3_field)
    )
    {
      $this->email3_field = $data->email3_field;
    }

    if (
        Validation::attrStr('email4_field')->isValid($data) &&
        isset($data->email4_field)
    )
    {
      $this->email4_field = $data->email4_field;
    }

    if (
        Validation::attrStr('use_dn')->isValid($data) &&
        isset($data->use_dn) &&
        $data->use_dn == 'on'
    )
    {
      $this->use_dn = true;
    } else {
      $this->use_dn = false;
    }

    if (
        Validation::attrStr('language_field')->isValid($data) &&
        isset($data->language_field)
    )
    {
      $this->language_field = $data->language_field;
    }

    if (
        Validation::attrStr('group_field')->isValid($data) &&
        isset($data->group_field)
    )
    {
      $this->group_field = $data->group_field;
    }

    if (
        Validation::attrStr('group_condition')->isValid($data) &&
        isset($data->group_condition)
    )
    {
      $this->group_condition = $data->group_condition;
    }

    if (
        Validation::attrStr('group_member_field')->isValid($data) &&
        isset($data->group_member_field)
    )
    {
      $this->group_member_field = $data->group_member_field;
    }

    if (
        Validation::attrStr('sync_field')->isValid($data) &&
        isset($data->sync_field)
    )
    {
      $this->sync_field = $data->sync_field;
    }

    if (
        Validation::attrStr('responsible_field')->isValid($data) &&
        isset($data->responsible_field)
    )
    {
      $this->responsible_field = $data->responsible_field;
    }

    if (
        Validation::attrStr('inventory_domain')->isValid($data) &&
        isset($data->inventory_domain)
    )
    {
      $this->inventory_domain = $data->inventory_domain;
    }
  }

  /**
   * @return array{name?: string, is_active?: bool, host?: string, port?: int, is_default?: bool, basedn?: string,
   *               rootdn?: string, rootdn_passwd?: string, condition?: string, login_field?: string,
   *               realname_field?: string, firstname_field?: string, phone_field?: string, phone2_field?: string,
   *               mobile_field?: string, title_field?: string, category_field?: string, comment?: string,
   *               email1_field?: string, email2_field?: string, email3_field?: string, email4_field?: string,
   *               use_dn?: bool, language_field?: string, group_field?: string, group_condition?: string,
   *               group_member_field?: string, sync_field?: string, responsible_field?: string,
   *               inventory_domain?: string}
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
   * @param-out array{name?: string, is_active?: bool, host?: string, port?: int, is_default?: bool, basedn?: string,
   *                  rootdn?: string, rootdn_passwd?: string, condition?: string, login_field?: string,
   *                  realname_field?: string, firstname_field?: string, phone_field?: string, phone2_field?: string,
   *                  mobile_field?: string, title_field?: string, category_field?: string, comment?: string,
   *                  email1_field?: string, email2_field?: string, email3_field?: string, email4_field?: string,
   *                  use_dn?: bool, language_field?: string, group_field?: string, group_condition?: string,
   *                  group_member_field?: string, sync_field?: string, responsible_field?: string,
   *                  inventory_domain?: string} $data
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
