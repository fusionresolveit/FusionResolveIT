<?php

declare(strict_types=1);

namespace App\Events;

final class Updated
{
  /**
   * @template C of \App\Models\Common
   * @param C $model
   */
  public function __construct($model)
  {
    UpdatedKnowledgebasearticle::run($model);
    UpdatedUserProfile::run($model);
  }
}
