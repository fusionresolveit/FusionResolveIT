<?php

declare(strict_types=1);

namespace App\Events;

final class PivotAttaching
{
  /**
   * @param array<int> $pivotIds
   */
  public function __construct(string $relationName, array $pivotIds)
  {
    if ($relationName == 'storages') {
      foreach ($pivotIds as $id)
      {
        $storage = \App\Models\Storage::where('id', $id)->first();
        if (!is_null($storage))
        {
          $storage->itemComputers()->detach();
          $storage->itemNetworkequipments()->detach();
          $storage->itemPeripherals()->detach();
          $storage->itemPhones()->detach();
          $storage->itemPrinters()->detach();
        }
      }
    }
  }
}
