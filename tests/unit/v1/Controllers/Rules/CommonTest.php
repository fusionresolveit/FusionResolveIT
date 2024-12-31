<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass('\App\v1\Controllers\Rules\Common')]
#[UsesClass('\App\Translation')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Ticket')]
#[UsesClass('\App\Models\Ticket')]

final class CommonTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    \App\Models\Rules\Rule::create([
      'name'      => 'test',
      'sub_type'  => 'RuleTicket',
      'match'     => 'AND',
      'is_active' => true,
    ]);
  }

  public static function tearDownAfterClass(): void
  {
    // delete rules
    \App\Models\Rules\Rule::truncate();
  }

  public static function providerAction(): array
  {
    return [
      // field, data (data to update), ruledData (data updated after rules), expected
      'name changed' => ['name', ['name' => 'original test'], ['name' => 'my new title'], 'my new title'],
      'no name' => ['name', [], ['name' => 'my new title'], 'my new title'],
      'new requester' => ['requester', ['requester' => '4'], ['requester' => [5, 6]], '5,6'],
      'append requester' => ['requester', ['requester' => '4'], ['requester' => [4, 3]], '4,3'],
      'no requester' => ['requester', [], ['requester' => [5]], '5'],
      'same requester' => ['requester', ['requester' => '4'], ['requester' => [4]], '4'],
    ];
  }

  #[DataProvider('providerAction')]
  public function testParseNewData($field, $data, $ruledData, $expected): void
  {

    $ctrl = new \App\v1\Controllers\Rules\Common();
    $ticket = new \App\Models\Ticket();

    $ret = $ctrl->parseNewData($ticket, (object) $data, $ruledData);

    $this->assertEquals($expected, $ret->{$field});
  }
}
