<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules;

final class Action
{
  public function runAction($action, $preparedData, array $regexResult = [])
  {
    switch ($action->action_type)
    {
      case "assign":
        $preparedData[$action->field] = $action->value;
          break;

      case "assign_dropdown":
        if (isset($action->fieldviewfield['multiple']))
        {
          $preparedData[$action->field] = [$action->value];
        } else {
          $preparedData[$action->field] = $action->value;
        }
          break;

      case "append":
        if (!isset($preparedData[$action->field]))
        {
          $preparedData[$action->field] = $action->value;
        } else {
          $preparedData[$action->field] .= $action->value;
        }
          break;

      case "append_dropdown":
        if (!isset($preparedData[$action->field]))
        {
          if (isset($action->fieldviewfield['multiple']))
          {
            $preparedData[$action->field] = [$action->value];
          } else {
            $preparedData[$action->field] = $action->value;
          }
        }
        elseif (isset($action->fieldviewfield['multiple']))
        {
          if (!in_array($action->value, $preparedData[$action->field]))
          {
            $preparedData[$action->field][] = $action->value;
          }
        } else {
          $preparedData[$action->field] .= $action->value;
        }
          break;

      case "regex_result":
        $preparedData[$action->field] = $action->value;
        foreach ($regexResult as $idx => $value)
        {
          $preparedData[$action->field] = str_replace('#' . $idx, $value, $preparedData[$action->field]);
        }
          break;

      case "append_regex_result":
        $preparedData[$action->field] .= $action->value;
        foreach ($regexResult as $idx => $value)
        {
          $preparedData[$action->field] = str_replace('#' . $idx, $value, $preparedData[$action->field]);
        }
          break;
    }
    return $preparedData;
  }
}
