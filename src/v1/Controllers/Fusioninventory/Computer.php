<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

use Carbon\Carbon;

final class Computer extends \App\v1\Controllers\Common
{
  /**
   * @var array<string, \Respect\Validation\Validator>
   */
  private $vs = [];

  public function __construct()
  {
    $this->vs = [
      'NAME'          => Validation::attrStrNotempty('NAME'),
      'BVERSION'      => Validation::attrStrNotempty('BVERSION'),
    ];
  }

  public function importComputer(string $dataStr): void
  {
    $dataObj = Common::xmlToObj($dataStr);
    $json = json_encode($dataObj);
    if ($json === false)
    {
      throw new FusioninventoryXmlException('Data error', 400);
    }
    $dataObject = json_decode($json);
    // file_put_contents('/tmp/fusion.log', print_r($dataObj, true));

    // blacklists

    // rule

    // dictionnaries

    if (!property_exists($dataObject, 'CONTENT'))
    {
      throw new FusioninventoryXmlException('Data not right', 400);
    }

    if (
        !property_exists($dataObject, 'CONTENT') ||
        !property_exists($dataObject->CONTENT, 'HARDWARE') ||
        !Validation::attrStrNotempty('NAME')->isValid($dataObject->CONTENT->HARDWARE)
    )
    {
      throw new FusioninventoryXmlException('Data not right, missing HARDWARE/NAME element', 400);
    }

    if (
        property_exists($dataObject->CONTENT, 'BIOS') &&
        property_exists($dataObject->CONTENT->BIOS, 'SSN') &&
        !empty($dataObject->CONTENT->BIOS->SSN)
    )
    {
      $computer = \App\Models\Computer::where('serial', $dataObject->CONTENT->BIOS->SSN)
        ->withOnly([
          'processors',
          'softwareversions',
          'manufacturer',
          'type'
        ])
        ->first();
    } else {
      $computer = \App\Models\Computer::where('name', $dataObject->CONTENT->HARDWARE->NAME)
        ->withOnly([
          'processors',
          'softwareversions',
          'manufacturer',
          'type'
        ])
        ->first();
    }
    if (is_null($computer))
    {
      $computer = new \App\Models\Computer();
    }

    $manufacturerId = $this->getManufacturer($dataObject);

    $computer->name = $dataObject->CONTENT->HARDWARE->NAME;
    // $computer->uuid = $dataObj->CONTENT->HARDWARE->UUID;
    if (
        property_exists($dataObject->CONTENT, 'BIOS') &&
        property_exists($dataObject->CONTENT->BIOS, 'SSN') &&
        !empty($dataObject->CONTENT->BIOS->SSN)
    )
    {
      $computer->serial = $dataObject->CONTENT->BIOS->SSN;
    }
    $firmwareId = 0;
    if (
        property_exists($dataObject->CONTENT, 'BIOS') &&
        property_exists($dataObject->CONTENT->BIOS, 'BVERSION')
    )
    {
      $firmwareId = $this->getComputerFirmware($dataObject->CONTENT->BIOS, $manufacturerId);
    }

    $computer->otherserial = $this->getOtherSerial($dataObject);
    $computer->manufacturer_id = $manufacturerId;
    $computer->computertype_id = $this->getType($dataObject);
    $computer->firmware_id = $firmwareId;
    $computer->fusioninventoried_at = Carbon::now();
    // computermodel_id

    $computer->save();

    // $this->operatingSystem($dataObj, $computer);
    Computersoftware::parse($dataObject, $computer);
    // $this->antivirus($dataObj, $computer);
    Computerprocessor::parse($dataObject, $computer);
    Computeroperatingsystem::parse($dataObject, $computer);

    $computerMemory = new Computermemory($computer);
    $computerMemory->parse($dataObject);

    $computerStorage = new Computerstorage($computer);
    $computerStorage->parse($dataObject);
  }

  private function getOtherSerial(object $dataObj): string|null
  {
    if (
        property_exists($dataObj, 'CONTENT') &&
        property_exists($dataObj->CONTENT, 'BIOS') &&
        property_exists($dataObj->CONTENT->BIOS, 'ASSETTAG')
    )
    {
      return $dataObj->CONTENT->BIOS->ASSETTAG;
    }
    return null;
  }

  private function getManufacturer(object $dataObj): int
  {
    if (
        property_exists($dataObj, 'CONTENT') &&
        property_exists($dataObj->CONTENT, 'BIOS')
    )
    {
      $fields = ['SMANUFACTURER', 'MMANUFACTURER', 'BMANUFACTURER'];
      foreach ($fields as $field)
      {
        if (
            property_exists($dataObj->CONTENT->BIOS, $field) &&
            !empty($dataObj->CONTENT->BIOS->{$field})
        )
        {
          $name = $dataObj->CONTENT->BIOS->{$field};
          $manufacturer = \App\Models\Manufacturer::where('name', $name)->first();
          if (is_null($manufacturer))
          {
            $manufacturer = new \App\Models\Manufacturer();
            $manufacturer->name = $name;
            $manufacturer->save();
          }
          return $manufacturer->id;
        }
      }
    }
    return 0;
  }

  private function getType(object $dataObj): int
  {
    $fields = [
      ['HARDWARE', 'CHASSIS_TYPE'],
      ['BIOS', 'TYPE'],
      ['BIOS', 'MMODEL'],
      ['HARDWARE', 'VMSYSTEM'],
    ];
    foreach ($fields as $field)
    {
      if (
          property_exists($dataObj, 'CONTENT') &&
          property_exists($dataObj->CONTENT, $field[0]) &&
          property_exists($dataObj->CONTENT->{$field[0]}, $field[1]) &&
          !empty($dataObj->CONTENT->{$field[0]}->{$field[1]})
      )
      {
        $name = $dataObj->CONTENT->{$field[0]}->{$field[1]};
        $model = \App\Models\Computermodel::where('name', $name)->first();
        if (is_null($model))
        {
          $model = new \App\Models\Computermodel();
          $model->name = $name;
          $model->save();
        }
        return $model->id;
      }
    }
    return 0;
  }

  // private function operatingSystem(object $dataObj, \App\Models\Computer $computer)
  // {
  // }

  // private function antivirus(object $dataObj, \App\Models\Computer $computer): void
  // {
  //   if (property_exists($dataObj->CONTENT, 'SOFTWARES'))
  //   {
  //     // $content = json_decode(json_encode($dataObj->CONTENT));
  //     // foreach ($content->ANTIVIRUS as $contentAntivirus)
  //     // {
  //       $antivirus = \App\Models\Computerantivirus::withoutEagerLoads()->firstOrCreate(
  //         [
  //           'name'        => (string) $dataObj->CONTENT->ANTIVIRUS->NAME,
  //           'computer_id' => $computer->id
  //         ],
  //         []
  //       );
  //       $antivirus->antivirus_version = $dataObj->CONTENT->ANTIVIRUS->VERSION;
  //       $antivirus->signature_version = $dataObj->CONTENT->ANTIVIRUS->BASE_VERSION;
  //       $antivirus->is_active = $dataObj->CONTENT->ANTIVIRUS->ENABLED;
  //       $antivirus->is_uptodate = $dataObj->CONTENT->ANTIVIRUS->UPTODATE;
  //       $antivirus->is_dynamic = true;
  //       $manufacturer = \App\Models\Manufacturer::withoutEagerLoads()->firstOrCreate(
  //         [
  //           'name' => $dataObj->CONTENT->ANTIVIRUS->COMPANY,
  //         ]
  //       );
  //       $antivirus->manufacturer_id = $manufacturer->id;

  //       $antivirus->save();
  //     // }
  //   }
  // }

  private function getComputerFirmware(mixed $contentBios, int $manufacturerId): int
  {
    $firmwareId = 0;
    if (
        $this->vs['BVERSION']->isValid($contentBios) &&
        $manufacturerId > 0
    )
    {
      $firmware = \App\Models\Firmware::withoutEagerLoads()->firstOrCreate(
        [
          'name'            => Common::cleanString($contentBios->BVERSION),
          'manufacturer_id' => $manufacturerId,
          'model'           => \App\Models\Computer::class
        ],
      );
      $firmwareId = $firmware->id;
    }

    return $firmwareId;
  }
}
