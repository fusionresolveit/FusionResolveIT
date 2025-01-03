<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules;

final class Criterium
{
  protected $regexResults = [];

  // TODO rename in criterion

  public function checkCriteria(\App\Models\Rules\Rulecriterium $crit, $input)
  {
    $criterium = new self();
    // separate condition on multiple groups
    // group 1: check id (is, is not, exists, not exists, empty, under, not under)
    // group 2: check value (contain, not contain, begin, end, regex*)
    // group 3: (find)

    if (!isset($crit->criteria['name']))
    {
      return false;
    }

    switch ($crit->condition)
    {
      case Common::PATTERN_EXISTS:
        if (isset($input[$crit->criteria['name']]))
        {
          return true;
        }
          return false;

      case Common::PATTERN_DOES_NOT_EXISTS:
        if (!isset($input[$crit->criteria['name']]))
        {
          return true;
        }
          return false;
    }

    if (!isset($input[$crit->criteria['name']]))
    {
      return false;
    }

    $inputVal = $input[$crit->criteria['name']];
    $patternId = null;
    if ($crit->criteria['type'] == 'boolean')
    {
      if (is_bool($crit->pattern) === true)
      {
        $patternId = $crit->pattern;
      } else {
        $patternId = boolval($crit->pattern);
      }
    }
    elseif (
        ($crit->criteria['type'] == 'dropdown' || $crit->criteria['type'] == 'dropdown_remote') &&
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
      case Common::PATTERN_IS:
          return $this->patternIs($inputVal, $patternId);

      case Common::PATTERN_IS_NOT:
          return $this->patternIsNot($inputVal, $patternId);

      case Common::PATTERN_IS_EMPTY:
        if (empty($input[$crit->criteria['name']]))
        {
          return true;
        }
          return false;

      case Common::PATTERN_UNDER:
          return $this->patternUnder($crit->criteria['itemtype'], $inputVal, $patternId);

      case Common::PATTERN_NOT_UNDER:
          return $this->patternNotUnder($crit->criteria['itemtype'], $inputVal, $patternId);
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
    if (isset($crit->criteria['itemtype']))
    {
      $modelName = $crit->criteria['itemtype'];
      if (is_array($inputVal))
      {
        $tmpInputVal = [];
        foreach ($inputVal as $id)
        {
          $item = $modelName::find((int) $id);
          if (!is_null($item))
          {
            $tmpInputVal[] = $item->name;
          }
        }
        $inputVal = $tmpInputVal;
      } else {
        $item = $modelName::find($inputVal);
        if (is_null($item))
        {
          $inputVal = null;
        } else {
          $inputVal = $item->name;
        }
      }
    }
    elseif (isset($crit->criteria['values']))
    {
      if (isset($crit->criteria['values'][$inputVal]))
      {
        $inputVal = $crit->criteria['values'][$inputVal]['title'];
      } else {
        $inputVal = null;
      }
    }

    switch ($crit->condition)
    {
      case Common::PATTERN_CONTAIN:
          return $this->patternContain($inputVal, $patternValue);

      case Common::PATTERN_NOT_CONTAIN:
          return $this->patternNotContain($inputVal, $patternValue);

      case Common::PATTERN_BEGIN:
          return $this->patternBegin($inputVal, $patternValue);

      case Common::PATTERN_END:
          return $this->patternEnd($inputVal, $patternValue);

      case Common::REGEX_MATCH:
          return $this->patternRegexMatch($inputVal, $patternValue);

      case Common::REGEX_NOT_MATCH:
          return $this->patternRegexNotMatch($inputVal, $patternValue);
    }
    return false;

    // TODO regex result
  }

  public static function getConditionForCriterium($criteria, $condition)
  {
    global $translator;

    $values = [];
    if ($criteria['type'] == 'dropdown' || $criteria['type'] == 'dropdown_remote')
    {
      $values[Common::PATTERN_IS] = [
        'title' => $translator->translate('is')
      ];
      $values[Common::PATTERN_IS_NOT] = [
        'title' => $translator->translate('is not')
      ];
    }
    $values[Common::PATTERN_CONTAIN] = [
      'title' => $translator->translate('contains')
    ];
    $values[Common::PATTERN_NOT_CONTAIN] = [
      'title' => $translator->translate('does not contain')
    ];
    $values[Common::PATTERN_BEGIN] = [
      'title' => $translator->translate('starting with')
    ];
    $values[Common::PATTERN_END] = [
      'title' => $translator->translate('finished by')
    ];
    $values[Common::REGEX_MATCH] = [
      'title' => $translator->translate('regular expression matches')
    ];
    $values[Common::REGEX_NOT_MATCH] = [
      'title' => $translator->translate('regular expression does not match')
    ];
    $values[Common::PATTERN_EXISTS] = [
      'title' => $translator->translate('exists')
    ];
    $values[Common::PATTERN_DOES_NOT_EXISTS] = [
      'title' => $translator->translate('does not exist')
    ];
    $values[Common::PATTERN_FIND] = [
      'title' => $translator->translate('find')
    ];
    $values[Common::PATTERN_UNDER] = [
      'title' => $translator->translate('under')
    ];
    $values[Common::PATTERN_NOT_UNDER] = [
      'title' => $translator->translate('not under')
    ];
    $values[Common::PATTERN_IS_EMPTY] = [
      'title' => $translator->translate('is empty')
    ];

    return [
      'title'   => $translator->translate('Condition'),
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
    $parent = $modelName::find($patternId);
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
        $item = $modelName::find($valId);
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
      $item = $modelName::find($value);
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
    $parent = $modelName::find($patternId);
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
        $item = $modelName::find($valId);
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
      $item = $modelName::find($value);
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

  private function parseRegexMatchesValues($data)
  {
    // remove the first element (because it's the complete value)
    array_shift($data);
    foreach ($data as $value)
    {
      $this->regexResults[] = $value;
    }
  }

  public function getRegexResults()
  {
    return $this->regexResults;
  }

  public static function getConditionsForDefinition($model, $name)
  {
    $completeModelName = '\App\Models\\' . $model;
    if (!class_exists($completeModelName))
    {
      return [];
    }
    $item = new $completeModelName();
    $definitions = $item->getDefinitions();

    $type = null;
    $typeModelTree = false;
    foreach ($definitions as $definition)
    {
      if ($definition['name'] == $name)
      {
        $type = $definition['type'];
        if (isset($definition['itemtype']))
        {
          $typeModel = $definition['itemtype'];
          $item = new $typeModel();
          $typeModelTree = $item->isTree();
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
          \App\v1\Controllers\Rules\Common::PATTERN_CONTAIN,
          \App\v1\Controllers\Rules\Common::PATTERN_NOT_CONTAIN,
          \App\v1\Controllers\Rules\Common::PATTERN_BEGIN,
          \App\v1\Controllers\Rules\Common::PATTERN_END,
          \App\v1\Controllers\Rules\Common::REGEX_MATCH,
          \App\v1\Controllers\Rules\Common::REGEX_NOT_MATCH,
          \App\v1\Controllers\Rules\Common::PATTERN_EXISTS,
          \App\v1\Controllers\Rules\Common::PATTERN_DOES_NOT_EXISTS,
          \App\v1\Controllers\Rules\Common::PATTERN_IS_EMPTY,
        ];
          break;

      case 'boolean':
        $conditions = [
          \App\v1\Controllers\Rules\Common::PATTERN_IS,
          \App\v1\Controllers\Rules\Common::PATTERN_IS_NOT,
          \App\v1\Controllers\Rules\Common::PATTERN_EXISTS,
          \App\v1\Controllers\Rules\Common::PATTERN_DOES_NOT_EXISTS,
        ];
          break;

      case 'dropdown':
      case 'dropdown_remote':
        $conditions = [
          \App\v1\Controllers\Rules\Common::PATTERN_IS,
          \App\v1\Controllers\Rules\Common::PATTERN_IS_NOT,
          \App\v1\Controllers\Rules\Common::PATTERN_CONTAIN,
          \App\v1\Controllers\Rules\Common::PATTERN_NOT_CONTAIN,
          \App\v1\Controllers\Rules\Common::PATTERN_BEGIN,
          \App\v1\Controllers\Rules\Common::PATTERN_END,
          \App\v1\Controllers\Rules\Common::REGEX_MATCH,
          \App\v1\Controllers\Rules\Common::REGEX_NOT_MATCH,
          \App\v1\Controllers\Rules\Common::PATTERN_EXISTS,
          \App\v1\Controllers\Rules\Common::PATTERN_DOES_NOT_EXISTS,
          \App\v1\Controllers\Rules\Common::PATTERN_IS_EMPTY,
        ];
        if ($typeModelTree)
        {
          $conditions[] = \App\v1\Controllers\Rules\Common::PATTERN_UNDER;
          $conditions[] = \App\v1\Controllers\Rules\Common::PATTERN_NOT_UNDER;
        }
          break;
    }
    return $conditions;
  }
}
