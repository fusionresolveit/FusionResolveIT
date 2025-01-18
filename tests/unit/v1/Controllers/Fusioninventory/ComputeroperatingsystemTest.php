<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers\FusionInventory;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThan;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computeroperatingsystem')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Operatingsystemedition')]
#[UsesClass('\App\Models\Definitions\Operatingsystemversion')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Common')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Validation')]

final class ComputeroperatingsystemTest extends TestCase
{
  public static function osProvider(): array
  {
    // NAME | VERSION | FULL_NAME | LTS | result: version | result: edition
    return [
      'Windows 10 pro' => [
        'Windows',
        '2009',
        'Microsoft Windows 10 Pro',
        false,
        '10',
        'Pro',
      ],
      'Windows 10 entreprise' => [
        'Windows',
        '1809',
        'Microsoft Windows 10 Entreprise LTSC',
        true,
        '10',
        'Entreprise LTSC',
      ],
      'Windows 7' => [
        'Windows',
        null,
        'Microsoft Windows 7 Professionnel ',
        false,
        '7',
        'Professionnel',
      ],
      'Windows 8' => [
        'Windows',
        null,
        'Microsoft Windows 8',
        false,
        '8',
        null,
      ],
      'Windows 8.1' => [
        'Windows',
        null,
        'Microsoft Windows 8.1 Entreprise',
        false,
        '8.1',
        'Entreprise',
      ],
      'Windows XP' => [
        'Windows',
        null,
        'Microsoft Windows XP Professionnel',
        false,
        'XP',
        'Professionnel',
      ],
      'Windows 11 entreprise' => [
        'Windows',
        '2009',
        'Microsoft Windows 11 Entreprise',
        false,
        '11',
        'Entreprise'
      ],
      'Windows vista' => [
        'Windows',
        null,
        'Microsoft® Windows Vista™ Professionnel ',
        false,
        'Vista',
        'Professionnel',
      ],
      'Windows Server 2003' => [
        'Windows',
        null,
        'Microsoft(R) Windows(R) Server 2003, Standard Edition x64',
        false,
        '2003',
        'Server Standard Edition',
      ],
      'Windows Server 2008' => [
        'Windows',
        null,
        'Microsoft® Windows Server® 2008 Entreprise ',
        false,
        '2008',
        'Server Entreprise',
      ],
      'Windows Server 2012' => [
        'Windows',
        null,
        'Microsoft Windows Server 2012 R2 Datacenter',
        false,
        '2012 R2',
        'Server Datacenter',
      ],
      'Hyper-V Server 2012 R2' => [
        'Windows',
        null,
        'Microsoft Hyper-V Server 2012 R2',
        false,
        '2012 R2',
        'Hyper-V Server',
      ],
      'Fedora 41' => [
        'Fedora Linux',
        null,
        'Fedora Linux 41 (Workstation Edition)',
        false,
        '41',
        'Workstation',
      ],
      'ubuntu 20.04' => [
        'Ubuntu',
        '20.04.4 LTS (Focal Fossa)',
        'Ubuntu 20.04.4 LTS',
        true,
        '20.04.4',
        null,
      ],
      'FreeBSD 14.1' => [
        'freebsd',
        '14.1-RELEASE-p4',
        'freebsd',
        false,
        '14.1',
        null,
      ],
      'empty fullname' => [
        'Windows',
        '1909',
        '',
        false,
        null,
        null,
      ],
    ];
  }

  #[DataProvider('osProvider')]
  public function testGetversion($name, $version, $full_name, $lts, $result, $resEdition): void
  {
    $data = (object) [
      'NAME'      => $name,
      'FULL_NAME' => $full_name
    ];
    if (!is_null($version)) {
      $data->VERSION = $version;
    }

    $versionId = \App\v1\Controllers\Fusioninventory\Computeroperatingsystem::getVersion($data);

    if (is_null($result)) {
      $this > assertEquals(0, $versionId, 'version id must 0');
    } else {
      $this > assertGreaterThan(0, $versionId, 'version id must > 0');
      $modelVersion = \App\Models\Operatingsystemversion::find($versionId);
      $this->assertEquals($result, $modelVersion->name, 'version not right');
      if ($lts === false)
      {
        $this->assertFalse($modelVersion->is_lts, 'LTS not right defined');
      } else {
        $this->assertTrue($modelVersion->is_lts, 'LTS not right defined');
      }
    }
  }

  // test getEdition
  #[DataProvider('osProvider')]
  public function testGetEdition($name, $version, $full_name, $lts, $resVersion, $result): void
  {
    $data = (object) [
      'NAME'      => $name,
      'FULL_NAME' => $full_name
    ];
    if (!is_null($version)) {
      $data->VERSION = $version;
    }

    $editionId = \App\v1\Controllers\Fusioninventory\Computeroperatingsystem::getEdition($data);

    if (is_null($result)) {
      $this > assertEquals(0, $editionId, 'edition id must 0');
    } else {
      $this > assertGreaterThan(0, $editionId, 'edition id must > 0');
      $editionEdition = \App\Models\Operatingsystemedition::find($editionId);
      $this->assertEquals($result, $editionEdition->name, 'edition not right');
    }
  }
}
