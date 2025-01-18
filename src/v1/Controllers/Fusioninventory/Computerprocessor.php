<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

final class Computerprocessor extends \App\v1\Controllers\Common
{
  public static function parse(object $dataObj, \App\Models\Computer $computer)
  {
    $vs = [
      'NAME'          => Validation::attrStrNotempty('NAME'),
      'MANUFACTURER'  => Validation::attrStrNotempty('MANUFACTURER'),
      'ID'            => Validation::attrStrNotempty('ID'),
      'SPEED'         => Validation::attrStrNotempty('SPEED'),
      'DESCRIPTION'   => Validation::attrStrNotempty('DESCRIPTION'),
      'STEPPING'      => Validation::attrStrNotempty('STEPPING'),
      'CORE'          => Validation::attrStrNotempty('CORE'),
      'THREAD'        => Validation::attrStrNotempty('THREAD'),
      'SERIAL'        => Validation::attrStrNotempty('SERIAL'),
    ];

    if (property_exists($dataObj->CONTENT, 'CPUS'))
    {
      $prepareData = [];

      $content = Common::getArrayData($dataObj->CONTENT->CPUS);
      $dbProcessors = $computer->processors;

      foreach ($content as $contentCpu)
      {
        // Validate the data
        if (!$vs['NAME']->isValid($contentCpu))
        {
          continue;
        }

        $manufacturer_id = 0;
        if ($vs['MANUFACTURER']->isValid($contentCpu))
        {
          $manufacturer = \App\Models\Manufacturer::withoutEagerLoads()->firstOrCreate(
            [
              'name' => Common::cleanString($contentCpu->MANUFACTURER),
            ],
          );
          $manufacturer_id = $manufacturer->id;
        }

        // serial blacklist
        // TODO finish with database
        $serial = null;
        if ($vs['SERIAL']->isValid($contentCpu) && gettype($contentCpu->SERIAL) != 'object')
        {
          $blacklist = ['ToBeFilledByO.E.M.'];
          if (!(Validation::strInArray($blacklist)->isValid($contentCpu->SERIAL)))
          {
            $serial = Common::cleanString($contentCpu->SERIAL);
          }
        }

        // use ID if exists and not null
        $cpuid = null;
        if ($vs['ID']->isValid($contentCpu))
        {
          $cpuid = Common::cleanString($contentCpu->ID);
          $cpuid = str_replace(' ', '', $cpuid);
        }

        $supp = [];
        if ($vs['SPEED']->isValid($contentCpu))
        {
          $supp['frequence'] = Common::cleanString($contentCpu->SPEED);
        }
        if ($vs['DESCRIPTION']->isValid($contentCpu))
        {
          $supp['comment'] = Common::cleanString($contentCpu->DESCRIPTION);
        }
        if ($vs['STEPPING']->isValid($contentCpu))
        {
          $supp['stepping'] = Common::cleanString($contentCpu->STEPPING);
        }
        if ($vs['CORE']->isValid($contentCpu))
        {
          $supp['nbcores_default'] = Common::cleanString($contentCpu->CORE);
        }
        if ($vs['THREAD']->isValid($contentCpu))
        {
          $supp['nbthreads_default'] = Common::cleanString($contentCpu->THREAD);
        }
        // create it
        $processor = \App\Models\Deviceprocessor::withoutEagerLoads()->firstOrCreate(
          [
            'name'            => Common::cleanString($contentCpu->NAME),
            'entity_id'       => 1,
            'manufacturer_id' => $manufacturer_id,
            'cpuid'           => $cpuid,
          ],
          $supp,
        );

        $prepareData[] = [
          'id'      => $processor->id,
          'serial'  => $serial,
        ];
      }

      // detach in DB
      foreach ($dbProcessors as $dbprocessor)
      {
        $found = false;
        foreach ($prepareData as $idx => $data)
        {
          if (
              $data['id'] == $dbprocessor->id &&
              $data['serial'] == $dbprocessor->getRelationValue('pivot')->serial
          )
          {
            $found = $idx;
            break;
          }
        }
        if ($found === false)
        {
          $computer
            ->processors()
            ->wherePivot('id', $dbprocessor->getRelationValue('pivot')->id)
            ->detach($dbprocessor->id);
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
        $computer->processors()->attach($data['id'], $pivot);
      }
    }
  }
}
