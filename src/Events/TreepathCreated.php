<?php

declare(strict_types=1);

namespace App\Events;

final class TreepathCreated
{
  public function __construct(public $model)
  {
    // Manage tree
    if ($model->isTree())
    {
      $modelName = '\\' . get_class($model);
      $currItem = $modelName::find($model->id);
      if (is_null($currItem))
      {
        return;
      }
      $currItem->treepath = sprintf("%05d", $currItem->id);
      if ($currItem->{$model->getForeignKey()} > 0)
      {
        $parentItem = $modelName::find($currItem->{$model->getForeignKey()});
        if (!is_null($parentItem))
        {
          $currItem->treepath = $parentItem->treepath . $currItem->treepath;
        }
      }
      $currItem->save();
    }
  }
}
