<?php

declare(strict_types=1);

namespace Tests\api\v1;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[UsesClass('\App\App')]
#[CoversClass('\App\Route')]
#[CoversClass('\App\v1\Controllers\Fusioninventory\Communication')]

final class FusioninventoryTest extends TestCase
{
  use HttpTestTrait;

  protected $app;

  protected function setUp(): void
  {
    $this->app = (new \App\App())->get();
  }

  public function testSendJsonError(): void
  {
    $data = [
      'REQUEST' => [
        'CONTENT' => [
          'HARDWARE' => [
            'NAME' => 'Test computer name',
          ],
        ],
      ],
    ];

    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/api/v1/fusioninventory',
      ['Content-Type' => 'application/json'],
      [],
      [],
      json_encode($data),
    );

    $response = $this->app->handle($request);

    $this->assertEquals(400, $response->getStatusCode());
    $xml = @simplexml_load_string((string) $response->getBody());
    $this->assertNotFalse($xml, 'answer not XML format');
    $this->assertEquals('Data format not right', $xml->ERROR);
    $this->assertEquals(
      'application/xml',
      $response->getHeaderLine('Content-Type'),
      'Response content-type must be application/xml'
    );
  }

  public function testSendEmptyData(): void
  {
    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/api/v1/fusioninventory',
      ['Content-Type' => 'application/xml'],
      [],
      [],
      ''
    );

    $response = $this->app->handle($request);

    $this->assertEquals(400, $response->getStatusCode());
    $xml = @simplexml_load_string((string) $response->getBody());
    $this->assertNotFalse($xml, 'answer not XML format');
    $this->assertEquals('Data not right', $xml->ERROR);
    $this->assertEquals(
      'application/xml',
      $response->getHeaderLine('Content-Type'),
      'Response content-type must be application/xml'
    );
  }

  public function testSendInventoryNoContent(): void
  {
    $xmlStr = '<?xml version="1.0" encoding="UTF-8" ?>
<REQUEST>
  <QUERY>INVENTORY</QUERY>
</REQUEST>';

    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/api/v1/fusioninventory',
      ['Content-Type' => 'application/xml'],
      [],
      [],
      $xmlStr
    );

    $response = $this->app->handle($request);

    $this->assertEquals(400, $response->getStatusCode());
    $xml = @simplexml_load_string((string) $response->getBody());
    $this->assertNotFalse($xml, 'answer not XML format');
    $this->assertEquals('Data not right', $xml->ERROR);
    $this->assertEquals(
      'application/xml',
      $response->getHeaderLine('Content-Type'),
      'Response content-type must be application/xml'
    );
  }

  public function testSendInventoryXmlNotWellFormed(): void
  {
    $xmlStr = '<?xml version="1.0" encoding="UTF-8" ?>
<REQUEST>
  <CONTENT>
    <HARDWARE>
      <NAME>Test computer</NAME
    </HARDWARE>
  </CONTENT>
  <QUERY>INVENTORY</QUERY>
</REQUEST>';

    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/api/v1/fusioninventory',
      ['Content-Type' => 'application/xml'],
      [],
      [],
      $xmlStr
    );

    $response = $this->app->handle($request);

    $this->assertEquals(400, $response->getStatusCode());
    $xml = @simplexml_load_string((string) $response->getBody());
    $this->assertNotFalse($xml, 'answer not XML format');
    $this->assertEquals('XML not well formed', $xml->ERROR);
    $this->assertEquals(
      'application/xml',
      $response->getHeaderLine('Content-Type'),
      'Response content-type must be application/xml'
    );
  }

  public function testSendInventory(): void
  {
    \App\Models\Computer::truncate();

    $xmlStr = '<?xml version="1.0" encoding="UTF-8" ?>
<REQUEST>
  <CONTENT>
    <BIOS>
      <ASSETTAG>31002544</ASSETTAG>
      <BDATE>06/25/2019</BDATE>
      <BMANUFACTURER>Dell Inc.</BMANUFACTURER>
      <BVERSION>A18</BVERSION>
      <MMANUFACTURER>Dell Inc.</MMANUFACTURER>
      <MMODEL>08HPGT</MMODEL>
      <MSN>/F2NPWX1/CN7220034A015J/</MSN>
      <SKUNUMBER />
      <SMANUFACTURER>Dell Inc.</SMANUFACTURER>
      <SMODEL>Precision T3600</SMODEL>
      <SSN>F2NPWX1</SSN>
    </BIOS>
    <HARDWARE>
      <CHASSIS_TYPE>Tower</CHASSIS_TYPE>
      <CHECKSUM>131071</CHECKSUM>
      <DEFAULTGATEWAY>192.168.0.1</DEFAULTGATEWAY>
      <DNS>8.8.8.8</DNS>
      <ETIME>52</ETIME>
      <IPADDR>192.168.0.10</IPADDR>
      <LASTLOGGEDUSER>ddurieux</LASTLOGGEDUSER>
      <MEMORY>65493</MEMORY>
      <NAME>DESKTOP-J5943E9</NAME>
      <OSNAME>Microsoft Windows 10 Pro</OSNAME>
      <OSVERSION>10.0.19045</OSVERSION>
      <PROCESSORN>1</PROCESSORN>
      <PROCESSORS>2600</PROCESSORS>
      <PROCESSORT>Intel(R) Xeon(R) CPU E5-2670 0 @ 2.60GHz</PROCESSORT>
      <USERID>ddurieux</USERID>
      <UUID>4C4C4544-0032-4E10-8050-C6C04F575831</UUID>
      <VMSYSTEM>Physical</VMSYSTEM>
      <WINLANG>2057</WINLANG>
      <WINOWNER>ddurieux</WINOWNER>
      <WORKGROUP>WORKGROUP</WORKGROUP>
    </HARDWARE>
  </CONTENT>
  <QUERY>INVENTORY</QUERY>
</REQUEST>';

    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/api/v1/fusioninventory',
      ['Content-Type' => 'application/xml'],
      [],
      [],
      $xmlStr
    );

    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());
    $expected = '<?xml version="1.0" encoding="UTF-8"?>
<REPLY/>
';
    $this->assertEquals($expected, (string) $response->getBody());

    $computers = \App\Models\Computer::get();
    $this->assertEquals(1, count($computers), 'Must have 1 computer');
    $this->assertEquals('DESKTOP-J5943E9', $computers[0]->name, 'Computer name not right');
    $this->assertEquals(
      'application/xml',
      $response->getHeaderLine('Content-Type'),
      'Response content-type must be application/xml'
    );
  }
}
