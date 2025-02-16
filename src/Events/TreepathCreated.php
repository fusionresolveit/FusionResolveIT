<?php

declare(strict_types=1);

namespace App\Events;

final class TreepathCreated
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
      $currItem = $modelName::where('id', $model->getAttribute('id'))->first();
      if (is_null($currItem))
      {
        return;
      }
      $currItem->treepath = sprintf("%05d", $currItem->id);
      if ($currItem->{$model->getForeignKey()} > 0)
      {
        $parentItem = $modelName::where('id', $currItem->{$model->getForeignKey()})->first();
        if (!is_null($parentItem))
        {
          $currItem->treepath = $parentItem->treepath . $currItem->treepath;
        }
      }
      $currItem->save();
    }
  }
}
