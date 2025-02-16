<?php

declare(strict_types=1);

namespace App\Events;

final class RuleCreating
{
  /**
   * @template C of \App\Models\Common
   * @param C $model
   */
  public function __construct($model)
  {
    // $test =& $model;
    // new \App\Events\EntityCreating($test);

    // $model = $test;
    $className = get_class($model);
    $spl = explode('\\', $className);
    $model->setAttribute('sub_type', 'Rule' . $spl[array_key_last($spl)]);
  }
}
