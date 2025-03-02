<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules;

final class Action
{
  /**
   * @template D of \App\DataInterface\Post
   * @param D $data
   * @param array<string> $regexResult
   *
   * @return D
   */
  public function runAction(\App\Models\Rules\Ruleaction $action, $data, array $regexResult = [])
  {
    $value = $action->value;
    if (!is_null($action->fieldviewfield->itemtype))
    {
      $item = $action->fieldviewfield->itemtype::where('id', $value)->first();
      if (is_null($item))
      {
        return $data;
      }
      $value = $item;
    }

    switch ($action->action_type)
    {
      case 0: // assign
        $data->{$action->field} = $value;
          break;

      case 1: // assign dropdown
        if ($action->fieldviewfield->multiple === true)
        {
          $data->{$action->field} = [$value];
        } else {
          $data->{$action->field} = $value;
        }
          break;

      case 2: // append
        if (is_null($data->{$action->field}))
        {
          $data->{$action->field} = $value;
        } else {
          $data->{$action->field} .= $value;
        }
          break;

      case 3: // append dropdown
        if ($action->fieldviewfield->multiple === true)
        {
          if (is_null($data->{$action->field}))
          {
            $data->{$action->field}[] = $value;
          }
          elseif (!in_array($value, $data->{$action->field}))
          {
            $data->{$action->field}[] = $value;
          }
        } else {
          $data->{$action->field} .= $value;
        }
          break;

      case 4: // regex_result
        $data->{$action->field} = $value;
        foreach ($regexResult as $idx => $value)
        {
          if (!is_null($data->{$action->field}))
          {
            $data->{$action->field} = str_replace('#' . $idx, $value, $data->{$action->field});
          }
        }
          break;

      case 5: //append_regex_result
        $data->{$action->field} .= $value;
        foreach ($regexResult as $idx => $value)
        {
          $data->{$action->field} = str_replace('#' . $idx, $value, $data->{$action->field});
        }
          break;
    }
    return $data;
  }
}
