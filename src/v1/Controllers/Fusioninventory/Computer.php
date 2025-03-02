<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

final class Computer extends \App\v1\Controllers\Common
{
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
        !Validation::attrStrNotempty('NAME')->isValid($dataObject->CONTENT->HARDWARE) ||
        !Validation::attrStrNotempty('SSN')->isValid($dataObject->CONTENT->BIOS)
    )
    {
      return;
    }

    $computer = \App\Models\Computer::where('serial', $dataObject->CONTENT->BIOS->SSN)
    ->withOnly([
      'processors',
      'softwareversions',
      'manufacturer',
      'type'
    ])->first();
    if (is_null($computer))
    {
      $computer = new \App\Models\Computer();
    }

    $computer->name = $dataObject->CONTENT->HARDWARE->NAME;
    // $computer->uuid = $dataObj->CONTENT->HARDWARE->UUID;
    $computer->serial = $dataObject->CONTENT->BIOS->SSN;
    $computer->otherserial = $this->getOtherSerial($dataObject);
    $computer->manufacturer_id = $this->getManufacturer($dataObject);
    $computer->computertype_id = $this->getType($dataObject);
    // computermodel_id

    $computer->save();

    // $this->operatingSystem($dataObj, $computer);
    Computersoftware::parse($dataObject, $computer);
    // $this->antivirus($dataObj, $computer);
    Computerprocessor::parse($dataObject, $computer);
    Computeroperatingsystem::parse($dataObject, $computer);
    Computermemory::parse($dataObject, $computer);
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
}
