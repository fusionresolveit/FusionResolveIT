<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

use Carbon\Carbon;

final class Computerstorage extends \App\v1\Controllers\Common
{
  /**
   * @var array<string, \Respect\Validation\Validator>
   */
  private $vs = [];

  public function __construct(
    private \App\Models\Computer $computer
  )
  {
    $this->vs = [
      'NAME'          => Validation::attrStrNotempty('NAME'),
      // Not use DESCRIPTION because not have pertinent info
      // 'DESCRIPTION'   => Validation::attrStrNotempty('DESCRIPTION'),
      'DISKSIZE'      => Validation::attrStrNotempty('DISKSIZE'),
      // <!-- device interface type (SCSI|HDC|IDE|USB|1394|serial-ATA|SAS) -->
      'INTERFACE'     => Validation::attrStrNotempty('INTERFACE'),
      'MANUFACTURER'  => Validation::attrStrNotempty('MANUFACTURER'),
      'MODEL'         => Validation::attrStrNotempty('MODEL'),
      'TYPE'          => Validation::attrStrNotempty('TYPE'),
      'SERIALNUMBER'  => Validation::attrStrNotempty('SERIALNUMBER'),
      'FIRMWARE'      => Validation::attrStrNotempty('FIRMWARE'),
      // Not used here:
        // 'SCSI_COID'     => Validation::attrStrNotempty('SCSI_COID'),
        // 'SCSI_CHID'     => Validation::attrStrNotempty('SCSI_CHID'),
        // 'SCSI_UNID'     => Validation::attrStrNotempty('SCSI_UNID'),
        // 'SCSI_LUN'      => Validation::attrStrNotempty('SCSI_LUN'),
        // 'WWN'           => 'regex', // 50:00:51:e3:63:a4:49:01
        // start by 50:00 ou 60:00 ou 10:00 ou 2x:xx
        // see http://fr.wikipedia.org/wiki/World_Wide_Name
    ];
  }

  public function parse(object $dataObj): void
  {
    if (
        property_exists($dataObj, 'CONTENT') &&
        property_exists($dataObj->CONTENT, 'STORAGES')
    )
    {
      $content = Common::getArrayData($dataObj->CONTENT->STORAGES);
      // $this->manageSlots($content);
      $this->manageStorage($content);
    }
  }

  /**
   * @param array<mixed> $content
   */
  private function manageStorage(array $content): void
  {
    $storageIds = [];
    foreach ($content as $contentStorage)
    {
      $name = $this->getName($contentStorage);
      $serial = $this->getSerial($contentStorage);
      $size = $this->getStorageSize($contentStorage);
      $manufacturerId = $this->getManufacturer($contentStorage);
      $interfaceId = $this->getStorageInterface($contentStorage);
      $type = $this->getStorageType($contentStorage);
      $firmwareId = $this->getStorageFirmware($contentStorage, $manufacturerId);

      $storage = \App\Models\Storage::
          where('serial', $serial)
        ->where('size', $size)
        ->where('manufacturer_id', $manufacturerId)
        ->first();
      if (is_null($storage))
      {
        $storage = new \App\Models\Storage();
        $storage->name = $name;
        $storage->size = $size;
        $storage->manufacturer_id = $manufacturerId;
        $storage->serial = $serial;
        $storage->interfacetype_id = $interfaceId;
        $storage->type = $type;
        $storage->firmware_id = $firmwareId;
        $storage->fusioninventoried_at = Carbon::now();
        $storage->save();
      } else {
        if ($storage->getAttribute('type') == 0 && $type > 0)
        {
          $storage->type = $type;
        }
        if ($storage->getAttribute('firmware_id') != $firmwareId)
        {
          $storage->firmware_id = $firmwareId;
        }
        $storage->fusioninventoried_at = Carbon::now();
        $storage->save();
      }
      $storageIds[] = $storage->id;
    }
    $this->computer->storages()->sync($storageIds);
  }

  /**
   * Parse the name
   */
  private function getName(mixed $contentStorage): string|null
  {
    $name = null;
    if ($this->vs['NAME']->isValid($contentStorage))
    {
      $name = Common::cleanString($contentStorage->NAME);
    }
    return $name;
  }

  /**
   * Parse the serial number
   */
  private function getSerial(mixed $contentStorage): string|null
  {
    $serial = null;
    if ($this->vs['SERIALNUMBER']->isValid($contentStorage))
    {
      $serial = Common::cleanString($contentStorage->SERIALNUMBER);
    }
    return $serial;
  }

  /**
   * Parse the storage size
   */
  private function getStorageSize(mixed $contentStorage): int
  {
    $size = 0;
    if ($this->vs['DISKSIZE']->isValid($contentStorage))
    {
      $size = intval($contentStorage->DISKSIZE);
    }
    return $size;
  }

  /**
   * Parse the manufacturer
   */
  private function getManufacturer(mixed $contentStorage): int
  {
    $manufacturerId = 0;
    if ($this->vs['MANUFACTURER']->isValid($contentStorage))
    {
      $manufacturer = \App\Models\Manufacturer::withoutEagerLoads()->firstOrCreate(
        [
          'name' => Common::cleanString($contentStorage->MANUFACTURER),
        ],
      );
      $manufacturerId = $manufacturer->id;
    }
    return $manufacturerId;
  }

  /**
   * Parse the interface
   */
  private function getStorageInterface(mixed $contentStorage): int
  {
    $interfaceId = 0;
    if ($this->vs['INTERFACE']->isValid($contentStorage))
    {
      $interface = \App\Models\Interfacetype::withoutEagerLoads()->firstOrCreate(
        [
          'name' => Common::cleanString($contentStorage->INTERFACE),
        ],
      );
      $interfaceId = $interface->id;
    }
    return $interfaceId;
  }

  /**
   * Parse the type of storage
   */
  private function getStorageType(mixed $contentStorage): int
  {
    $types = \App\Models\Definitions\Storage::getTypesArray();
    if ($this->vs['TYPE']->isValid($contentStorage))
    {
      switch (Common::cleanString($contentStorage->TYPE)) {
        case 'disk':
        case 'ATA':
        case 'SERIAL-ATA':
        case 'Fibre Channel':
        case 'MPxIO':
        case 'FC':
        case 'SCSI':
        case 'Fixed hard disk media':
            return 1;

        case 'floppy':
        case '3 -Inch Floppy Disk':
        case '5 -Inch Floppy Disk':
        case '8-Inch Floppy Disk':
            return 2;

        case 'cd':
        case 'cdrom':
        case 'Disk drive':
        case 'Disk burning':
            return 3;

        case 'Card reader':
        case 'SD Card':
        case 'USB':
        case '1394':
        case 'Removable media other than floppy':
            return 4;

        case 'tape':
            return 5;
      }
    }
    // Try detect in model
    if ($this->vs['MODEL']->isValid($contentStorage))
    {
      $model = Common::cleanString($contentStorage->MODEL);
      if (stristr($model, 'cdrom') || stristr($model, 'dvd'))
      {
        return 3;
      }
    }
    return 0;
  }

  private function getStorageFirmware(mixed $contentStorage, int $manufacturerId): int
  {
    $firmwareId = 0;
    if (
        $this->vs['FIRMWARE']->isValid($contentStorage) &&
        $manufacturerId > 0
    )
    {
      $firmware = \App\Models\Firmware::withoutEagerLoads()->firstOrCreate(
        [
          'name'            => Common::cleanString($contentStorage->FIRMWARE),
          'manufacturer_id' => $manufacturerId,
          'model'           => \App\Models\Storage::class,
        ],
      );
      $firmwareId = $firmware->id;
    }

    return $firmwareId;
  }
}
