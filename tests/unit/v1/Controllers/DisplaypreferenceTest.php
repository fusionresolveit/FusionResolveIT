<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass('\App\v1\Controllers\Displaypreference')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\ProfileUser')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Location')]
#[UsesClass('\App\Models\Profile')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\v1\Controllers\Log')]

final class DisplaypreferenceTest extends TestCase
{
  protected function setUp(): void
  {
    // num, rank, user_id
    $data = [
      [40, 10, 50],
      [1, 6, 50],
      [42, 1, 0],
      [40, 10, 0],
      [20, 4, 0],
      [1004, 2, 0],
      [25, 11, 0],
      [98, 7, 0],
      [5, 8, 0],
      [1, 6, 0],
      [3, 12, 0],
    ];

    foreach ($data as $d)
    {
      $myData = [
        'itemtype' => '\App\Models\Testdisplaypref',
        'num'      => $d[0],
        'rank'     => $d[1],
        'user_id'  => $d[2],
      ];
      \App\Models\Displaypreference::create($myData);
    }
  }

  protected function tearDown(): void
  {
    $items = \App\Models\Displaypreference::where('itemtype', '\App\Models\Testdisplaypref')->get();
    foreach ($items as $item)
    {
      $item->delete();
    }
  }

  public function testDataInDatabase()
  {
    $items = \App\Models\Displaypreference::where('itemtype', '\App\Models\Testdisplaypref')->get();
    $this->assertEquals(11, count($items), 'Must have 9 entries in database for the test');
  }

  public function testUpColumn1(): void
  {
    $dpref = new \App\v1\Controllers\Displaypreference();
    $reflection = new \ReflectionClass($dpref);
    $method = $reflection->getMethod('upColumn');
    $method->setAccessible(true);

    $myDisplayPref = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('num', 1)
      ->where('user_id', 0)
      ->first();
    $this->assertNotNull($myDisplayPref, 'The entry in database is missing');

    $method->invoke($dpref, $myDisplayPref);

    // Verify all entries in database for user 0
    $items = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('user_id', 0)
      ->orderBy('id')
      ->get();
    $data = [$items[0]->num, $items[0]->rank];
    $this->assertEquals([42, 1], $data, '[ num, rank ]');

    $data = [$items[1]->num, $items[1]->rank];
    $this->assertEquals([40, 10], $data, '[ num, rank ]');

    $data = [$items[2]->num, $items[2]->rank];
    $this->assertEquals([20, 6], $data, '[ num, rank ]');

    $data = [$items[3]->num, $items[3]->rank];
    $this->assertEquals([1004, 2], $data, '[ num, rank ]');

    $data = [$items[4]->num, $items[4]->rank];
    $this->assertEquals([25, 11], $data, '[ num, rank ]');

    $data = [$items[5]->num, $items[5]->rank];
    $this->assertEquals([98, 7], $data, '[ num, rank ]');

    $data = [$items[6]->num, $items[6]->rank];
    $this->assertEquals([5, 8], $data, '[ num, rank ]');

    $data = [$items[7]->num, $items[7]->rank];
    $this->assertEquals([1, 4], $data, '[ num, rank ]');

    $data = [$items[8]->num, $items[8]->rank];
    $this->assertEquals([3, 12], $data, '[ num, rank ]');

    // Verify all entries in database for user 50
    $items = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('user_id', 50)
      ->orderBy('id')
      ->get();
    $data = [$items[0]->num, $items[0]->rank];
    $this->assertEquals([40, 10], $data, '[ num, rank ]');

    $data = [$items[1]->num, $items[1]->rank];
    $this->assertEquals([1, 6], $data, '[ num, rank ]');
  }

  public function testUpColumn2(): void
  {
    $dpref = new \App\v1\Controllers\Displaypreference();
    $reflection = new \ReflectionClass($dpref);
    $method = $reflection->getMethod('upColumn');
    $method->setAccessible(true);

    $myDisplayPref = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('num', 98)
      ->where('user_id', 0)
      ->first();
    $this->assertNotNull($myDisplayPref, 'The entry in database is missing');

    $method->invoke($dpref, $myDisplayPref);

    // Verify all entries in database for user 0
    $items = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('user_id', 0)
      ->orderBy('id')
      ->get();
    $data = [$items[0]->num, $items[0]->rank];
    $this->assertEquals([42, 1], $data, '[ num, rank ]');

    $data = [$items[1]->num, $items[1]->rank];
    $this->assertEquals([40, 10], $data, '[ num, rank ]');

    $data = [$items[2]->num, $items[2]->rank];
    $this->assertEquals([20, 4], $data, '[ num, rank ]');

    $data = [$items[3]->num, $items[3]->rank];
    $this->assertEquals([1004, 2], $data, '[ num, rank ]');

    $data = [$items[4]->num, $items[4]->rank];
    $this->assertEquals([25, 11], $data, '[ num, rank ]');

    $data = [$items[5]->num, $items[5]->rank];
    $this->assertEquals([98, 6], $data, '[ num, rank ]');

    $data = [$items[6]->num, $items[6]->rank];
    $this->assertEquals([5, 8], $data, '[ num, rank ]');

    $data = [$items[7]->num, $items[7]->rank];
    $this->assertEquals([1, 7], $data, '[ num, rank ]');

    $data = [$items[8]->num, $items[8]->rank];
    $this->assertEquals([3, 12], $data, '[ num, rank ]');

    // Verify all entries in database for user 50
    $items = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('user_id', 50)
      ->orderBy('id')
      ->get();
    $data = [$items[0]->num, $items[0]->rank];
    $this->assertEquals([40, 10], $data, '[ num, rank ]');

    $data = [$items[1]->num, $items[1]->rank];
    $this->assertEquals([1, 6], $data, '[ num, rank ]');
  }

  public function testDownColumn(): void
  {
    $dpref = new \App\v1\Controllers\Displaypreference();
    $reflection = new \ReflectionClass($dpref);
    $method = $reflection->getMethod('downColumn');
    $method->setAccessible(true);

    $myDisplayPref = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('num', 1)
      ->where('user_id', 0)
      ->first();
    $this->assertNotNull($myDisplayPref, 'The entry in database is missing');

    $method->invoke($dpref, $myDisplayPref);

    // Verify all entries in database for user 0
    $items = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('user_id', 0)
      ->orderBy('id')
      ->get();
    $data = [$items[0]->num, $items[0]->rank];
    $this->assertEquals([42, 1], $data, '[ num, rank ]');

    $data = [$items[1]->num, $items[1]->rank];
    $this->assertEquals([40, 10], $data, '[ num, rank ]');

    $data = [$items[2]->num, $items[2]->rank];
    $this->assertEquals([20, 4], $data, '[ num, rank ]');

    $data = [$items[3]->num, $items[3]->rank];
    $this->assertEquals([1004, 2], $data, '[ num, rank ]');

    $data = [$items[4]->num, $items[4]->rank];
    $this->assertEquals([25, 11], $data, '[ num, rank ]');

    $data = [$items[5]->num, $items[5]->rank];
    $this->assertEquals([98, 6], $data, '[ num, rank ]');

    $data = [$items[6]->num, $items[6]->rank];
    $this->assertEquals([5, 8], $data, '[ num, rank ]');

    $data = [$items[7]->num, $items[7]->rank];
    $this->assertEquals([1, 7], $data, '[ num, rank ]');

    $data = [$items[8]->num, $items[8]->rank];
    $this->assertEquals([3, 12], $data, '[ num, rank ]');

    // Verify all entries in database for user 50
    $items = \App\Models\Displaypreference::
        where('itemtype', '\App\Models\Testdisplaypref')
      ->where('user_id', 50)
      ->orderBy('id')
      ->get();
    $data = [$items[0]->num, $items[0]->rank];
    $this->assertEquals([40, 10], $data, '[ num, rank ]');

    $data = [$items[1]->num, $items[1]->rank];
    $this->assertEquals([1, 6], $data, '[ num, rank ]');
  }
}
