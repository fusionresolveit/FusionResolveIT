<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostUser extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $lastname;

  /** @var ?string */
  public $firstname;

  /** @var ?bool */
  public $is_active;

  /** @var ?string */
  public $begin_date;

  /** @var ?string */
  public $end_date;

  /** @var ?string */
  public $phone;

  /** @var ?string */
  public $phone2;

  /** @var ?string */
  public $mobile;

  /** @var ?string */
  public $registration_number;

  /** @var ?\App\Models\Usercategory */
  public $category;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\Usertitle */
  public $title;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\Profile */
  public $profile;

  /** @var ?\App\Models\Group */
  public $defaultgroup;

  /** @var ?\App\Models\Entity */
  public $entity;

  /** @var ?\App\Models\User */
  public $supervisor;

  /** @var ?string */
  public $user_dn;

  /** @var ?bool */
  public $is_deleted_ldap;

  /** @var ?string */
  public $personal_token;

  /** @var ?string */
  public $api_token;

  /** @var ?string */
  public $sync_field;

  /** @var ?string */
  public $synchronized_at;

  /** @var ?string */
  public $last_login;

  /** @var ?string */
  public $new_password;

  /** @var ?string */
  public $new_password_verification;

  /** @var ?\App\Models\Authsso */
  public $authsso;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\User');
    $user = new \App\Models\User();
    $this->definitions = $user->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('lastname')->isValid($data) &&
        isset($data->lastname)
    )
    {
      $this->lastname = $data->lastname;
    }

    if (
        Validation::attrStrNotempty('firstname')->isValid($data) &&
        isset($data->firstname)
    )
    {
      $this->firstname = $data->firstname;
    }

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
        Validation::attrDate('begin_date')->isValid($data) &&
        isset($data->begin_date)
    )
    {
      $this->begin_date = $data->begin_date;
    }

    if (
        Validation::attrDate('end_date')->isValid($data) &&
        isset($data->end_date)
    )
    {
      $this->end_date = $data->end_date;
    }

    if (
        Validation::attrStrNotempty('phone')->isValid($data) &&
        isset($data->phone)
    )
    {
      $this->phone = $data->phone;
    }

    if (
        Validation::attrStrNotempty('phone2')->isValid($data) &&
        isset($data->phone2)
    )
    {
      $this->phone2 = $data->phone2;
    }

    if (
        Validation::attrStrNotempty('mobile')->isValid($data) &&
        isset($data->mobile)
    )
    {
      $this->mobile = $data->mobile;
    }

    if (
        Validation::attrStrNotempty('registration_number')->isValid($data) &&
        isset($data->registration_number)
    )
    {
      $this->registration_number = $data->registration_number;
    }

    if (
        Validation::attrNumericVal('category')->isValid($data) &&
        isset($data->category)
    )
    {
      $category = \App\Models\Usercategory::where('id', $data->category)->first();
      if (!is_null($category))
      {
        $this->category = $category;
      }
      elseif (intval($data->category) == 0)
      {
        $emptyCategory = new \App\Models\Usercategory();
        $emptyCategory->id = 0;
        $this->category = $emptyCategory;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    if (
        Validation::attrNumericVal('title')->isValid($data) &&
        isset($data->title)
    )
    {
      $title = \App\Models\Usertitle::where('id', $data->title)->first();
      if (!is_null($title))
      {
        $this->title = $title;
      }
      elseif (intval($data->title) == 0)
      {
        $emptyTitle = new \App\Models\Usertitle();
        $emptyTitle->id = 0;
        $this->title = $emptyTitle;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->location = $this->setLocation($data);

    if (
        Validation::attrNumericVal('profile')->isValid($data) &&
        isset($data->profile)
    )
    {
      $profile = \App\Models\Profile::where('id', $data->profile)->first();
      if (!is_null($profile))
      {
        $this->profile = $profile;
      }
      elseif (intval($data->profile) == 0)
      {
        $emptyProfile = new \App\Models\Profile();
        $emptyProfile->id = 0;
        $this->profile = $emptyProfile;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('defaultgroup')->isValid($data) &&
        isset($data->defaultgroup)
    )
    {
      $defaultgroup = \App\Models\Group::where('id', $data->defaultgroup)->first();
      if (!is_null($defaultgroup))
      {
        $this->defaultgroup = $defaultgroup;
      }
      elseif (intval($data->defaultgroup) == 0)
      {
        $emptyGroup = new \App\Models\Group();
        $emptyGroup->id = 0;
        $this->defaultgroup = $emptyGroup;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->entity = $this->setEntity($data);

    if (
        Validation::attrNumericVal('supervisor')->isValid($data) &&
        isset($data->supervisor)
    )
    {
      $supervisor = \App\Models\User::where('id', $data->supervisor)->first();
      if (!is_null($supervisor))
      {
        $this->supervisor = $supervisor;
      }
      elseif (intval($data->supervisor) == 0)
      {
        $emptyUser = new \App\Models\User();
        $emptyUser->id = 0;
        $this->supervisor = $emptyUser;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('is_deleted_ldap')->isValid($data) &&
        isset($data->is_deleted_ldap) &&
        $data->is_deleted_ldap == 'on'
    )
    {
      $this->is_deleted_ldap = true;
    } else {
      $this->is_deleted_ldap = false;
    }

    if (
        Validation::attrStrNotempty('personal_token')->isValid($data) &&
        isset($data->personal_token)
    )
    {
      $this->personal_token = $data->personal_token;
    }

    if (
        Validation::attrStrNotempty('api_token')->isValid($data) &&
        isset($data->api_token)
    )
    {
      $this->api_token = $data->api_token;
    }

    if (
        Validation::attrStrNotempty('sync_field')->isValid($data) &&
        isset($data->sync_field)
    )
    {
      $this->sync_field = $data->sync_field;
    }

    if (
        Validation::attrDate('synchronized_at')->isValid($data) &&
        isset($data->synchronized_at)
    )
    {
      $this->synchronized_at = $data->synchronized_at;
    }

    if (
        Validation::attrDate('last_login')->isValid($data) &&
        isset($data->last_login)
    )
    {
      $this->last_login = $data->last_login;
    }

    if (
        Validation::attrStrNotempty('new_password')->isValid($data) &&
        isset($data->new_password)
    )
    {
      $this->new_password = $data->new_password;
    }

    if (
        Validation::attrStrNotempty('new_password_verification')->isValid($data) &&
        isset($data->new_password_verification)
    )
    {
      $this->new_password_verification = $data->new_password_verification;
    }

    if (
        Validation::attrNumericVal('authsso')->isValid($data) &&
        isset($data->authsso)
    )
    {
      $authsso = \App\Models\Authsso::where('id', $data->authsso)->first();
      if (!is_null($authsso))
      {
        $this->authsso = $authsso;
        $this->filledFields[] = 'authsso';
      }
      elseif (intval($data->authsso) == 0)
      {
        $emptyAuthsso = new \App\Models\Authsso();
        $emptyAuthsso->id = 0;
        $this->authsso = $emptyAuthsso;
        $this->filledFields[] = 'authsso';
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
  }

  /**
   * @return array{name?: string, lastname?: string, firstname?: string, is_active?: bool, begin_date?: string,
   *               end_date?: string, phone?: string, phone2?: string, mobile?: string,
   *               registration_number?: string, category?: \App\Models\Usercategory, comment?: string,
   *               title?: \App\Models\Usertitle, location?: \App\Models\Location, profile?: \App\Models\Profile,
   *               defaultgroup?: \App\Models\Group, entity?: \App\Models\Entity, supervisor?: \App\Models\User,
   *               user_dn?: string, is_deleted_ldap?: bool, personal_token?: string, api_token?: string,
   *               sync_field?: string, synchronized_at?: string, last_login?: string, new_password?: string,
   *               new_password_verification?: string, authsso?: \App\Models\Authsso}
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
   * @param-out array{name?: string, lastname?: string, firstname?: string, is_active?: bool, begin_date?: string,
   *                  end_date?: string, phone?: string, phone2?: string, mobile?: string,
   *                  registration_number?: string, category?: \App\Models\Usercategory, comment?: string,
   *                  title?: \App\Models\Usertitle, location?: \App\Models\Location, profile?: \App\Models\Profile,
   *                  defaultgroup?: \App\Models\Group, entity?: \App\Models\Entity, supervisor?: \App\Models\User,
   *                  user_dn?: string, is_deleted_ldap?: bool, personal_token?: string, api_token?: string,
   *                  sync_field?: string, synchronized_at?: string, last_login?: string, new_password?: string,
   *                  new_password_verification?: string, authsso?: \App\Models\Authsso} $data
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

  /**
   * Used in special case, for example when run rules when connect (not
   * yet connected but must have all definitions for rules)
   */
  public function forceAllDefinitions(): void
  {
    $user = new \App\Models\User();
    $this->definitions = $user->getDefinitions(true);
  }
}
