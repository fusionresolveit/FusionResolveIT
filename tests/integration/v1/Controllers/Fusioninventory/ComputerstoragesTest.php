<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers\Fusioninventory;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computerstorage')]
#[CoversClass('\App\v1\Controllers\Fusioninventory\Common')]

final class ComputerstoragesTest extends TestCase
{

  protected function setUp(): void
  {
    // Needed to reset events
    \App\Models\Computer::boot();
  }

  protected function clean(): void
  {
    // delete computers
    $computers = \App\Models\Computer::get();
    foreach ($computers as $computer)
    {
      $computer->forceDelete();
    }
    $items = \App\Models\Storage::get();
    foreach ($items as $storage)
    {
      $storage->forceDelete();
    }
    $items = \App\Models\Firmware::get();
    foreach ($items as $firmware)
    {
      $firmware->forceDelete();
    }
  }

  public function testMoveStorage(): void
  {
    $this->clean();
    $myData = [
      'name' => 'testStorages1',
    ];
    $computer1 = \App\Models\Computer::create($myData);

    $myData = [
      'name' => 'testStorages2',
    ];
    $computer2 = \App\Models\Computer::create($myData);

    $storage1 = [
      'DESCRIPTION'   => 'SATA',
      'DISKSIZE'      => 960197,
      'FIRMWARE'      => '0132',
      'MANUFACTURER'  => 'Intel',
      'NAME'          => 'sda',
      'SERIALNUMBER'  => 'BTYF209101D6960CGN',
      'TYPE'          => 'disk',
    ];
    $storage2 = [
      'DESCRIPTION'   => 'SATA',
      'DISKSIZE'      => 960197,
      'FIRMWARE'      => '0132',
      'MANUFACTURER'  => 'Intel',
      'NAME'          => 'sdb',
      'SERIALNUMBER'  => 'BTYF209101D6960CGO',
      'TYPE'          => 'disk',
    ];
    $storage3 = [
      'DESCRIPTION'   => 'SATA',
      'DISKSIZE'      => 960197,
      'FIRMWARE'      => '0132',
      'MANUFACTURER'  => 'Intel',
      'NAME'          => 'sdc',
      'SERIALNUMBER'  => 'BTYF209101D6960CGP',
      'TYPE'          => 'disk',
    ];
    $storage4 = [
      'DESCRIPTION'   => 'SATA',
      'DISKSIZE'      => 960197,
      'FIRMWARE'      => '0132',
      'MANUFACTURER'  => 'Intel',
      'NAME'          => 'sdd',
      'SERIALNUMBER'  => 'BTYF209101D6960CGQ',
      'TYPE'          => 'disk',
    ];

    // PART I
    // computer1
    //   storage1
    //   storage2
    $data = (object) [
      'CONTENT' => (object) [
        'STORAGES' => [
          (object) $storage1,
          (object) $storage2,
        ],
      ],
    ];
    $computerstorage = new \App\v1\Controllers\Fusioninventory\Computerstorage($computer1);
    $computerstorage->parse($data);
    $storages = \App\Models\Storage::count();
    $this->assertEquals(2, $storages);
    $computer1->refresh();
    $this->assertCount(2, $computer1->storages);

    // PART II
    // computer2
    //   storage3
    //   storage4
    $data = (object) [
      'CONTENT' => (object) [
        'STORAGES' => [
          (object) $storage3,
          (object) $storage4,
        ],
      ],
    ];
    $computerstorage = new \App\v1\Controllers\Fusioninventory\Computerstorage($computer2);
    $computerstorage->parse($data);
    $storages = \App\Models\Storage::count();
    $this->assertEquals(4, $storages);
    $computer2->refresh();
    $this->assertCount(2, $computer2->storages);

    // PART III
    // On computer1, remove storage2, add storage3, so remove storage3 from computer2
    // computer1
    //   storage1
    //   storage3
    $data = (object) [
      'CONTENT' => (object) [
        'STORAGES' => [
          (object) $storage1,
          (object) $storage3,
        ],
      ],
    ];
    $computerstorage = new \App\v1\Controllers\Fusioninventory\Computerstorage($computer1);
    $computerstorage->parse($data);
    $storages = \App\Models\Storage::count();
    $this->assertEquals(4, $storages);
    $computer1->refresh();
    $computer2->refresh();
    $this->assertCount(2, $computer1->storages);
    $this->assertEquals('sda', $computer1->storages[0]->name);
    $this->assertEquals('sdc', $computer1->storages[1]->name);

    $this->assertCount(1, $computer2->storages);
    $this->assertEquals('sdd', $computer2->storages[0]->name);

    // PART IV
    // On computer2, keep have storage4
    // computer2
    //   storage4
    $data = (object) [
      'CONTENT' => (object) [
        'STORAGES' => [
          (object) $storage4,
        ],
      ],
    ];
    $computerstorage = new \App\v1\Controllers\Fusioninventory\Computerstorage($computer2);
    $computerstorage->parse($data);
    $storages = \App\Models\Storage::count();
    $this->assertEquals(4, $storages);
    $computer2->refresh();
    $this->assertCount(2, $computer1->storages);
    $this->assertEquals('sda', $computer1->storages[0]->name);
    $this->assertEquals('sdc', $computer1->storages[1]->name);

    $this->assertCount(1, $computer2->storages);
    $this->assertEquals('sdd', $computer2->storages[0]->name);

    // PART V
    // On computer1, keep only storage1
    // computer1
    //   storage1
    $data = (object) [
      'CONTENT' => (object) [
        'STORAGES' => [
          (object) $storage1,
        ],
      ],
    ];
    $computerstorage = new \App\v1\Controllers\Fusioninventory\Computerstorage($computer1);
    $computerstorage->parse($data);
    $storages = \App\Models\Storage::count();
    $this->assertEquals(4, $storages);
    $computer1->refresh();
    $computer2->refresh();
    $this->assertCount(1, $computer1->storages);
    $this->assertEquals('sda', $computer1->storages[0]->name);

    $this->assertCount(1, $computer2->storages);
    $this->assertEquals('sdd', $computer2->storages[0]->name);

    // PART VI
    // On computer2, have storage3 and storage4
    $data = (object) [
      'CONTENT' => (object) [
        'STORAGES' => [
          (object) $storage3,
          (object) $storage4,
        ],
      ],
    ];
    $computerstorage = new \App\v1\Controllers\Fusioninventory\Computerstorage($computer2);
    $computerstorage->parse($data);
    $storages = \App\Models\Storage::count();
    $this->assertEquals(4, $storages);
    $computer2->refresh();
    $this->assertCount(1, $computer1->storages);
    $this->assertEquals('sda', $computer1->storages[0]->name);

    $this->assertCount(2, $computer2->storages);
    $this->assertEquals('sdc', $computer2->storages[0]->name);
    $this->assertEquals('sdd', $computer2->storages[1]->name);
  }

  /**
   * @depends testMoveStorage
   */
  public function testFirmware(): void
  {
    $firmware = \App\Models\Firmware::get();
    $this->assertCount(1, $firmware);
    $item = $firmware[0];
    $this->assertEquals('0132', $item->getAttribute('name'));
    $this->assertNull($item->getAttribute('date'));
    $this->assertEquals(\App\Models\Storage::class, $item->getAttribute('model'));
    $this->assertEquals('Storage', $item->getAttribute('modelname'));

    $this->assertNotNull($item->manufacturer);
    $this->assertEquals('Intel', $item->manufacturer->name);

    // Verify not have create multiple manufacturers 'Intel'
    $nb = \App\Models\Manufacturer::where('name', 'Intel')->count();
    $this->assertEquals(1, $nb, 'Must have only 1 manufacturer `Intel`');
  }

  public function testFirmwareNoManufacturer(): void
  {
    $this->clean();
    $myData = [
      'name' => 'testStorages3',
    ];
    $computer = \App\Models\Computer::create($myData);

    $storage1 = [
      'DESCRIPTION'   => 'SATA',
      'DISKSIZE'      => 960197,
      'FIRMWARE'      => '0143',
      'NAME'          => 'sda',
      'SERIALNUMBER'  => 'BTYF209101D6960DDD',
      'TYPE'          => 'disk',
    ];

    $data = (object) [
      'CONTENT' => (object) [
        'STORAGES' => [
          (object) $storage1,
        ],
      ],
    ];
    $computerstorage = new \App\v1\Controllers\Fusioninventory\Computerstorage($computer);
    $computerstorage->parse($data);

    $storage = \App\Models\Storage::where('serial', 'BTYF209101D6960DDD')->first();
    $this->assertNotNull($storage);

    $this->assertNull($storage->getAttribute('firmware'));

    $firmware = \App\Models\Firmware::where('name', '0143')->first();
    $this->assertNull($firmware);
  }
}
