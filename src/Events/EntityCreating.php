<?php

declare(strict_types=1);

namespace App\Events;

final class EntityCreating
{
  public function __construct(public $model)
  {
    if ($model->hasEntityField)
    {
      $model->entity_id = $GLOBALS['entity_id'];
    }
  }
}
