<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;

final class Criterium
{
  /** @var array<string> */
  protected $regexResults = [];

  // TODO rename in criterion

  /**
   * @template D of \App\DataInterface\Post
   * @param D $data
   */
  public function checkCriteria(\App\Models\Rules\Rulecriterium $crit, $data): bool
  {
    $criterium = new self();
    // separate condition on multiple groups
    // group 1: check id (is, is not, exists, not exists, empty, under, not under)
    // group 2: check value (contain, not contain, begin, end, regex*)
    // group 3: (find)

    if (!isset($crit->criteria->name))
    {
      return false;
    }

    switch ($crit->condition)
    {
      case 8: // pattern exists
        if (isset($data->{$crit->criteria->name}))
        {
          return true;
        }
          return false;

      case 9: // pattern does not exists
        if (!isset($data->{$crit->criteria->name}))
        {
          return true;
        }
          return false;
    }

    if (!isset($data->{$crit->criteria->name}))
    {
      return false;
    }

    $inputVal = $data->{$crit->criteria->name};
    $patternId = null;
    if ($crit->criteria->type == 'boolean')
    {
      if (is_bool($crit->pattern) === true)
      {
        $patternId = $crit->pattern;
      } else {
        $patternId = boolval($crit->pattern);
      }
    }
    elseif (
        ($crit->criteria->type == 'dropdown' || $crit->criteria->type == 'dropdown_remote') &&
        is_array($crit->pattern)
    )
    {
      $patternId = $crit->pattern['id'];
    }

    // TODO
    // prepare data and try to use generic code for conditions
    // $inputVal = value(s) from input
    // $pattern = pattern to check (from DB)


    // Check group based on id
    switch ($crit->condition)
    {
      case 0: // pattern is
          return $this->patternIs($inputVal, $patternId);

      case 1: // pattern is not
          return $this->patternIsNot($inputVal, $patternId);

      case 30: // pattern is empty
        if (empty($data->{$crit->criteria->name}))
        {
          return true;
        }
          return false;

      case 11: // pattern under
        if (!is_null($crit->criteria->itemtype))
        {
          return $this->patternUnder($crit->criteria->itemtype, $inputVal, $patternId);
        }
          return false;

      case 12: // pattern not under
        if (!is_null($crit->criteria->itemtype))
        {
          return $this->patternNotUnder($crit->criteria->itemtype, $inputVal, $patternId);
        }
          return false;
    }

    $patternValue = '';
    $modelName = null;
    // if (isset($crit->patternviewfield['valuename']))
    // {
    //   $patternValue = $crit->patternviewfield['value'];
    // } else {
    if (is_array($crit->pattern))
    {
      $patternValue = $crit->pattern['value'];
    } else {
      $patternValue = $crit->pattern;
    }
      // $patternValue = $crit->pattern;
    // }
    if (isset($crit->criteria->itemtype))
    {
      $modelName = $crit->criteria->itemtype;
      if (is_array($inputVal))
      {
        $tmpInputVal = [];
        foreach ($inputVal as $id)
        {
          $item = $modelName::where('id', (int) $id)->first();
          if (!is_null($item))
          {
            $tmpInputVal[] = $item->name;
          }
        }
        $inputVal = $tmpInputVal;
      } else {
        $item = $modelName::where('id', $inputVal)->first();
        if (is_null($item))
        {
          $inputVal = null;
        } else {
          $inputVal = $item->name;
        }
      }
    }
    elseif (count($crit->criteria->values) > 0)
    {
      if (isset($crit->criteria->values[$inputVal]))
      {
        $inputVal = $crit->criteria->values[$inputVal]['title'];
      } else {
        $inputVal = null;
      }
    }

    switch ($crit->condition)
    {
      case 2: // pattern contain
          return $this->patternContain($inputVal, $patternValue);

      case 3: // Pattern not contain
          return $this->patternNotContain($inputVal, $patternValue);

      case 4: // Pattern begin
          return $this->patternBegin($inputVal, $patternValue);

      case 5: // Pattern end
          return $this->patternEnd($inputVal, $patternValue);

      case 6: // regex match
          return $this->patternRegexMatch($inputVal, $patternValue);

      case 7: // regex not match
          return $this->patternRegexNotMatch($inputVal, $patternValue);
    }
    return false;

    // TODO regex result
  }

  /**
   * @return array<mixed>
   */
  public static function getConditionForCriterium(Definition $criteria, int $condition): array
  {
    $values = \App\Models\Definitions\Rulecriterium::getConditionArray();
    if ($criteria->type != 'dropdown' && $criteria->type != 'dropdown_remote')
    {
      unset($values[0]);
      unset($values[1]);
    }

    return [
      'title'   => pgettext('rule', 'Condition'),
      'type'    => 'dropdown',
      'name'    => 'ondition',
      'values'  => $values,
      'value'   => $condition,
      'valuename' => $values[$condition]['title'],
    ];
  }

  /**
   * Check if the value(s) is same than the pattern (check the id)
   * @param mixed  $value         the value(s)
   * @param mixed  $patternValue  the value to have into value(s)
   *
   * @return boolean
   */
  private function patternIs($value, $patternValue): bool
  {
    if (is_null($value) || is_null($patternValue))
    {
      return false;
    }
    if (is_bool($value) && is_bool($patternValue))
    {
      if ($patternValue === $value)
      {
        return true;
      }
    }
    elseif (is_array($value))
    {
      if (in_array($patternValue, $value, true))
      {
        return true;
      }
    }
    elseif (is_object($value) && method_exists($value, 'getAttribute'))
    {
      if ($value->getAttribute('id') == $patternValue)
      {
        return true;
      }
    } else {
      if ($patternValue === $value)
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Check if the value(s) is same than the pattern (check the id)
   * @param mixed  $value         the value(s)
   * @param mixed  $patternValue  the value to have into value(s)
   *
   * @return boolean
   */
  private function patternIsNot($value, $patternValue): bool
  {
    if (is_null($value) || is_null($patternValue))
    {
      return true;
    }
    if (is_array($value))
    {
      if (!in_array($patternValue, $value, true))
      {
        return true;
      }
    }
    elseif (is_object($value) && method_exists($value, 'getAttribute'))
    {
      if ($value->getAttribute('id') != $patternValue)
      {
        return true;
      }
    } else {
      if ($patternValue !== $value)
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Check if the value(s) has the pattern
   * @param mixed  $value        the value(s)
   * @param mixed  $patternValue the value to have into value(s)
   *
   * @return boolean
   */
  private function patternContain($value, $patternValue): bool
  {
    if (is_null($value) || is_null($patternValue))
    {
      return false;
    }
    if (is_array($value))
    {
      foreach ($value as $val)
      {
        if (strstr(strtolower($val), strtolower($patternValue)))
        {
          return true;
        }
      }
    } else {
      if (strstr(strtolower($value), strtolower($patternValue)))
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Check if the value(s) hasn't the pattern
   * @param mixed   $value        the value(s)
   * @param string  $patternValue the value to not have into value(s)
   *
   * @return boolean
   */
  private function patternNotContain($value, $patternValue): bool
  {
    if (is_null($value))
    {
      return true;
    }
    if (is_array($value))
    {
      foreach ($value as $val)
      {
        $found = false;
        if (strstr(strtolower($val), strtolower($patternValue)))
        {
          $found = true;
        }
        return !$found;
      }
    } else {
      if (!strstr(strtolower($value), strtolower($patternValue)))
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Check if the value(s) begin with the pattern
   * @param mixed   $value        the value(s)
   * @param string  $patternValue the value at the beginning ot the value(s)
   *
   * @return boolean
   */
  private function patternBegin($value, $patternValue): bool
  {
    if (is_null($value))
    {
      return false;
    }
    if (is_array($value))
    {
      foreach ($value as $val)
      {
        if (str_starts_with(strtolower($val), strtolower($patternValue)))
        {
          return true;
        }
      }
    } else {
      if (str_starts_with(strtolower($value), strtolower($patternValue)))
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Check if the value(s) end with the pattern
   * @param mixed   $value        the value(s)
   * @param string  $patternValue the value at the end ot the value(s)
   *
   * @return boolean
   */
  private function patternEnd($value, $patternValue): bool
  {
    if (is_null($value))
    {
      return false;
    }
    if (is_array($value))
    {
      foreach ($value as $val)
      {
        if (str_ends_with(strtolower($val), strtolower($patternValue)))
        {
          return true;
        }
      }
    } else {
      if (str_ends_with(strtolower($value), strtolower($patternValue)))
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Check if the value(s) match the regex (pattern)
   * @param mixed   $value        the value(s)
   * @param string  $patternValue the pattern of the regex
   *
   * @return boolean
   */
  private function patternRegexMatch($value, $patternValue): bool
  {
    $matches = [];
    if (is_null($value))
    {
      return false;
    }
    if (is_array($value))
    {
      foreach ($value as $val)
      {
        if (preg_match("/" . $patternValue . "/", $val, $matches))
        {
          $this->parseRegexMatchesValues($matches);
          return true;
        }
      }
    } else {
      if (preg_match("/" . $patternValue . "/", $value, $matches))
      {
        $this->parseRegexMatchesValues($matches);
        return true;
      }
    }
    return false;
  }

  /**
   * Check if the value(s) not match the regex (pattern)
   * @param mixed   $value        the value(s)
   * @param string  $patternValue the pattern of the regex
   *
   * @return boolean
   */
  private function patternRegexNotMatch($value, $patternValue): bool
  {
    if (is_null($value))
    {
      return true;
    }
    if (is_array($value))
    {
      $found = false;
      foreach ($value as $val)
      {
        if (preg_match("/" . $patternValue . "/", $val))
        {
          $found = true;
        }
      }
      return !$found;
    } else {
      if (!preg_match("/" . $patternValue . "/", $value))
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Check if the value(s) is under pattern id (tree item)
   * @param string        $modelName  the class name of item
   * @param mixed         $value      the value(s)
   * @param string|null   $patternId  the parent pattern id
   *
   * @return boolean
   */
  private function patternUnder($modelName, $value, $patternId): bool
  {
    if (is_null($value) || is_null($patternId))
    {
      return false;
    }

    // check the id exists
    $parent = $modelName::where('id', $patternId)->first();
    if (is_null($parent))
    {
      return false;
    }
    if (!$parent->isTree())
    {
      return false;
    }

    if (is_array($value))
    {
      foreach ($value as $valId)
      {
        $item = $modelName::where('id', $valId)->first();
        if (is_null($item))
        {
          continue;
        }
        $itemsId = str_split($item->treepath, 5);
        foreach ($itemsId as $val)
        {
          if ((int) $val == (int) $patternId)
          {
            return true;
          }
        }
      }
    } else {
      $item = $modelName::where('id', $value)->first();
      if (is_null($item))
      {
        return false;
      }
      $itemsId = str_split($item->treepath, 5);
      foreach ($itemsId as $val)
      {
        if ((int) $val == (int) $patternId)
        {
          return true;
        }
      }
    }
    return false;
  }


  /**
   * Check if the value(s) is not under pattern id (tree item)
   * @param string        $modelName  the class name of item
   * @param mixed         $value      the value(s)
   * @param string|null   $patternId  the parent pattern id
   *
   * @return boolean
   */
  private function patternNotUnder($modelName, $value, $patternId): bool
  {
    if (is_null($value) || is_null($patternId))
    {
      return true;
    }

    // check the id exists
    $parent = $modelName::where('id', $patternId)->first();
    if (is_null($parent))
    {
      return false;
    }
    if (!$parent->isTree())
    {
      return false;
    }

    if (is_array($value))
    {
      $under = false;
      foreach ($value as $valId)
      {
        $item = $modelName::where('id', $valId)->first();
        if (is_null($item))
        {
          continue;
        }
        $itemsId = str_split($item->treepath, 5);
        foreach ($itemsId as $val)
        {
          if ((int) $val == (int) $patternId)
          {
            $under = true;
          }
        }
      }
      return !$under;
    } else {
      $item = $modelName::where('id', $value)->first();
      if (is_null($item))
      {
        return true;
      }
      $itemsId = str_split($item->treepath, 5);
      foreach ($itemsId as $value)
      {
        if ((int) $value == (int) $patternId)
        {
          return false;
        }
      }
    }
    return true;
  }

  /**
   * @param array<string> $data
   */
  private function parseRegexMatchesValues(array $data): void
  {
    // remove the first element (because it's the complete value)
    array_shift($data);
    foreach ($data as $value)
    {
      $this->regexResults[] = $value;
    }
  }

  /**
   * @return array<string>
   */
  public function getRegexResults(): array
  {
    return $this->regexResults;
  }

  /**
   * @return array<int>
   */
  public static function getConditionsForDefinition(string $model, string $name): array
  {
    $spl = explode('::::', $name);
    $name = $spl[0];

    $completeModelName = '\App\Models\\Rules\\' . $model;
    if (!class_exists($completeModelName))
    {
      return [];
    }
    $item = new $completeModelName();
    if (!is_subclass_of($item, \App\Models\Common::class))
    {
      return [];
    }

    if (!property_exists($item, 'definitionCriteria') || is_null($item->definitionCriteria))
    {
      throw new \Exception('Error', 500);
    }
    $definitions = $item->definitionCriteria::getDefinition();

    $type = null;
    $typeModelTree = false;
    foreach ($definitions as $definition)
    {
      if ($definition->name == $name)
      {
        $type = $definition->type;
        if (!is_null($definition->itemtype))
        {
          $typeModel = $definition->itemtype;
          $defItem = new $typeModel();
          if (!is_subclass_of($defItem, \App\Models\Common::class))
          {
            continue;
          }
          $typeModelTree = $defItem->isTree();
        }
        break;
      }
    }

    if (is_null($type))
    {
      return [];
    }

    $conditions = [];
    switch ($type)
    {
      case 'string':
      case 'input':
      case 'textarea':
      case 'email':
      case 'date':
      case 'datetime':
        $conditions = [
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          30
        ];
          break;

      case 'boolean':
        $conditions = [
          0,
          1,
          8,
          9,
        ];
          break;

      case 'dropdown':
      case 'dropdown_remote':
        $conditions = [
          0,
          1,
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          30,
        ];
        if ($typeModelTree)
        {
          $conditions[] = 11; // Pattern under
          $conditions[] = 12; // Pattern not under
        }
          break;
    }
    return $conditions;
  }
}
