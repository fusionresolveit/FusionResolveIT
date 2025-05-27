<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers\FusionInventory;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computermemory')]
#[CoversClass('\App\Models\Memoryslot')]
#[CoversClass('\App\Models\Memorymodule')]

final class ComputermemoryTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    // delete computers
    $computers = \App\Models\Computer::get();
    foreach ($computers as $computer)
    {
      $computer->forceDelete();
    }
    $items = \App\Models\Memorymodule::get();
    foreach ($items as $memorymodule)
    {
      $memorymodule->forceDelete();
    }
  }

  protected function setUp(): void
  {
    // // delete computers
    // $computers = \App\Models\Computer::get();
    // foreach ($computers as $computer)
    // {
    //   $computer->forceDelete();
    // }
  }

  public static function nameProvider(): array
  {
    return [
      'Empty Slot'    => ['Empty Slot', 'Dummy Memory Module'],
      'Unknown'       => [456, 'Dummy Memory Module'],
      'sodimm 333MHz' => ['sodimm 333MHz', 'sodimm 333MHz'],
      'empty value'   => ['', 'Dummy Memory Module'],
      'null'          => [null, 'Dummy Memory Module'],
    ];
  }

  #[DataProvider('nameProvider')]
  public function testGetName($type, $expected)
  {
    $computer = \App\Models\Computer::firstOrCreate(['name' => 'test']);
    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $reflection = new \ReflectionClass($computermemory);
    $method = $reflection->getMethod('getName');
    $method->setAccessible(true);
  
    $data = (object) [];
    if (!is_null($type))
    {
      $data->TYPE = $type;
    }

    $name = $method->invoke($computermemory, $data);
    $this->assertEquals($expected, $name);
  }

  public static function frequenceProvider(): array
  {
    return [
      'only text'     => ['Only text', null],
      'sodimm 333MHz' => ['sodimm 333MHz', null],
      'empty value'   => ['', null],
      '2600MHz'       => ['2600MHz', '2600'],
      'integer'       => [2600, null],
      '564'           => ['564', 564],
      '56.4'          => ['56.4', 56],
      'null'          => [null, null],
    ];
  }

  #[DataProvider('frequenceProvider')]
  public function testGetFrequence($frequence, $expected)
  {
    $computer = \App\Models\Computer::firstOrCreate(['name' => 'test']);
    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $reflection = new \ReflectionClass($computermemory);
    $method = $reflection->getMethod('getFrequence');
    $method->setAccessible(true);
  
    $data = (object) [];
    if (!is_null($frequence))
    {
      $data->SPEED = $frequence;
    }

    $newFrequence = $method->invoke($computermemory, $data);
    $this->assertEquals($expected, $newFrequence);
  }

  public static function memorysizeProvider(): array
  {
    return [
      'only text'     => ['Only text', null],
      'sodimm 333MHz' => ['sodimm 333MHz', null],
      'empty value'   => ['', null],
      '2600MHz'       => ['2600Mo', 2600],
      'integer'       => [2600, null],
      '564'           => ['564', 564],
      '56.4'          => ['56.4', 56],
      'null'          => [null, null],
    ];
  }

  #[DataProvider('memorysizeProvider')]
  public function testGetmemorySize($size, $expected)
  {
    $computer = \App\Models\Computer::firstOrCreate(['name' => 'test']);
    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $reflection = new \ReflectionClass($computermemory);
    $method = $reflection->getMethod('getMemorySize');
    $method->setAccessible(true);
  
    $data = (object) [];
    if (!is_null($size))
    {
      $data->CAPACITY = $size;
    }

    $size = $method->invoke($computermemory, $data);
    $this->assertEquals($expected, $size);
  }

  public static function serialProvider(): array
  {
    return [
      'only text'     => ['Only text', 'Only text'],
      'RGTtG67D!'     => ['RGTtG67D!', 'RGTtG67D!'],
      'empty value'   => ['', null],
      '2600MHz'       => ['2600Mo', '2600Mo'],
      'integer'       => [2600, null],
      '564'           => ['564', '564'],
      '56.4'          => ['56.4', '56.4'],
      'null'          => [null, null],
    ];
  }

  #[DataProvider('serialProvider')]
  public function testGetSerial($serial, $expected)
  {
    $computer = \App\Models\Computer::firstOrCreate(['name' => 'test']);
    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $reflection = new \ReflectionClass($computermemory);
    $method = $reflection->getMethod('getSerial');
    $method->setAccessible(true);
  
    $data = (object) [];
    if (!is_null($serial))
    {
      $data->SERIALNUMBER = $serial;
    }

    $newSerial = $method->invoke($computermemory, $data);
    $this->assertEquals($expected, $newSerial);
  }

  // manufacturer
  public static function manufacturerProvider(): array
  {
    return [
      'only text'     => ['Only text', 'Only text'],
      'empty value'   => ['', null],
      '2600MHz'       => ['2600Mo', '2600Mo'],
      'integer'       => [2600, null],
      '564'           => ['564', '564'],
      '56.4'          => ['56.4', '56.4'],
      'null'          => [null, null],
      'with space end' => ['intel ', 'intel'],
      'same than previous but with upcase' => ['Intel ', 'intel'],
    ];
  }

  #[DataProvider('manufacturerProvider')]
  public function testGetManufacturer($manuName, $expected)
  {
    $computer = \App\Models\Computer::firstOrCreate(['name' => 'test']);
    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $reflection = new \ReflectionClass($computermemory);
    $method = $reflection->getMethod('getmanufacturer');
    $method->setAccessible(true);
  
    $data = (object) [];
    if (!is_null($manuName))
    {
      $data->MANUFACTURER = $manuName;
    }

    $manufacturerId = $method->invoke($computermemory, $data);
    if (is_null($expected))
    {
      $this->assertEquals(0, $manufacturerId);
    } else {
      $manufacturer = \App\Models\Manufacturer::where('name', $expected)->first();
      $this->assertNotNull($manufacturer);
      $this->assertGreaterThan(0, $manufacturer->id);
    }
  }
}
