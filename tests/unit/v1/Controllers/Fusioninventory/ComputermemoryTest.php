<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers\FusionInventory;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computermemory')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Computer')]
#[UsesClass('\App\Models\Definitions\Appliance')]
#[UsesClass('\App\Models\Definitions\Autoupdatesystem')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Change')]
#[UsesClass('\App\Models\Definitions\Computer')]
#[UsesClass('\App\Models\Definitions\Computermodel')]
#[UsesClass('\App\Models\Definitions\Computertype')]
#[UsesClass('\App\Models\Definitions\Computervirtualmachine')]
#[UsesClass('\App\Models\Definitions\Contract')]
#[UsesClass('\App\Models\Definitions\Devicebattery')]
#[UsesClass('\App\Models\Definitions\Devicecase')]
#[UsesClass('\App\Models\Definitions\Devicecontrol')]
#[UsesClass('\App\Models\Definitions\Devicedrive')]
#[UsesClass('\App\Models\Definitions\Devicefirmware')]
#[UsesClass('\App\Models\Definitions\Devicegeneric')]
#[UsesClass('\App\Models\Definitions\Devicegraphiccard')]
#[UsesClass('\App\Models\Definitions\Deviceharddrive')]
#[UsesClass('\App\Models\Definitions\Devicememory')]
#[UsesClass('\App\Models\Definitions\Devicememorymodel')]
#[UsesClass('\App\Models\Definitions\Devicememorytype')]
#[UsesClass('\App\Models\Definitions\Devicemotherboard')]
#[UsesClass('\App\Models\Definitions\Devicenetworkcard')]
#[UsesClass('\App\Models\Definitions\Devicepci')]
#[UsesClass('\App\Models\Definitions\Devicepowersupply')]
#[UsesClass('\App\Models\Definitions\Deviceprocessor')]
#[UsesClass('\App\Models\Definitions\Devicesensor')]
#[UsesClass('\App\Models\Definitions\Devicesimcard')]
#[UsesClass('\App\Models\Definitions\Devicesoundcard')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Domain')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Infocom')]
#[UsesClass('\App\Models\Definitions\ItemDevicememory')]
#[UsesClass('\App\Models\Definitions\Itemdisk')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Manufacturer')]
#[UsesClass('\App\Models\Definitions\Network')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Operatingsystem')]
#[UsesClass('\App\Models\Definitions\Problem')]
#[UsesClass('\App\Models\Definitions\Reservationitem')]
#[UsesClass('\App\Models\Definitions\State')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Devicememory')]
#[UsesClass('\App\Models\Ticket')]
#[UsesClass('\App\Traits\Relationships\Changes')]
#[UsesClass('\App\Traits\Relationships\Contract')]
#[UsesClass('\App\Traits\Relationships\Documents')]
#[UsesClass('\App\Traits\Relationships\Entity')]
#[UsesClass('\App\Traits\Relationships\Infocom')]
#[UsesClass('\App\Traits\Relationships\Knowbaseitems')]
#[UsesClass('\App\Traits\Relationships\Location')]
#[UsesClass('\App\Traits\Relationships\Notes')]
#[UsesClass('\App\Traits\Relationships\Problems')]
#[UsesClass('\App\Traits\Relationships\Reservations')]
#[UsesClass('\App\Traits\Relationships\Tickets')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Common')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Validation')]

final class ComputermemoryTest extends TestCase
{
  public static function speedProvider(): array
  {
    return [
      'unknown' => ['Unknown', null],
      '800 MHz' => ['800 MHz', 800],
      '333 MHz (3.0 ns)' => ['333 MHz (3.0 ns)', 333],
    ];
  }

  #[DataProvider('speedProvider')]
  public function testSpeeds($speed, $expected): void
  {
    // delete computers
    \App\Models\Computer::truncate();

    $myData = [
      'name' => 'testMemory',
    ];
    $computer = \App\Models\Computer::create($myData);

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => (object) [
          'SPEED' => $speed,
        ],
      ],
    ];

    \App\v1\Controllers\Fusioninventory\Computermemory::parse($data, $computer);

    $computer->refresh();

    $items = $computer->memories()->get();
    $this->assertEquals(1, count($items), 'Must have 1 memory');
    $this->assertEquals('Dummy Memory Module', $items[0]->name, 'memory name not right');
    $this->assertEquals($expected, $items[0]->frequence, 'Memory frequence not right');
  }

  public function testType(): void
  {
    // delete computers
    \App\Models\Computer::truncate();

    $myData = [
      'name' => 'testMemory',
    ];
    $computer = \App\Models\Computer::create($myData);

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => (object) [
          'TYPE' => 'DDR2',
        ],
      ],
    ];

    \App\v1\Controllers\Fusioninventory\Computermemory::parse($data, $computer);

    $computer->refresh();

    $items = $computer->memories()->get();
    $this->assertEquals(1, count($items), 'Must have 1 memory');
    $this->assertEquals('DDR2', $items[0]->name, 'memory name not right');
    $this->assertGreaterThan(0, $items[0]->devicememorytype_id);
    $type = \App\Models\Devicememorytype::find($items[0]->devicememorytype_id);
    $this->assertEquals('DDR2', $type->name, 'type name not right');
  }

  public function testTwoMemories(): void
  {
    // delete computers
    \App\Models\Computer::truncate();
    \App\Models\Devicememory::truncate();
    \App\Models\ItemDevicememory::truncate();

    $myData = [
      'name' => 'testMemory',
    ];
    $computer = \App\Models\Computer::create($myData);

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => [
          (object) [
            'TYPE' => 'DDR2',
          ],
          (object) [
            'TYPE' => 'DDR2',
          ],
        ],
      ],
    ];

    \App\v1\Controllers\Fusioninventory\Computermemory::parse($data, $computer);

    $computer->refresh();

    $items = $computer->memories()->get();
    $this->assertEquals(2, count($items), 'Must have 2 memories');

    // Now remove 1 memory and add two new

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => [
          (object) [
            'TYPE' => 'DDR3',
          ],
          (object) [
            'TYPE' => 'DDR2',
          ],
          (object) [
            'TYPE' => 'DDR3',
          ],
        ],
      ],
    ];

    \App\v1\Controllers\Fusioninventory\Computermemory::parse($data, $computer);

    $computer->refresh();

    $items = $computer->memories()->orderBy('item_devicememory.id')->get();
    $this->assertEquals(3, count($items), 'Must have 3 memories');

    $this->assertEquals('DDR2', $items[0]->name, 'memory 2 name not right');
    $this->assertGreaterThan(0, $items[0]->devicememorytype_id);
    $type = \App\Models\Devicememorytype::find($items[0]->devicememorytype_id);
    $this->assertEquals('DDR2', $type->name, 'type 2 name not right');
    $this->assertEquals(1, $items[0]->pivot->id, 'memory 2 intermediate id not right');

    $this->assertEquals('DDR3', $items[1]->name, 'memory 1 name not right');
    $this->assertGreaterThan(0, $items[1]->devicememorytype_id);
    $type = \App\Models\Devicememorytype::find($items[1]->devicememorytype_id);
    $this->assertEquals('DDR3', $type->name, 'type 1 name not right');
    $this->assertEquals(3, $items[1]->pivot->id, 'memory 1 intermediate id not right');

    $this->assertEquals('DDR3', $items[2]->name, 'memory 3 name not right');
    $this->assertGreaterThan(0, $items[2]->devicememorytype_id);
    $type = \App\Models\Devicememorytype::find($items[2]->devicememorytype_id);
    $this->assertEquals('DDR3', $type->name, 'type 3 name not right');
    $this->assertEquals(4, $items[2]->pivot->id, 'memory 3 intermediate id not right');
  }
}
