<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

use Carbon\Carbon;

final class Computermemory extends \App\v1\Controllers\Common
{
  /**
   * @var array<int, \App\Models\Memoryslot>
   */
  private $slots = [];

  /**
   * @var array<string, \Respect\Validation\Validator>
   */
  private $vs = [];

  public function __construct(
    private \App\Models\Computer $computer
  )
  {
    $this->vs = [
      'TYPE'          => Validation::attrStrNotempty('TYPE'),
      'SPEED'         => Validation::attrStrNotempty('SPEED'),
      'CAPACITY'      => Validation::attrStrNotempty('CAPACITY'),
      'MANUFACTURER'  => Validation::attrStrNotempty('MANUFACTURER'),
      'NUMSLOTS'      => Validation::attrStrNotempty('NUMSLOTS'),
      'SERIALNUMBER'  => Validation::attrStrNotempty('SERIALNUMBER'),
      'MODEL'         => Validation::attrStrNotempty('MODEL'),
    ];

    foreach ($computer->memoryslots as $slot)
    {
      $this->slots[$slot->slotnumber] = $slot;
    }
  }

  public function parse(object $dataObj): void
  {
    if (
        property_exists($dataObj, 'CONTENT') &&
        property_exists($dataObj->CONTENT, 'MEMORIES')
    )
    {
      $content = Common::getArrayData($dataObj->CONTENT->MEMORIES);
      $this->manageSlots($content);
      $this->manageMemorymoduleOnSlot($content);
    }
  }

  /**
   * @param array<mixed> $content
   */
  private function manageSlots(array $content): void
  {
    $listSlotNumbers = [];
    foreach ($content as $contentMemory)
    {
      if ($this->vs['NUMSLOTS']->isValid($contentMemory))
      {
        $slotNumber = intval(Common::cleanString($contentMemory->NUMSLOTS));
        $listSlotNumbers[] = $slotNumber;

        if (!isset($this->slots[$slotNumber]))
        {
          // create slot
          $memoryslot = new \App\Models\Memoryslot();
          $memoryslot->item_id = $this->computer->id;
          $memoryslot->item_type = \App\Models\Computer::class;
          $memoryslot->is_dynamic = true;
          $memoryslot->slotnumber = intval($slotNumber);
          $memoryslot->save();
          $this->slots[$slotNumber] = $memoryslot;
        }
      }
    }
    // if slot exists in DB but not in agent inventory
    // delete
    foreach ($this->slots as $slotNumber => $slot)
    {
      if (!in_array($slotNumber, $listSlotNumbers))
      {
        $slot->forceDelete();
      }
    }
  }

  /**
   * @param array<mixed> $content
   */
  private function manageMemorymoduleOnSlot(array $content): void
  {
    foreach ($content as $contentMemory)
    {
      if ($this->vs['NUMSLOTS']->isValid($contentMemory))
      {
        $slotNumber = Common::cleanString($contentMemory->NUMSLOTS);
        // check on size + serial
        $slot = $this->slots[$slotNumber];

        $name = $this->getName($contentMemory);
        $typeId = $this->getType($contentMemory);
        $frequence = $this->getFrequence($contentMemory);
        $size = $this->getMemorySize($contentMemory);
        $manufacturerId = $this->getManufacturer($contentMemory);
        $serial = $this->getSerial($contentMemory);
        $modelId = $this->getModel($contentMemory);

        $findAndAttach = false;
        if (is_null($slot->memorymodule))
        {
          // search if in DB, else create
          if ($size == 0)
          {
            continue;
          }
          $findAndAttach = true;
        } else {
          // see if memorymodule attached is the right
          if (
              $slot->memorymodule->serial === $serial &&
              $slot->memorymodule->size === $size &&
              $slot->memorymodule->memorytype_id === $typeId &&
              $slot->memorymodule->manufacturer_id === $manufacturerId &&
              $slot->memorymodule->memorymodel_id === $modelId
          )
          {
            // we are OK
            if ($slot->memorymodule->frequence != $frequence)
            {
              $slot->memorymodule->frequence = $frequence;
              $slot->memorymodule->save();
            }
          } else {
            // free the memorymodule
            $slot->memorymodule->memoryslot_id = 0;
            $slot->memorymodule->save();
            // find and put
            $findAndAttach = true;
          }
        }
        if ($findAndAttach)
        {
          if (is_null($serial))
          {
            $memorymodule = \App\Models\Memorymodule::
                where('serial', $serial)
              ->where('size', $size)
              ->where('memorytype_id', $typeId)
              ->where('manufacturer_id', $manufacturerId)
              ->where('memorymodel_id', $modelId)
              ->where('memoryslot_id', 0)
              ->first();
          } else {
            $memorymodule = \App\Models\Memorymodule::
                where('serial', $serial)
              ->where('size', $size)
              ->where('memorytype_id', $typeId)
              ->where('manufacturer_id', $manufacturerId)
              ->where('memorymodel_id', $modelId)
              ->first();
          }
          if (is_null($memorymodule))
          {
            $memorymodule = new \App\Models\Memorymodule();
            $memorymodule->name = $name;
            $memorymodule->size = $size;
            $memorymodule->frequence = $frequence;
            $memorymodule->manufacturer_id = $manufacturerId;
            $memorymodule->memorymodel_id = $modelId;
            $memorymodule->memorytype_id = $typeId;
            $memorymodule->serial = $serial;
            $memorymodule->memoryslot_id = $slot->id;
            $memorymodule->fusioninventoried_at = Carbon::now();
            $memorymodule->save();
          } else {
            $memorymodule->memoryslot_id = $slot->id;
            if ($memorymodule->frequence != $frequence)
            {
              $memorymodule->frequence = $frequence;
            }
            $memorymodule->fusioninventoried_at = Carbon::now();
            $memorymodule->save();
          }
        }
      }
    }
  }

  private function getName(mixed $contentMemory): string
  {
    $name = 'Dummy Memory Module';
    if (
        $this->vs['TYPE']->isValid($contentMemory) &&
        $contentMemory->TYPE !== 'Empty Slot' &&
        $contentMemory->TYPE !== 'Unknown'
    )
    {
      $name = Common::cleanString($contentMemory->TYPE);
    }
    return $name;
  }

  private function getType(mixed $contentMemory): int
  {
    $typeId = 0;
    if (
        $this->vs['TYPE']->isValid($contentMemory) &&
        $contentMemory->TYPE !== 'Empty Slot' &&
        $contentMemory->TYPE !== 'Unknown'
    )
    {
      $name = Common::cleanString($contentMemory->TYPE);
      $type = \App\Models\Memorytype::withoutEagerLoads()->firstOrCreate(
        [
          'name' => $name,
        ],
      );
      $typeId = $type->id;
    }
    return $typeId;
  }

  private function getFrequence(mixed $contentMemory): int|null
  {
    $frequence = null;
    if ($this->vs['SPEED']->isValid($contentMemory))
    {
      $speed = Common::cleanString($contentMemory->SPEED);
      $matches = [];
      preg_match("/^(\d+)/", $speed, $matches);
      if (count($matches) == 2)
      {
        $frequence = intval($matches[1]);
      }
    }
    return $frequence;
  }

  private function getMemorySize(mixed $contentMemory): int
  {
    $size = 0;
    if ($this->vs['CAPACITY']->isValid($contentMemory))
    {
      $size = intval($contentMemory->CAPACITY);
    }
    return $size;
  }

  private function getManufacturer(mixed $contentMemory): int
  {
    $manufacturerId = 0;
    if ($this->vs['MANUFACTURER']->isValid($contentMemory))
    {
      $manufacturer = \App\Models\Manufacturer::withoutEagerLoads()->firstOrCreate(
        [
          'name' => Common::cleanString($contentMemory->MANUFACTURER),
        ],
      );
      $manufacturerId = $manufacturer->id;
    }
    return $manufacturerId;
  }

  private function getSerial(mixed $contentMemory): string|null
  {
    $serial = null;
    if ($this->vs['SERIALNUMBER']->isValid($contentMemory))
    {
      $serial = Common::cleanString($contentMemory->SERIALNUMBER);
    }
    return $serial;
  }

  private function getModel(mixed $contentMemory): int
  {
    $modelId = 0;
    if ($this->vs['MODEL']->isValid($contentMemory))
    {
      $model = \App\Models\Memorymodel::withoutEagerLoads()->firstOrCreate(
        [
          'name' => Common::cleanString($contentMemory->MODEL),
        ],
      );
      $modelId = $model->id;
    }
    return $modelId;
  }
}
