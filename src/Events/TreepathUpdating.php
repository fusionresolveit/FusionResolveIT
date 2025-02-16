<?php

declare(strict_types=1);

namespace App\Events;

final class TreepathUpdating
{
  /**
   * @template C of \App\Models\Common
   * @param C $model
   */
  public function __construct($model)
  {
    // Manage tree
    if ($model->isTree())
    {
      $modelName = '\\' . get_class($model);
      $model->setAttribute('treepath', sprintf("%05d", $model->getAttribute('id')));
      if ($model->{$model->getForeignKey()} > 0)
      {
        $parentItem = $modelName::where('id', $model->{$model->getForeignKey()})->first();
        if (!is_null($parentItem))
        {
          $model->setAttribute('treepath', $parentItem->getAttribute('treepath') . $model->getAttribute('treepath'));
        }
      }
    }
  }
}
