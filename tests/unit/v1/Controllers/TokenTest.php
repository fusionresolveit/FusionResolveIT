<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass('\App\v1\Controllers\Token')]

final class TokenTest extends TestCase
{
  public static function checkPawwsordProvider(): array
  {
    return [
      [
        'adminIT',
        'c58a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933ea788ce51e0336a15dc597f92d71d18f',
        true
      ],
      [
        'admin IT',
        'c58a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933ea788ce51e0336a15dc597f92d71d18f',
        false
      ],
      [
        'adminIT',
        'c88a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933ea788ce51e0336a15dc597f92d71d18f',
        false
      ],
      [
        'adminIT',
        'c58a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933eb788ce51e0336a15dc597f92d71d18f',
        false
      ],
      ['adminIT', 'c58a5addefacdd3629c6a960ba7f5a08.', false],
      ['adminIT', null, false],
      ['adminIT', '', false],
      ['adminIT', '.', false],
      [
        'adminIT',
        'c58a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933ea788ce51e0336a15dc597f92d71d18f.test',
        false
      ],
    ];
  }

  #[DataProvider('checkPawwsordProvider')]
  public function testCheckPassword($password, $hash, $expected)
  {
    $state = \App\v1\Controllers\Token::checkPassword($password, $hash);
    $this->assertEquals($expected, $state);
  }
}
