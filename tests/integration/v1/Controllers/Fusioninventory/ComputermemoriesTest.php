<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers\Fusioninventory;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computeroperatingsystem')]
#[CoversClass('\App\v1\Controllers\Fusioninventory\Common')]

final class ComputermemoriesTest extends TestCase
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

  public static function speedProvider(): array
  {
    return [
      'unknown'          => ['Unknown', null],
      '800 MHz'          => ['800 MHz', 800],
      '333 MHz (3.0 ns)' => ['333 MHz (3.0 ns)', 333],
    ];
  }

  #[DataProvider('speedProvider')]
  public function testSpeeds($speed, $expected): void
  {
    $myData = [
      'name' => 'testMemory',
    ];
    $computer = \App\Models\Computer::create($myData);

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => (object) [
          'SPEED'    => $speed,
          'CAPACITY' => '32768',
          'NUMSLOTS' => '1',
        ],
      ],
    ];

    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $computermemory->parse($data);

    $computer->refresh();

    $items = $computer->memoryslots()->get();
    $this->assertEquals(1, count($items), 'Must have 1 slot');
    $this->assertNotNull($items[0]->memorymodule);
    $this->assertEquals('Dummy Memory Module', $items[0]->memorymodule->name, 'memory name not right');
    $this->assertEquals($expected, $items[0]->memorymodule->frequence, 'Memory frequence not right');
  }

  public function testType(): void
  {
    $myData = [
      'name' => 'testMemory',
    ];
    $computer = \App\Models\Computer::create($myData);

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => (object) [
          'TYPE' => 'DDR2',
          'CAPACITY' => '32768',
          'NUMSLOTS' => '1',
        ],
      ],
    ];

    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $computermemory->parse($data);

    $computer->refresh();

    $items = $computer->memoryslots()->get();
    $this->assertEquals(1, count($items), 'Must have 1 slot');
    $this->assertEquals('DDR2', $items[0]->memorymodule->name, 'memory name not right');
    $this->assertGreaterThan(0, $items[0]->memorymodule->memorytype_id);
    $type = \App\Models\Memorytype::find($items[0]->memorymodule->memorytype_id);
    $this->assertEquals('DDR2', $type->name, 'type name not right');
  }

  public function testTwoMemories(): void
  {
    $items = \App\Models\Memorymodule::get();
    foreach ($items as $memorymodule)
    {
      $memorymodule->forceDelete();
    }

    $myData = [
      'name' => 'testMemory',
    ];
    $computer = \App\Models\Computer::create($myData);

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => [
          (object) [
            'TYPE' => 'DDR2',
            'CAPACITY' => '32768',
            'NUMSLOTS' => '1',
            ],
          (object) [
            'TYPE' => 'DDR2',
            'CAPACITY' => '32768',
            'NUMSLOTS' => '2',
            ],
        ],
      ],
    ];

    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $computermemory->parse($data);

    $computer->refresh();

    $items = $computer->memoryslots()->get();
    $this->assertEquals(2, count($items), 'Must have 2 memories');
    $this->assertEquals(2, \App\Models\Memorymodule::count(), 'Must have 2 memory modules');

    // Now remove 1 memory and add two new

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => [
          (object) [
            'TYPE' => 'DDR3',
            'CAPACITY' => '32768',
            'NUMSLOTS' => '1',
          ],
          (object) [
            'TYPE' => 'DDR2',
            'CAPACITY' => '32768',
            'NUMSLOTS' => '2',
          ],
          (object) [
            'TYPE' => 'DDR3',
            'CAPACITY' => '32768',
            'NUMSLOTS' => '3',
          ],
        ],
      ],
    ];

    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $computermemory->parse($data);

    $computer->refresh();

    $items = $computer->memoryslots()->orderBy('id')->get();
    $this->assertEquals(3, count($items), 'Must have 3 slots');

    $this->assertEquals('DDR2', $items[1]->memorymodule->name, 'memory 1 name not right');
    $this->assertGreaterThan(0, $items[1]->memorymodule->memorytype_id);
    $type = \App\Models\Memorytype::find($items[1]->memorymodule->memorytype_id);
    $this->assertEquals('DDR2', $type->name, 'type 1 name not right');

    $this->assertEquals('DDR3', $items[0]->memorymodule->name, 'memory 2 name not right');
    $this->assertGreaterThan(0, $items[0]->memorymodule->memorytype_id);
    $type = \App\Models\Memorytype::find($items[0]->memorymodule->memorytype_id);
    $this->assertEquals('DDR3', $type->name, 'type 2 name not right');

    $this->assertEquals('DDR3', $items[2]->memorymodule->name, 'memory 3 name not right');
    $this->assertGreaterThan(0, $items[2]->memorymodule->memorytype_id);
    $type = \App\Models\Memorytype::find($items[2]->memorymodule->memorytype_id);
    $this->assertEquals('DDR3', $type->name, 'type 3 name not right');

    $this->assertEquals(4, \App\Models\Memorymodule::count(), 'must have 4 memory modules in db');

    $cnt = \App\Models\Memorymodule::where('memoryslot_id', 0)->count();
    $this->assertEquals(1, $cnt, 'must have only 1 memory module not attached');
  }

  public function testParseComplete(): void
  {
    \App\Models\Computer::truncate();

    $data = (object) [
      'CONTENT' => (object) [
        'MEMORIES' => [
          (object) [
            'CAPACITY'      => '32768',
            'CAPTION'       => 'DIMM 0',
            'DESCRIPTION'   => 'SODIMM',
            'MANUFACTURER'  => 'Ramaxel Technology',
            'MODEL'         => 'RMSA3330MF88HCF-3200',
            'NUMSLOTS'      => '1',
            'SERIALNUMBER'  => '113F261A',
            'SPEED'         => '3200',
            'TYPE'          => 'DDR4',
          ],
          (object) [
            'CAPTION'   => 'DIMM 0',
            'NUMSLOTS'  => '2',
          ],
        ],
        'HARDWARE' => (object) [
          'CHASSIS_TYPE'        => 'Notebook',
          'CHECKSUM'            => '131071',
          'DATELASTLOGGEDUSER'  => 'Fri May 16 17:14',
          'DESCRIPTION'         => 'amd64/-1-11-30 23:56:35',
          'DNS'                 => '192.168.188.87',
          'ETIME'               => '3',
          'IPADDR'              => '10.10.10.2/10.10.10.3',
          'LASTLOGGEDUSER'      => 'ddurieux',
          'MEMORY'              => '31491',
          'NAME'                => 'ddurieux_lpt_home',
          'OSCOMMENTS'          => 'FreeBSD 14.2-RELEASE-p1 GENERIC',
          'OSNAME'              => 'freebsd',
          'OSVERSION'           => '14.2-RELEASE-p1',
          'PROCESSORN'          => '1',
          'PROCESSORT'          => 'AMD Ryzen 5 PRO 7530U with Radeon Graphics',
          'SWAP'                => '8192',
          'USERID'              => 'ddurieux',
          'UUID'                => 'b742854c-2f60-11b2-a85c-f7752eacc9f0',
          'VMSYSTEM'            => 'Physical',
        ],
      ],
    ];

    $myData = [
      'name' => 'testMemories',
    ];
    $computer = \App\Models\Computer::create($myData);

    $computermemory = new \App\v1\Controllers\Fusioninventory\Computermemory($computer);
    $computermemory->parse($data);

    $computer->refresh();

    $items = $computer->memoryslots()->get();
    $this->assertCount(2, $items);

    $this->assertNotNull($items[0]['memorymodule']);

    $this->assertEquals(32768, $items[0]['memorymodule']->size);
    $this->assertEquals(3200, $items[0]['memorymodule']->frequence);
    $this->assertEquals('113F261A', $items[0]['memorymodule']->serial);
    $this->assertEquals('DDR4', $items[0]['memorymodule']->name);
    $this->assertEquals('Ramaxel Technology', $items[0]['memorymodule']->manufacturer->name);
    $this->assertEquals('RMSA3330MF88HCF-3200', $items[0]['memorymodule']->model->name);
    $this->assertEquals('DDR4', $items[0]['memorymodule']->type->name);

    $this->assertNull($items[1]['memorymodule']);
  }
}
