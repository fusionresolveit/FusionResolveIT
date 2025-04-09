<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostFormQuestion extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $comment;

  /** @var 'checkbox'|'radio'|'hidden'|'email'|'date'|'description'|'integer'|'file'|'float'|'time'|'dropdown'|'glpiselect'|'select'|'multiselect'|'text'|'urgency'|'textarea' */
  public $fieldtype;

  /** @var ?bool */
  public $is_required;

  /** @var ?bool */
  public $show_empty;

  /** @var ?string */
  public $default_values;

  /** @var ?string */
  public $values;

  /** @var ?integer */
  public $width;

  /** @var ?integer */
  public $range_min;

  /** @var ?integer */
  public $range_max;

  /** @var ?string */
  public $regex;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Form\Question');
    $question = new \App\Models\Forms\Question();
    $this->definitions = $question->getDefinitions();

    $this->name = $this->setName($data);

    $this->comment = $this->setComment($data);

    $valuesAllowed = [
      'checkbox',
      'radio',
      'hidden',
      'email',
      'date',
      'description',
      'integer',
      'file',
      'float',
      'time',
      'dropdown',
      'glpiselect',
      'select',
      'multiselect',
      'text',
      'urgency',
      'textarea',
    ];
    if (
        Validation::attrStr('fieldtype')->isValid($data) &&
        isset($data->fieldtype) &&
        Validation::strInArray($valuesAllowed)->isValid($data->fieldtype)
    )
    {
      $this->fieldtype = $data->fieldtype;
    }

    if (
        Validation::attrStr('is_required')->isValid($data) &&
        isset($data->is_required) &&
        $data->is_required == 'on'
    )
    {
      $this->is_required = true;
    } else {
      $this->is_required = false;
    }

    if (
        Validation::attrStr('show_empty')->isValid($data) &&
        isset($data->show_empty) &&
        $data->show_empty == 'on'
    )
    {
      $this->show_empty = true;
    } else {
      $this->show_empty = false;
    }

    if (
        Validation::attrStr('default_values')->isValid($data) &&
        isset($data->default_values)
    )
    {
      $this->default_values = $data->default_values;
    }

    if (
        Validation::attrStr('values')->isValid($data) &&
        isset($data->values)
    )
    {
      $this->values = $data->values;
    }

    if (
        Validation::attrNumericVal('width')->isValid($data) &&
        isset($data->width)
    )
    {
      $this->width = intval($data->width);
    }

    if (
        Validation::attrNumericVal('range_min')->isValid($data) &&
        isset($data->range_min)
    )
    {
      $this->range_min = intval($data->range_min);
    } else {
      $this->range_min = null;
    }

    if (
        Validation::attrNumericVal('range_max')->isValid($data) &&
        isset($data->range_max)
    )
    {
      $this->range_max = intval($data->range_max);
    } else {
      $this->range_max = null;
    }

    if (
        Validation::attrStr('regex')->isValid($data) &&
        isset($data->regex)
    )
    {
      $this->regex = $data->regex;
    } else {
      $this->regex = null;
    }
  }

  /**
   * @return array{name?: string, comment?: string, fieldtype?: 'checkbox'|'radio'|'hidden'|'email'|'date'|'description'|'integer'|'file'|'float'|'time'|'dropdown'|'glpiselect'|'select'|'multiselect'|'text'|'urgency'|'textarea',
   *               is_required?: bool, show_empty?: bool, default_values?: string, values?: string,
   *               width?: integer, range_min?: integer|null, range_max?: integer|null, regex?: string|null}
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
   * @param-out array{name?: string, comment?: string, fieldtype?: 'checkbox'|'radio'|'hidden'|'email'|'date'|'description'|'integer'|'file'|'float'|'time'|'dropdown'|'glpiselect'|'select'|'multiselect'|'text'|'urgency'|'textarea',
   *                  is_required?: bool, show_empty?: bool, default_values?: string, values?: string,
   *                  width?: integer, range_min?: integer|null, range_max?: integer|null, regex?: string|null} $data
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
