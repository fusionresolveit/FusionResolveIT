<?php

declare(strict_types=1);

namespace App\Events;

final class RuleCreating
{
  public function __construct(public $model)
  {
    // $test =& $model;
    // new \App\Events\EntityCreating($test);

    // $model = $test;
    $className = get_class($model);
    $spl = explode('\\', $className);
    $model->sub_type = 'Rule' . $spl[array_key_last($spl)];
  }
}
