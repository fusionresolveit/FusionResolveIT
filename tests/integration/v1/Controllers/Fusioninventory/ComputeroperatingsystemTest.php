<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThan;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computeroperatingsystem')]
#[CoversClass('\App\v1\Controllers\Fusioninventory\Common')]
#[CoversClass('\App\Models\Definitions\Operatingsystem')]
#[CoversClass('\App\Models\Definitions\Operatingsystemedition')]
#[CoversClass('\App\Models\Definitions\Operatingsystemversion')]
#[CoversClass('\App\Models\Definitions\Operatingsystemarchitecture')]
#[CoversClass('\App\Models\Definitions\Operatingsystemkernel')]
#[CoversClass('\App\Models\Definitions\Operatingsystemkernelversion')]
#[CoversClass('\App\Models\Operatingsystemkernelversion')]
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
#[UsesClass('\App\Models\Definitions\Problem')]
#[UsesClass('\App\Models\Definitions\Reservationitem')]
#[UsesClass('\App\Models\Definitions\State')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Devicememory')]
#[UsesClass('\App\Models\Problemtemplate')]
#[UsesClass('\App\Models\Software')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Validation')]
#[UsesClass('\App\Models\Definitions\Deviceprocessormodel')]
#[UsesClass('\App\Models\Definitions\ItemDeviceprocessor')]
#[UsesClass('\App\Models\Deviceprocessor')]

final class ComputeroperatingsystemTest extends TestCase
{
  public function testParse(): void
  {
    \App\Models\Computer::truncate();

    $data = (object) [
      'CONTENT' => (object) [
        'OPERATINGSYSTEM' => (object) [
          'ARCH'            => '64-bit',
          'BOOT_TIME'       => '2025-01-14 00:52:32',
          'DNS_DOMAIN'      => 'WORKGROUP',
          'FQDN'            => 'DESKTOP-J5943E9',
          'FULL_NAME'       => 'Microsoft Windows 10 Pro',
          'INSTALL_DATE'    => '2024-09-20 00:42:02',
          'KERNEL_NAME'     => 'MSWin32',
          'KERNEL_VERSION'  => '10.0.19045',
          'NAME'            => 'Windows',
          'SERVICE_PACK'    => null,
          'SSH_KEY'         => 'ssh-rsa AAAAB3NzaC1yc2Es=',
          'TIMEZONE'        => (object) [
            'NAME'    => 'Europe/Paris',
            'OFFSET'  => '+0100',
          ],
          'VERSION'         => '2009',
          'HOSTID'          => 'DD-TestID',
        ],
        'HARDWARE' => (object) [
          'CHASSIS_TYPE'    => 'Tower',
          'CHECKSUM'        => '131071',
          'DEFAULTGATEWAY'  => '192.168.0.1',
          'DNS'             => '8.8.8.8',
          'ETIME'           => '49',
          'IPADDR'          => '192.168.0.74',
          'LASTLOGGEDUSER'  => 'ddurieux',
          'MEMORY'          => '65493',
          'OSCOMMENTS'      => 'FreeBSD 10.3-RELEASE #0 r297264: Fri Mar 25 02:10:02 UTC 2016     root@releng1.nyi.' .
                               'freebsd.org:/usr/obj/usr/src/sys/GENERIC ',
          'NAME'            => 'DESKTOP-J5943E9',
          'OSNAME'          => 'Microsoft Windows 10 Pro',
          'OSVERSION'       => '10.0.19045',
          'PROCESSORN'      => '1',
          'PROCESSORS'      => '2600',
          'PROCESSORT'      => 'Intel(R) Xeon(R) CPU E5-2670 0 @ 2.60GHz',
          'USERID'          => 'ddurieux',
          'UUID'            => '4C4C4544-0032-4E10-8050-C6C04F575831',
          'VMSYSTEM'        => 'Physical',
          'WINLANG'         => '2057',
          'WINCOMPANY'      => 'GG Enterprise',
          'WINOWNER'        => 'ddurieux',
          'WINPRODID'       => '00330-73340-61453-AAOEM',
          'WINPRODKEY'      => 'YYYY-777DD-DVVRQ-HQ3DG-XXXX',
          'WORKGROUP'       => 'WORKGROUP',
        ],
      ],
    ];

    $myData = [
      'name' => 'testOperatingsystem',
    ];
    $computer = \App\Models\Computer::create($myData);

    \App\v1\Controllers\Fusioninventory\Computeroperatingsystem::parse($data, $computer);

    $computer->refresh();

    $items = $computer->operatingsystems()->get();
    $os = $items[0];

    $this->assertEquals('Windows', $os->name, 'os name not right');

    $this->assertGreaterThan(0, $os->pivot->operatingsystemarchitecture_id, 'architecture id must be > 0');
    $arch = \App\Models\Operatingsystemarchitecture::find($os->pivot->operatingsystemarchitecture_id);
    $this->assertEquals('64-bit', $arch->name, 'architecture name not right');

    $this->assertGreaterThan(0, $os->pivot->operatingsystemkernelversion_id, 'kernel version id must be > 0');
    $kVersion = \App\Models\Operatingsystemkernelversion::find($os->pivot->operatingsystemkernelversion_id);
    $this->assertEquals('10.0.19045', $kVersion->name, 'kernel version not right');
    $this->assertEquals('MSWin32', $kVersion->kernel->name, 'kernel name not right');

    $this->assertGreaterThan(0, $os->pivot->operatingsystemversion_id, 'version id must be > 0');
    $version = \App\Models\Operatingsystemversion::find($os->pivot->operatingsystemversion_id);
    $this->assertEquals('10', $version->name, 'version not right');

    $this->assertEquals(0, $os->pivot->operatingsystemservicepack_id, 'service pack id must be 0');

    $this->assertGreaterThan(0, $os->pivot->operatingsystemedition_id, 'edition id must be > 0');
    $edition = \App\Models\Operatingsystemedition::find($os->pivot->operatingsystemedition_id);
    $this->assertEquals('Pro', $edition->name, 'edition not right');

    $this->assertEquals('00330-73340-61453-AAOEM', $os->pivot->licenseid, 'product id / license id not right');
    $this->assertEquals(
      'YYYY-777DD-DVVRQ-HQ3DG-XXXX',
      $os->pivot->license_number,
      'serial number (license number) not right'
    );
    $this->assertEquals('GG Enterprise', $os->pivot->wincompany, 'company name not right');
    $this->assertEquals(
      'FreeBSD 10.3-RELEASE #0 r297264: Fri Mar 25 02:10:02 UTC 2016 root@releng1.nyi.freebsd' .
        '.org:/usr/obj/usr/src/sys/GENERIC',
      $os->pivot->oscomment,
      'os comment not right'
    );
    $this->assertEquals('DD-TestID', $os->pivot->hostid, 'host id not right');
    $this->assertEquals('ddurieux', $os->pivot->winowner, 'owner not right');
    $this->assertEquals('2024-09-20 00:42:02', $os->pivot->installationdate, 'installation date not right');
  }

  public function testParseNovalues(): void
  {
    \App\Models\Computer::truncate();

    $data = (object) [
      'CONTENT' => (object) [
        'OPERATINGSYSTEM' => (object) [
          'NAME'            => 'Windows',
        ],
        'HARDWARE' => (object) [
          'CHASSIS_TYPE'    => 'Tower',
          'CHECKSUM'        => '131071',
          'NAME'            => 'DESKTOP-J5943E9',
        ],
      ],
    ];

    $myData = [
      'name' => 'testOperatingsystem',
    ];
    $computer = \App\Models\Computer::create($myData);

    \App\v1\Controllers\Fusioninventory\Computeroperatingsystem::parse($data, $computer);

    $computer->refresh();

    $items = $computer->operatingsystems()->get();
    $os = $items[0];

    $this->assertEquals('Windows', $os->name, 'os name not right');
    $this->assertEquals(0, $os->pivot->operatingsystemarchitecture_id, 'architecture id must be 0');
    $this->assertEquals(0, $os->pivot->operatingsystemkernelversion_id, 'kernel version id must be 0');
    $this->assertEquals(0, $os->pivot->operatingsystemversion_id, 'version id must be 0');
    $this->assertEquals(0, $os->pivot->operatingsystemservicepack_id, 'service pack id must be 0');
    $this->assertEquals(0, $os->pivot->operatingsystemedition_id, 'edition id must be 0');
    $this->assertNull($os->pivot->licenseid, 'product id / license id must be null');
    $this->assertNull($os->pivot->license_number, 'serial number (license number) must be null');
    $this->assertNull($os->pivot->wincompany, 'company name must be null');
    $this->assertNull($os->pivot->oscomment, 'os comment must be null');
    $this->assertNull($os->pivot->hostid, 'host id must be null');
    $this->assertNull($os->pivot->winowner, 'owner must be null');
    $this->assertNull($os->pivot->installationdate, 'installation date must be null');
  }

  public function testParseNoOperatigsystemNode(): void
  {
    \App\Models\Computer::truncate();

    $data = (object) [
      'CONTENT' => (object) [
        'HARDWARE' => (object) [
          'CHASSIS_TYPE'    => 'Tower',
          'CHECKSUM'        => '131071',
          'NAME'            => 'DESKTOP-J5943E9',
        ],
      ],
    ];

    $myData = [
      'name' => 'testOperatingsystem',
    ];
    $computer = \App\Models\Computer::create($myData);

    \App\v1\Controllers\Fusioninventory\Computeroperatingsystem::parse($data, $computer);

    $computer->refresh();

    $items = $computer->operatingsystems()->get();
    $this->assertEquals(0, count($items), 'Must not have operatingsystem');
  }
}
