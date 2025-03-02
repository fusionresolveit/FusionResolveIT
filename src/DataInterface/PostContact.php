<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostContact extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $firstname;

  /** @var ?string */
  public $phone;

  /** @var ?string */
  public $phone2;

  /** @var ?string */
  public $mobile;

  /** @var ?string */
  public $fax;

  /** @var ?string */
  public $email;

  /** @var ?string */
  public $address;

  /** @var ?string */
  public $postcode;

  /** @var ?string */
  public $town;

  /** @var ?string */
  public $state;

  /** @var ?string */
  public $country;

  /** @var ?\App\Models\Contacttype */
  public $type;

  /** @var ?\App\Models\Usertitle */
  public $title;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Contact');
    $contact = new \App\Models\Contact();
    $this->definitions = $contact->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('firstname')->isValid($data) &&
        isset($data->firstname)
    )
    {
      $this->firstname = $data->firstname;
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
        Validation::attrStrNotempty('fax')->isValid($data) &&
        isset($data->fax)
    )
    {
      $this->fax = $data->fax;
    }

    if (
        Validation::attrStrNotempty('email')->isValid($data) &&
        isset($data->email)
    )
    {
      $this->email = $data->email;
    }

    if (
        Validation::attrStrNotempty('address')->isValid($data) &&
        isset($data->address)
    )
    {
      $this->address = $data->address;
    }

    if (
        Validation::attrStrNotempty('postcode')->isValid($data) &&
        isset($data->postcode)
    )
    {
      $this->postcode = $data->postcode;
    }

    if (
        Validation::attrStrNotempty('town')->isValid($data) &&
        isset($data->town)
    )
    {
      $this->town = $data->town;
    }

    if (
        Validation::attrStrNotempty('state')->isValid($data) &&
        isset($data->state)
    )
    {
      $this->state = $data->state;
    }

    if (
        Validation::attrStrNotempty('country')->isValid($data) &&
        isset($data->country)
    )
    {
      $this->country = $data->country;
    }

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Contacttype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type =  $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Contacttype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('title')->isValid($data) &&
        isset($data->title)
    )
    {
      $title = \App\Models\Usertitle::where('id', $data->title)->first();
      if (!is_null($title))
      {
        $this->title =  $title;
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

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, firstname?: string, phone?: string, phone2?: string, mobile?: string, fax?: string,
   *               email?: string, address?: string, postcode?: string, town?: string, state?: string,
   *               country?: string, type?: \App\Models\Contacttype, title?: \App\Models\Usertitle,
   *               comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, firstname?: string, phone?: string, phone2?: string, mobile?: string, fax?: string,
   *                  email?: string, address?: string, postcode?: string, town?: string, state?: string,
   *                  country?: string, type?: \App\Models\Contacttype, title?: \App\Models\Usertitle,
   *                  comment?: string, is_recursive?: bool} $data
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
