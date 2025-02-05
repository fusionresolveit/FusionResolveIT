<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers\FusionInventory;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Common')]

final class CommonTest extends TestCase
{
  public function testXmlToObjOk(): void
  {
    // Load computer inventory
    $value = file_get_contents(__DIR__ . '/Fixtures/computer1.xml');

    $ret = \App\v1\Controllers\Fusioninventory\Common::xmlToObj($value);

    $this->assertInstanceOf('SimpleXMLElement', $ret);
  }

  public function testXmlToObjError(): void
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('XML not well formed');

    // Load computer inventory
    $value = file_get_contents(__DIR__ . '/Fixtures/badxml.xml');

    \App\v1\Controllers\Fusioninventory\Common::xmlToObj($value);
  }

  public static function stringsProvider(): array
  {
    return [
      ['my   test    multiple spaces ', 'my test multiple spaces'],
      [' have null value', 'have null value'],
      ['Microsoft® Windows Server® 2008 Entreprise ', 'Microsoft Windows Server 2008 Entreprise']
    ];
  }

  #[DataProvider('stringsProvider')]
  public function testCleanString($string, $expected): void
  {
    $ret = \App\v1\Controllers\Fusioninventory\Common::cleanString($string);
    $this->assertEquals($expected, $ret, 'string not cleanned like wanted');
  }
}
