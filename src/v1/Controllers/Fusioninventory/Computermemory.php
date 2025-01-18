<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

final class Computermemory extends \App\v1\Controllers\Common
{
  public static function parse(object $dataObj, \App\Models\Computer $computer)
  {
    $vs = [
      'TYPE'          => Validation::attrStrNotempty('TYPE'),
      'SPEED'         => Validation::attrStrNotempty('SPEED'),
      'CAPACITY'      => Validation::attrStrNotempty('CAPACITY'),
      'MANUFACTURER'  => Validation::attrStrNotempty('MANUFACTURER'),
      'NUMSLOTS'      => Validation::attrStrNotempty('NUMSLOTS'),
      'SERIALNUMBER'  => Validation::attrStrNotempty('SERIALNUMBER'),
      'MODEL'         => Validation::attrStrNotempty('MODEL'),
    ];

    $prepareData = [];

    $dbMemories = $computer->memories;

    if (property_exists($dataObj->CONTENT, 'MEMORIES'))
    {
      $content = Common::getArrayData($dataObj->CONTENT->MEMORIES);

      foreach ($content as $contentMemory)
      {
        $name = 'Dummy Memory Module';
        $typeId = 0;
        if (
            $vs['TYPE']->isValid($contentMemory) &&
            $contentMemory->TYPE !== 'Empty Slot' &&
            $contentMemory->TYPE !== 'Unknown'
        )
        {
          $name = Common::cleanString($contentMemory->TYPE);
          $type = \App\Models\Devicememorytype::withoutEagerLoads()->firstOrCreate(
            [
              'name' => $name,
            ],
          );
          $typeId = $type->id;
        }

        $frequence = null;
        if ($vs['SPEED']->isValid($contentMemory))
        {
          $speed = Common::cleanString($contentMemory->SPEED);
          $matches = [];
          preg_match("/^(\d+)/", $speed, $matches);
          if (count($matches) == 2) {
            $frequence = $matches[1];
          }
        }

        $size = 0;
        if ($vs['CAPACITY']->isValid($contentMemory))
        {
          $size = intval($contentMemory->CAPACITY);
        }

        $manufacturerId = 0;
        if ($vs['MANUFACTURER']->isValid($contentMemory))
        {
          $manufacturer = \App\Models\Manufacturer::withoutEagerLoads()->firstOrCreate(
            [
              'name' => Common::cleanString($contentMemory->MANUFACTURER),
            ],
          );
          $manufacturerId = $manufacturer->id;
        }

        $busId = null;
        if ($vs['NUMSLOTS']->isValid($contentMemory))
        {
          $busId = Common::cleanString($contentMemory->NUMSLOTS);
        }

        $serial = null;
        if ($vs['SERIALNUMBER']->isValid($contentMemory))
        {
          $serial = Common::cleanString($contentMemory->SERIALNUMBER);
        }

        $modelId = 0;
        if ($vs['MODEL']->isValid($contentMemory))
        {
          $model = \App\Models\Devicememorymodel::withoutEagerLoads()->firstOrCreate(
            [
              'name' => Common::cleanString($contentMemory->MODEL),
            ],
          );
          $modelId = $model->id;
        }

        // MEMORYCORRECTION

        $devicememory = \App\Models\Devicememory::withoutEagerLoads()->firstOrCreate(
          [
            'name'                  => $name,
            'entity_id'             => 1,
            'frequence'             => $frequence,
            'manufacturer_id'       => $manufacturerId,
            'devicememorytype_id'   => $typeId,
            'devicememorymodel_id'  => $modelId,
          ],
        );

        $prepareData[] = [
          'id'          => $devicememory->id,
          'is_dynamic'  => true,
          'size'        => $size,
          'serial'      => $serial,
          'busID'       => $busId,
          // 'otherserial' => '',
        ];
      }
    }

    // TODO detach only memory not in this inventory

    // detach in DB
    foreach ($dbMemories as $dbmemory)
    {
      $found = false;
      foreach ($prepareData as $idx => $data)
      {
        $pivot = $dbmemory->getRelationValue('pivot');
        if (
            $data['id'] == $dbmemory->id &&
            $data['size'] == $pivot->size &&
            $data['serial'] == $pivot->serial &&
            $data['busID'] == $pivot->busID
        )
        {
          $found = $idx;
          break;
        }
      }
      if ($found === false)
      {
        $computer->memories()->wherePivot('id', $dbmemory->getRelationValue('pivot')->id)->detach($dbmemory->id);
      } else {
        unset($prepareData[$found]);
      }
    }

    // attach in DB
    foreach ($prepareData as $data)
    {
      $pivot = ['is_dynamic' => true];
      if (!is_null($data['serial']))
      {
        $pivot['serial'] = $data['serial'];
      }
      if (!is_null($data['busID']))
      {
        $pivot['busID'] = $data['busID'];
      }
      $computer->memories()->attach($data['id'], $pivot);
    }
  }
}
