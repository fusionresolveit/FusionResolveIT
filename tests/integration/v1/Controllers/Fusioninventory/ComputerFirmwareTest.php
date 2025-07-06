<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers\Fusioninventory;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computerstorage')]
#[CoversClass('\App\v1\Controllers\Fusioninventory\Common')]

final class ComputerFirmwareTest extends TestCase
{
  protected $bios = [
    'ASSETTAG'      => 'No Asset Information',
    'BDATE'         => '11/03/2023',
    'BMANUFACTURER' => 'LENOVO',
    'BVERSION'      => 'R25ET31W (1.12 )',
    'MMANUFACTURER' => 'LENOVO',
    'MMODEL'        => '21H5CTO1WW',
    'MSN'           => 'W4CG42H09XJ',
    'SKUNUMBER'     => 'LENOVO_MT_21H5_BU_Think_FM_ThinkPad L14 Gen 4',
    'SMANUFACTURER' => 'LENOVO',
    'SMODEL'        => 'ThinkPad L14 Gen 4',
    'SSN'           => 'PW0A2KQ6',
  ];

  public static function setUpBeforeClass(): void
  {
    // delete computers
    $computers = \App\Models\Computer::get();
    foreach ($computers as $computer)
    {
      $computer->forceDelete();
    }
    $items = \App\Models\Firmware::get();
    foreach ($items as $firmware)
    {
      $firmware->forceDelete();
    }

    $items = \App\Models\Manufacturer::get();
    foreach ($items as $manufacturer)
    {
      $manufacturer->forceDelete();
    }
  }

  public function testComputerFirmware()
  {
    $FusionComputer = new \App\v1\Controllers\Fusioninventory\Computer();
    $reflection = new \ReflectionClass($FusionComputer);
    $method = $reflection->getMethod('getComputerFirmware');
    $method->setAccessible(true);

    $manufacturer = \App\Models\Manufacturer::firstOrCreate([
      'name' => 'LENOVO',
    ]);

    $firmwareId = $method->invoke($FusionComputer, (object) $this->bios, $manufacturer->id);
    $this->assertNotEquals(0, $firmwareId);
  }

  public function testNoManufacturer()
  {
    $FusionComputer = new \App\v1\Controllers\Fusioninventory\Computer();
    $reflection = new \ReflectionClass($FusionComputer);
    $method = $reflection->getMethod('getComputerFirmware');
    $method->setAccessible(true);

    $firmwareId = $method->invoke($FusionComputer, (object) $this->bios, 0);
    $this->assertEquals(0, $firmwareId);
  }

  public function testNoVersion()
  {
    $FusionComputer = new \App\v1\Controllers\Fusioninventory\Computer();
    $reflection = new \ReflectionClass($FusionComputer);
    $method = $reflection->getMethod('getComputerFirmware');
    $method->setAccessible(true);

    $manufacturer = \App\Models\Manufacturer::firstOrCreate([
      'name' => 'LENOVO',
    ]);

    $bios = $this->bios;
    unset($bios['BVERSION']);

    $firmwareId = $method->invoke($FusionComputer, (object) $bios, $manufacturer->id);
    $this->assertEquals(0, $firmwareId);
  }

  public function testInvalidVersion()
  {
    $FusionComputer = new \App\v1\Controllers\Fusioninventory\Computer();
    $reflection = new \ReflectionClass($FusionComputer);
    $method = $reflection->getMethod('getComputerFirmware');
    $method->setAccessible(true);

    $manufacturer = \App\Models\Manufacturer::firstOrCreate([
      'name' => 'LENOVO',
    ]);

    $bios = $this->bios;
    $bios['BVERSION'] = 578;

    $firmwareId = $method->invoke($FusionComputer, (object) $bios, $manufacturer->id);
    $this->assertEquals(0, $firmwareId);
  }
}
