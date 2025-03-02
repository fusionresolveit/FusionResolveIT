<?php

declare(strict_types=1);

namespace App\Events;

final class EntityCreating
{
  /**
   * @template C of \App\Models\Common
   * @param C $model
   */
  public function __construct($model)
  {
    if ($model->isEntity())
    {
      $model->setAttribute('entity_id', $GLOBALS['entity_id']);
    }
  }
}
