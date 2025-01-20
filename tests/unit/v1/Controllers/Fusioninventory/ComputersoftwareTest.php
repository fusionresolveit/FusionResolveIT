<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers\FusionInventory;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computersoftware')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Computer')]
#[UsesClass('\App\Models\Ticket')]
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
#[UsesClass('\App\Models\Definitions\ItemSoftwareversion')]
#[UsesClass('\App\Models\Definitions\Itemdisk')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Manufacturer')]
#[UsesClass('\App\Models\Definitions\Network')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Operatingsystem')]
#[UsesClass('\App\Models\Definitions\Problem')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\Reservationitem')]
#[UsesClass('\App\Models\Definitions\Software')]
#[UsesClass('\App\Models\Definitions\State')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Software')]
#[UsesClass('\App\Models\Softwareversion')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Common')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Validation')]
#[UsesClass('\App\v1\Controllers\Log')]

final class ComputersoftwareTest extends TestCase
{
  public function testSoftwaresWithNoName(): void
  {
    // delete softwares
    \App\Models\Software::truncate();
    // delete software versionss
    \App\Models\Softwareversion::truncate();

    $myData = [
      'name' => 'testSoftwaresWithNoName',
    ];
    $computer = \App\Models\Computer::create($myData);

    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>FusionInventory Agent</GUID>
      <NAME>FusionInventory Agent</NAME>
      <PUBLISHER>FusionInventory Team</PUBLISHER>
      <UNINSTALL_STRING>C:\Program Files\FusionInventory-Agent\uninstFI.exe</UNINSTALL_STRING>
      <URL_INFO_ABOUT>http://www.FusionInventory.org</URL_INFO_ABOUT>
      <VERSION>2.2.7-4</VERSION>
    </SOFTWARES>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>HDMI</GUID>
      <NAME></NAME>
      <UNINSTALL_STRING>C:\WINDOWS\system32\igxpun.exe -uninstall</UNINSTALL_STRING>
    </SOFTWARES>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>7-Zip</GUID>
      <UNINSTALL_STRING>"C:\Program Files\7-Zip\Uninstall.exe"</UNINSTALL_STRING>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // check have only 1 software created

    $items = \App\Models\Software::get();

    $this->assertEquals(1, count($items), 'Must have only 1 software created');
    $this->assertEquals('FusionInventory Agent', $items[0]->name, 'name of the only software created is not right');
  }

  public function testSoftwareCreated(): void
  {
    // delete softwares
    \App\Models\Software::truncate();
    // delete software versionss
    \App\Models\Softwareversion::truncate();

    $myData = [
      'name' => 'testSoftwaresWithNoName',
    ];
    $computer = \App\Models\Computer::create($myData);

    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>FusionInventory Agent</GUID>
      <NAME>FusionInventory Agent</NAME>
      <PUBLISHER>FusionInventory Team</PUBLISHER>
      <UNINSTALL_STRING>C:\Program Files\FusionInventory-Agent\uninstFI.exe</UNINSTALL_STRING>
      <URL_INFO_ABOUT>http://www.FusionInventory.org</URL_INFO_ABOUT>
      <VERSION>2.2.7-4</VERSION>
      <INSTALLDATE>31/12/2013</INSTALLDATE>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // check one software created

    $items = \App\Models\Software::get();

    $this->assertEquals(1, count($items), 'one software must be present in database');
    $this->assertEquals('FusionInventory Agent', $items[0]->name, 'software name not same');
    $this->assertGreaterThan(0, $items[0]->manufacturer_id, 'Software manufacturer not same');
    $this->assertEquals('FusionInventory Team', $items[0]->manufacturer->name, 'manufacturer name not same');

    // check software version
    $itemversions = \App\Models\Softwareversion::get();
    $this->assertEquals(1, count($itemversions), 'one software version must be present in database');
    $this->assertEquals('2.2.7-4', $itemversions[0]->name, 'version name not same');
    $this->assertEquals($items[0]->id, $itemversions[0]->software_id, 'version not attached to software');

    // check software in computer
    $computer->refresh();

    $this->assertEquals(1, count($computer->softwareversions));
    $this->assertEquals('2.2.7-4', $computer->softwareversions[0]->name);
    $this->assertEquals($itemversions[0]->id, $computer->softwareversions[0]->id);
    $this->assertEquals('2013-12-31', $computer->softwareversions[0]->pivot->date_install);
  }

  public function testSoftwareUpdateWithNewVersion(): void
  {
    // delete softwares
    \App\Models\Software::truncate();
    // delete software versions
    \App\Models\Softwareversion::truncate();
    // delete linked table
    \App\Models\ItemSoftwareversion::truncate();

    $myData = [
      'name' => 'testSoftwaresWithNoName',
    ];
    $computer = \App\Models\Computer::create($myData);

    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>FusionInventory Agent</GUID>
      <NAME>FusionInventory Agent</NAME>
      <PUBLISHER>FusionInventory Team</PUBLISHER>
      <UNINSTALL_STRING>C:\Program Files\FusionInventory-Agent\uninstFI.exe</UNINSTALL_STRING>
      <URL_INFO_ABOUT>http://www.FusionInventory.org</URL_INFO_ABOUT>
      <VERSION>2.2.7-4</VERSION>
      <INSTALLDATE>31/12/2013</INSTALLDATE>
    </SOFTWARES>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>Branding</GUID>
      <NAME>Branding</NAME>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // second import with new version

    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>FusionInventory Agent</GUID>
      <NAME>FusionInventory Agent</NAME>
      <PUBLISHER>FusionInventory Team</PUBLISHER>
      <UNINSTALL_STRING>C:\Program Files\FusionInventory-Agent\uninstFI.exe</UNINSTALL_STRING>
      <URL_INFO_ABOUT>http://www.FusionInventory.org</URL_INFO_ABOUT>
      <VERSION>2.2.8</VERSION>
      <INSTALLDATE>31/12/2013</INSTALLDATE>
    </SOFTWARES>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>Branding</GUID>
      <NAME>Branding</NAME>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // check two softwares created

    $items = \App\Models\Software::get();

    $this->assertEquals(2, count($items), 'two softwares must be present in database');
    $this->assertEquals(1, $items[0]->id, 'software 1 id not same');
    $this->assertEquals('FusionInventory Agent', $items[0]->name, 'software 1 name not same');
    $this->assertEquals(2, $items[1]->id, 'software 2 id not same');
    $this->assertEquals('Branding', $items[1]->name, 'software 2 name not same');

    $this->assertGreaterThan(0, $items[0]->manufacturer_id, 'Software 1 manufacturer not same');
    $this->assertEquals('FusionInventory Team', $items[0]->manufacturer->name, 'manufacturer 1 name not same');
    $this->assertEquals(0, $items[1]->manufacturer_id, 'Software 2 manufacturer must be empty');

    // check software version
    $itemversions = \App\Models\Softwareversion::get();
    $this->assertEquals(3, count($itemversions), 'three software versions must be present in database');
    $this->assertEquals('2.2.7-4', $itemversions[0]->name, 'version 1 name not same');
    $this->assertEquals('N/A', $itemversions[1]->name, 'version 2 name not same');
    $this->assertEquals('2.2.8', $itemversions[2]->name, 'version 3 name not same');

    $this->assertEquals($items[0]->id, $itemversions[0]->software_id, 'version 1 not attached to software 1');
    $this->assertEquals($items[1]->id, $itemversions[1]->software_id, 'version 2 not attached to software 2');
    $this->assertEquals($items[0]->id, $itemversions[2]->software_id, 'version 3 not attached to software 1');

    // check software in computer
    $computer->refresh();

    $this->assertEquals(2, count($computer->softwareversions), 'must have 2 software versions associated to computer');
    $this->assertEquals('N/A', $computer->softwareversions[0]->name);
    $this->assertEquals('2.2.8', $computer->softwareversions[1]->name);

    // check only have 2 items in item_softwareversion (relations table)
    $itemlinks = \App\Models\ItemSoftwareversion::get();
    $this->assertEquals(2, count($itemlinks), 'must have 2 lines in relations table');
  }

  public function testSoftwareUpdateWithoutSoftwareAndWithNewSoftware(): void
  {
    // delete softwares
    \App\Models\Software::truncate();
    // delete software versions
    \App\Models\Softwareversion::truncate();
    // delete linked table
    \App\Models\ItemSoftwareversion::truncate();

    $myData = [
      'name' => 'testSoftwaresWithNoName',
    ];
    $computer = \App\Models\Computer::create($myData);

    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>FusionInventory Agent</GUID>
      <NAME>FusionInventory Agent</NAME>
      <PUBLISHER>FusionInventory Team</PUBLISHER>
      <UNINSTALL_STRING>C:\Program Files\FusionInventory-Agent\uninstFI.exe</UNINSTALL_STRING>
      <URL_INFO_ABOUT>http://www.FusionInventory.org</URL_INFO_ABOUT>
      <VERSION>2.2.7-4</VERSION>
      <INSTALLDATE>31/12/2013</INSTALLDATE>
    </SOFTWARES>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>Branding</GUID>
      <NAME>Branding</NAME>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // second import with new version

    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>{CE2CDD62-0124-36CA-84D3-9F4DCF5C5BD9}</GUID>
      <INSTALLDATE>09/10/2013</INSTALLDATE>
      <NAME>Microsoft .NET Framework 3.5 SP1</NAME>
      <PUBLISHER>Microsoft Corporation</PUBLISHER>
      <UNINSTALL_STRING>MsiExec.exe /I{CE2CDD62-0124-36CA-84D3-9F4DCF5C5BD9}</UNINSTALL_STRING>
      <VERSION>3.5.30729</VERSION>
    </SOFTWARES>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>Branding</GUID>
      <NAME>Branding</NAME>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // check three softwares created

    $items = \App\Models\Software::get();

    $this->assertEquals(3, count($items), 'three softwares must be present in database');
    $this->assertEquals(1, $items[0]->id, 'software 1 id not same');
    $this->assertEquals('FusionInventory Agent', $items[0]->name, 'software 1 name not same');
    $this->assertEquals(2, $items[1]->id, 'software 2 id not same');
    $this->assertEquals('Branding', $items[1]->name, 'software 2 name not same');
    $this->assertEquals(3, $items[2]->id, 'software 3 id not same');
    $this->assertEquals('Microsoft .NET Framework 3.5 SP1', $items[2]->name, 'software 3 name not same');

    $this->assertGreaterThan(0, $items[0]->manufacturer_id, 'Software 1 manufacturer not same');
    $this->assertEquals('FusionInventory Team', $items[0]->manufacturer->name, 'manufacturer 1 name not same');
    $this->assertEquals(0, $items[1]->manufacturer_id, 'Software 2 manufacturer must be empty');
    $this->assertGreaterThan(0, $items[2]->manufacturer_id, 'Software 3 manufacturer not same');
    $this->assertEquals('Microsoft Corporation', $items[2]->manufacturer->name, 'manufacturer 3 name not same');

    // check software version
    $itemversions = \App\Models\Softwareversion::get();
    $this->assertEquals(3, count($itemversions), 'three software versions must be present in database');
    $this->assertEquals('2.2.7-4', $itemversions[0]->name, 'version 1 name not same');
    $this->assertEquals('N/A', $itemversions[1]->name, 'version 2 name not same');
    $this->assertEquals('3.5.30729', $itemversions[2]->name, 'version 3 name not same');

    $this->assertEquals($items[0]->id, $itemversions[0]->software_id, 'version 1 not attached to software 1');
    $this->assertEquals($items[1]->id, $itemversions[1]->software_id, 'version 2 not attached to software 2');
    $this->assertEquals($items[2]->id, $itemversions[2]->software_id, 'version 3 not attached to software 3');

    // check software in computer
    $computer->refresh();

    $this->assertEquals(2, count($computer->softwareversions), 'must have 2 software versions associated to computer');
    $this->assertEquals('N/A', $computer->softwareversions[0]->name);
    $this->assertEquals('3.5.30729', $computer->softwareversions[1]->name);
    $this->assertEquals($items[2]->id, $computer->softwareversions[1]->software_id);

    // check only have 2 items in item_softwareversion (relations table)
    $itemlinks = \App\Models\ItemSoftwareversion::get();
    $this->assertEquals(2, count($itemlinks), 'must have 2 lines in relations table');
  }

  public function testSoftwareUpdatePivotField(): void
  {
    // delete softwares
    \App\Models\Software::truncate();
    // delete software versions
    \App\Models\Softwareversion::truncate();
    // delete linked table
    \App\Models\ItemSoftwareversion::truncate();

    $myData = [
      'name' => 'testSoftware pivot',
    ];
    $computer = \App\Models\Computer::create($myData);

    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>FusionInventory Agent</GUID>
      <NAME>FusionInventory Agent</NAME>
      <PUBLISHER>FusionInventory Team</PUBLISHER>
      <UNINSTALL_STRING>C:\Program Files\FusionInventory-Agent\uninstFI.exe</UNINSTALL_STRING>
      <URL_INFO_ABOUT>http://www.FusionInventory.org</URL_INFO_ABOUT>
      <VERSION>2.2.7-4</VERSION>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // date_install null
    $computer->refresh();
    $this->assertNull($computer->softwareversions[0]->pivot->date_install);

    // Update with date_install
    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>FusionInventory Agent</GUID>
      <NAME>FusionInventory Agent</NAME>
      <PUBLISHER>FusionInventory Team</PUBLISHER>
      <UNINSTALL_STRING>C:\Program Files\FusionInventory-Agent\uninstFI.exe</UNINSTALL_STRING>
      <URL_INFO_ABOUT>http://www.FusionInventory.org</URL_INFO_ABOUT>
      <VERSION>2.2.7-4</VERSION>
      <INSTALLDATE>31/12/2013</INSTALLDATE>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // date_install null
    $computer->refresh();
    $this->assertEquals('2013-12-31', $computer->softwareversions[0]->pivot->date_install);

    // Update again, but without date_install
    $softStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <SOFTWARES>
      <ARCH>i586</ARCH>
      <FROM>registry</FROM>
      <GUID>FusionInventory Agent</GUID>
      <NAME>FusionInventory Agent</NAME>
      <PUBLISHER>FusionInventory Team</PUBLISHER>
      <UNINSTALL_STRING>C:\Program Files\FusionInventory-Agent\uninstFI.exe</UNINSTALL_STRING>
      <URL_INFO_ABOUT>http://www.FusionInventory.org</URL_INFO_ABOUT>
      <VERSION>2.2.7-4</VERSION>
    </SOFTWARES>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($softStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computersoftware::parse($dataObj, $computer);

    // date_install null
    $computer->refresh();
    $this->assertNull($computer->softwareversions[0]->pivot->date_install);
  }
}
