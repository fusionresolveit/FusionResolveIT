<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

abstract class Post
{
  /** @var array<string> */
  protected $filledFields;

  /** @var ?\App\Models\Profileright */
  protected $profileright;

  /** @var array<\App\Models\Profilerightcustom> */
  protected $profilerightcustoms;

  /** @var DefinitionCollection */
  protected $definitions;

  protected function setName(object $data): string|null
  {
    if (
        Validation::attrStr('name')->isValid($data) &&
        isset($data->name)
    )
    {
      $this->filledFields[] = 'name';
      return $data->name;
    }
    return null;
  }

  protected function setComment(object $data): string|null
  {
    if (
        Validation::attrStr('comment')->isValid($data) &&
        isset($data->comment)
    )
    {
      $this->filledFields[] = 'comment';
      return $data->comment;
    }
    return null;
  }

  protected function setEntity(object $data): \App\Models\Entity|null
  {
    if (
        Validation::attrNumericVal('entity')->isValid($data) &&
        isset($data->entity)
    )
    {
      $entity = \App\Models\Entity::where('id', $data->entity)->first();
      if (!is_null($entity))
      {
        $this->filledFields[] = 'entity';
        return $entity;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setLocation(object $data): \App\Models\Location|null
  {
    if (
        Validation::attrNumericVal('location')->isValid($data) &&
        isset($data->location)
    )
    {
      $location = \App\Models\Location::where('id', $data->location)->first();
      if (!is_null($location))
      {
        $this->filledFields[] = 'location';
        return $location;
      }
      elseif (intval($data->location) == 0)
      {
        $emptyLocation = new \App\Models\Location();
        $emptyLocation->id = 0;
        $this->filledFields[] = 'location';
        return $emptyLocation;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setState(object $data): \App\Models\State|null
  {
    if (
        Validation::attrNumericVal('state')->isValid($data) &&
        isset($data->state)
    )
    {
      $state = \App\Models\State::where('id', $data->state)->first();
      if (!is_null($state))
      {
        $this->filledFields[] = 'state';
        return $state;
      }
      elseif (intval($data->state) == 0)
      {
        $emptyState = new \App\Models\State();
        $emptyState->id = 0;
        $this->filledFields[] = 'state';
        return $emptyState;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setManufacturer(object $data): \App\Models\Manufacturer|null
  {
    if (
        Validation::attrNumericVal('manufacturer')->isValid($data) &&
        isset($data->manufacturer)
    )
    {
      $manufacturer = \App\Models\Manufacturer::where('id', $data->manufacturer)->first();
      if (!is_null($manufacturer))
      {
        $this->filledFields[] = 'manufacturer';
        return $manufacturer;
      }
      elseif (intval($data->manufacturer) == 0)
      {
        $emptyManufacturer = new \App\Models\Manufacturer();
        $emptyManufacturer->id = 0;
        $this->filledFields[] = 'manufacturer';
        return $emptyManufacturer;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setUser(object $data): \App\Models\User|null
  {
    if (
        Validation::attrNumericVal('user')->isValid($data) &&
        isset($data->user)
    )
    {
      $user = \App\Models\User::where('id', $data->user)->first();
      if (!is_null($user))
      {
        $this->filledFields[] = 'user';
        return $user;
      }
      elseif (intval($data->user) == 0)
      {
        $emptyUser = new \App\Models\User();
        $emptyUser->id = 0;
        $this->filledFields[] = 'user';
        return $emptyUser;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setGroup(object $data): \App\Models\Group|null
  {
    if (
        Validation::attrNumericVal('group')->isValid($data) &&
        isset($data->group)
    )
    {
      $group = \App\Models\Group::where('id', $data->group)->first();
      if (!is_null($group))
      {
        $this->filledFields[] = 'group';
        return $group;
      }
      elseif (intval($data->group) == 0)
      {
        $emptyGroup = new \App\Models\Group();
        $emptyGroup->id = 0;
        $this->filledFields[] = 'group';
        return $emptyGroup;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setUsertech(object $data): \App\Models\User|null
  {
    if (
        Validation::attrNumericVal('usertech')->isValid($data) &&
        isset($data->usertech)
    )
    {
      $usertech = \App\Models\User::where('id', $data->usertech)->first();
      if (!is_null($usertech))
      {
        $this->filledFields[] = 'usertech';
        return $usertech;
      }
      elseif (intval($data->usertech) == 0)
      {
        $emptyUsertech = new \App\Models\User();
        $emptyUsertech->id = 0;
        $this->filledFields[] = 'usertech';
        return $emptyUsertech;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setGrouptech(object $data): \App\Models\Group|null
  {
    if (
        Validation::attrNumericVal('grouptech')->isValid($data) &&
        isset($data->grouptech)
    )
    {
      $grouptech = \App\Models\Group::where('id', $data->grouptech)->first();
      if (!is_null($grouptech))
      {
        $this->filledFields[] = 'grouptech';
        return $grouptech;
      }
      elseif (intval($data->grouptech) == 0)
      {
        $emptyGrouptech = new \App\Models\Group();
        $emptyGrouptech->id = 0;
        $this->filledFields[] = 'grouptech';
        return $emptyGrouptech;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setSerial(object $data): string|null
  {
    if (
        Validation::attrStr('serial')->isValid($data) &&
        isset($data->serial)
    )
    {
      $this->filledFields[] = 'serial';
      return $data->serial;
    }
    return null;
  }

  protected function setOtherserial(object $data): string|null
  {
    if (
        Validation::attrStr('otherserial')->isValid($data) &&
        isset($data->otherserial)
    )
    {
      $this->filledFields[] = 'otherserial';
      return $data->otherserial;
    }
    return null;
  }

  protected function setIsrecursive(object $data): bool
  {
    if (
        Validation::attrStr('is_recursive')->isValid($data) &&
        isset($data->is_recursive) &&
        $data->is_recursive == 'on'
    )
    {
      $this->filledFields[] = 'is_recursive';
      return true;
    }
    $this->filledFields[] = 'is_recursive';
    return false;
  }

  protected function setCategory(object $data): \App\Models\Category|null
  {
    if (
        Validation::attrNumericVal('category')->isValid($data) &&
        isset($data->category)
    )
    {
      $category = \App\Models\Category::where('id', $data->category)->first();
      if (!is_null($category))
      {
        $this->filledFields[] = 'category';
        return $category;
      }
      elseif (intval($data->category) == 0)
      {
        $emptyCategory = new \App\Models\Category();
        $emptyCategory->id = 0;
        $this->filledFields[] = 'category';
        return $emptyCategory;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
    return null;
  }

  protected function setProductnumber(object $data): string|null
  {
    if (
        Validation::attrStrNotempty('product_number')->isValid($data) &&
        isset($data->product_number)
    )
    {
      $this->filledFields[] = 'product_number';
      return $data->product_number;
    }
    return null;
  }

  protected function loadRights(string $model): void
  {
    $this->profileright = \App\Models\Profileright::where('profile_id', $GLOBALS['profile_id'])
      ->where('model', $model)
      ->first();

    if (!is_null($this->profileright))
    {
      if ($this->profileright->custom)
      {
        $customs = \App\Models\Profilerightcustom::where('profileright_id', $this->profileright->id)->get();
        foreach ($customs as $custom)
        {
          $this->profilerightcustoms[] = $custom;
        }
      }
    }
  }

  /**
   * @return array<string>
   */
  public function getFilledFields(): array
  {
    return $this->filledFields;
  }
}
