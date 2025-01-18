<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers\FusionInventory;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass('\App\v1\Controllers\Fusioninventory\Computerprocessor')]
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
#[UsesClass('\App\Models\Definitions\Deviceprocessormodel')]
#[UsesClass('\App\Models\Definitions\Devicesensor')]
#[UsesClass('\App\Models\Definitions\Devicesimcard')]
#[UsesClass('\App\Models\Definitions\Devicesoundcard')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Domain')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Infocom')]
#[UsesClass('\App\Models\Definitions\ItemDevicememory')]
#[UsesClass('\App\Models\Definitions\ItemDeviceprocessor')]
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
#[UsesClass('\App\Models\Deviceprocessor')]
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

final class ComputerprocessorTest extends TestCase
{
  public static function cpuSerialsProvider(): array
  {
    // serial cpu 1, serial cpu 2, second inventory serial cpu1, second inv. serial cpu2, max id pivot table
    return [
      'no serial for both cpus, data #1' => [null, null, null, null, 2],
      'no serial for both cpus, data #2' => [null, null, 'XH493KUU', null, 3],
      'no serial for both cpus, data #3' => [null, null, null, 'XH493KUV', 3],
      'no serial for both cpus, data #4' => [null, null, 'XH493KUU', 'XH493KUV', 4],

      'serial for first cpu, data #1' => ['XH493KUU', null, null, null, 3],
      'serial for first cpu, data #2' => ['XH493KUU', null, 'XH493KUU', null, 2],
      'serial for first cpu, data #3' => ['XH493KUU', null, null, 'XH493KUV', 3],
      'serial for first cpu, data #4' => ['XH493KUU', null, 'XH493KUU', 'XH493KUV', 3],
      'serial for first cpu, data #5' => ['XH493KUU', null, 'XH493KUX', 'XH493KUV', 4],
      'serial for first cpu, data #6' => ['XH493KUU', null, 'XH493KUV', 'XH493KUU', 3],

      'serial for second cpu, data #1' => [null, 'XH493KUV', null, null, 3],
      'serial for second cpu, data #2' => [null, 'XH493KUV', 'XH493KUU', null, 3],
      'serial for second cpu, data #3' => [null, 'XH493KUV', null, 'XH493KUV', 2],
      'serial for second cpu, data #4' => [null, 'XH493KUV', 'XH493KUU', 'XH493KUV', 3],
      'serial for second cpu, data #5' => [null, 'XH493KUV', 'XH493KUU', 'XH493KUX', 4],
      'serial for second cpu, data #6' => [null, 'XH493KUV', 'XH493KUV', 'XH493KUU', 3],

      'serial for both cpu, data #1' => ['XH493KUU', 'XH493KUV', null, null, 4],
      'serial for both cpu, data #2' => ['XH493KUU', 'XH493KUV', 'XH493KUU', null, 3],
      'serial for both cpu, data #3' => ['XH493KUU', 'XH493KUV', null, 'XH493KUV', 3],
      'serial for both cpu, data #4' => ['XH493KUU', 'XH493KUV', 'XH493KUU', 'XH493KUV', 2],
      'serial for both cpu, data #5' => ['XH493KUU', 'XH493KUV', 'XH493KUV', 'XH493KUU', 2],
      'serial for both cpu, data #6' => ['XH493KUU', 'XH493KUV', 'XH493KUX', 'XH493KUZ', 4],
    ];
  }

  public function testCpusWithNoName(): void
  {
    // delete processors
    \App\Models\Deviceprocessor::truncate();

    $myData = [
      'name' => 'testCpusWithNoName',
    ];
    $computer = \App\Models\Computer::create($myData);
    $cpuStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <CPUS>
      <CORE>2</CORE>
      <DESCRIPTION>x86 Family 6 Model 23 Stepping 10</DESCRIPTION>
      <FAMILYNUMBER>6</FAMILYNUMBER>
      <ID>7A 06 01 00 FF FB EB BF</ID>
      <MANUFACTURER>Intel</MANUFACTURER>
      <MODEL>23</MODEL>
      <SERIAL>ToBeFilledByO.E.M.</SERIAL>
      <SPEED>2600</SPEED>
      <STEPPING>10</STEPPING>
      <THREAD>2</THREAD>
    </CPUS>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($cpuStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computerprocessor::parse($dataObj, $computer);

    // check no processor created

    $items = \App\Models\Deviceprocessor::get();

    $this->assertEquals(0, count($items), 'No CPU mus tbe created because NAME attribute not present');
  }

  public function testCpuCreated(): void
  {
    // delete processors
    \App\Models\Deviceprocessor::truncate();

    $myData = [
      'name' => 'testCpuCreated',
    ];
    $computer = \App\Models\Computer::create($myData);
    $cpuStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <CPUS>
      <CORE>2</CORE>
      <DESCRIPTION>x86 Family 6 Model 23 Stepping 10</DESCRIPTION>
      <FAMILYNUMBER>6</FAMILYNUMBER>
      <ID>7A 06 01 00 FF FB EB BF</ID>
      <MANUFACTURER>Intel</MANUFACTURER>
      <MODEL>23</MODEL>
      <NAME>Pentium(R) Dual-Core  CPU      E5300  @ 2.60GHz</NAME>
      <SERIAL>ToBeFilledByO.E.M.</SERIAL>
      <SPEED>2600</SPEED>
      <STEPPING>10</STEPPING>
      <THREAD>2</THREAD>
    </CPUS>
  </CONTENT>
</REQUEST>';

    $dataXMLEl = simplexml_load_string($cpuStr);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computerprocessor::parse($dataObj, $computer);

    // check no processor created

    $items = \App\Models\Deviceprocessor::get();

    $this->assertEquals(1, count($items), 'one processor muste be present in database');
    $this->assertEquals('Pentium(R) Dual-Core CPU E5300 @ 2.60GHz', $items[0]->name, 'Processor name not same');
    $this->assertGreaterThan(0, $items[0]->manufacturer_id, 'Processor manufacturer not same');
    $this->assertEquals('7A060100FFFBEBBF', $items[0]->cpuid, 'Processor cpuid not same');
    $this->assertEquals(2600, $items[0]->frequence, 'Processor frequence not same');
    $this->assertEquals('x86 Family 6 Model 23 Stepping 10', $items[0]->comment, 'Processor comment not same');
    $this->assertEquals(10, $items[0]->stepping, 'Processor stepping not same');
    $this->assertEquals(2, $items[0]->nbcores_default, 'Processor nbcores_default not same');
    $this->assertEquals(2, $items[0]->nbthreads_default, 'Processor nbthreads_default not same');

    // check processor in computer
    $computer->refresh();

    $this->assertEquals(1, count($computer->processors));
    $this->assertEquals('Pentium(R) Dual-Core CPU E5300 @ 2.60GHz', $computer->processors[0]->name);
    $this->assertEquals('7A060100FFFBEBBF', $computer->processors[0]->cpuid);
  }

  #[DataProvider('cpuSerialsProvider')]
  public function testCpuDouble($serial1pass1, $serial2pass1, $serial1pass2, $serial2pass2, $maxId): void
  {
    // delete computers
    \App\Models\Computer::truncate();

    // delete item processor
    \App\Models\ItemDeviceprocessor::truncate();

    // delete processors
    \App\Models\Deviceprocessor::truncate();

    $myData = [
      'name' => 'testCpu',
    ];
    $computer = \App\Models\Computer::create($myData);
    $cpuStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <CPUS>
      <CORE>2</CORE>
      <DESCRIPTION>x86 Family 6 Model 23 Stepping 10</DESCRIPTION>
      <FAMILYNUMBER>6</FAMILYNUMBER>
      <ID>7A 06 01 00 FF FB EB BF</ID>
      <MANUFACTURER>Intel</MANUFACTURER>
      <MODEL>23</MODEL>
      <NAME>Pentium(R) Dual-Core  CPU      E5300  @ 2.60GHz</NAME>
      <SERIAL>[[SERIAL1]]</SERIAL>
      <SPEED>2600</SPEED>
      <STEPPING>10</STEPPING>
      <THREAD>2</THREAD>
    </CPUS>
    <CPUS>
      <CORE>2</CORE>
      <DESCRIPTION>x86 Family 6 Model 23 Stepping 10</DESCRIPTION>
      <FAMILYNUMBER>6</FAMILYNUMBER>
      <ID>7A 06 01 00 FF FB EB BF</ID>
      <MANUFACTURER>Intel</MANUFACTURER>
      <MODEL>23</MODEL>
      <NAME>Pentium(R) Dual-Core  CPU      E5300  @ 2.60GHz</NAME>
      <SERIAL>[[SERIAL2]]</SERIAL>
      <SPEED>2600</SPEED>
      <STEPPING>10</STEPPING>
      <THREAD>2</THREAD>
    </CPUS>
  </CONTENT>
</REQUEST>';

    // ***** FIRST INVENTORY ***** //
    $cpuStr1 = $cpuStr;
    if (is_null($serial1pass1))
    {
      $cpuStr1 = str_replace('[[SERIAL1]]', '', $cpuStr1);
    } else {
      $cpuStr1 = str_replace('[[SERIAL1]]', $serial1pass1, $cpuStr1);
    }
    if (is_null($serial2pass1))
    {
      $cpuStr1 = str_replace('[[SERIAL2]]', '', $cpuStr1);
    } else {
      $cpuStr1 = str_replace('[[SERIAL2]]', $serial2pass1, $cpuStr1);
    }

    $dataXMLEl = simplexml_load_string($cpuStr1);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computerprocessor::parse($dataObj, $computer);

    $items = \App\Models\Deviceprocessor::get();

    $this->assertEquals(1, count($items), '1 processor must be present in database');
    $this->assertEquals('Pentium(R) Dual-Core CPU E5300 @ 2.60GHz', $items[0]->name, 'Processor name not same');
    $this->assertGreaterThan(0, $items[0]->manufacturer_id, 'Processor manufacturer not same');
    $this->assertEquals('7A060100FFFBEBBF', $items[0]->cpuid, 'Processor cpuid not same');
    $this->assertEquals(2600, $items[0]->frequence, 'Processor frequence not same');
    $this->assertEquals('x86 Family 6 Model 23 Stepping 10', $items[0]->comment, 'Processor comment not same');
    $this->assertEquals(10, $items[0]->stepping, 'Processor stepping not same');
    $this->assertEquals(2, $items[0]->nbcores_default, 'Processor nbcores_default not same');
    $this->assertEquals(2, $items[0]->nbthreads_default, 'Processor nbthreads_default not same');

    // check processor in computer
    $computer->refresh();

    $this->assertEquals(2, count($computer->processors), 'Must have 2 processors attached to computer');
    $this->assertEquals($serial1pass1, $computer->processors[0]->pivot->serial, 'Processor 1 serial not same');
    $this->assertEquals($serial2pass1, $computer->processors[1]->pivot->serial, 'Processor 2 serial not same');

    // ***** SECOND INVENTORY ***** //
    $cpuStr2 = $cpuStr;
    if (is_null($serial1pass2))
    {
      $cpuStr2 = str_replace('[[SERIAL1]]', '', $cpuStr2);
    } else {
      $cpuStr2 = str_replace('[[SERIAL1]]', $serial1pass2, $cpuStr2);
    }
    if (is_null($serial2pass2))
    {
      $cpuStr2 = str_replace('[[SERIAL2]]', '', $cpuStr2);
    } else {
      $cpuStr2 = str_replace('[[SERIAL2]]', $serial2pass2, $cpuStr2);
    }

    $dataXMLEl = simplexml_load_string($cpuStr2);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computerprocessor::parse($dataObj, $computer);

    $items = \App\Models\Deviceprocessor::get();

    $this->assertEquals(1, count($items), '[pass2] 1 processor must be present in database');
    $this->assertEquals('Pentium(R) Dual-Core CPU E5300 @ 2.60GHz', $items[0]->name, '[pass2] Processor name not same');
    $this->assertGreaterThan(0, $items[0]->manufacturer_id, '[pass2] Processor manufacturer not same');
    $this->assertEquals('7A060100FFFBEBBF', $items[0]->cpuid, '[pass2] Processor cpuid not same');
    $this->assertEquals(2600, $items[0]->frequence, '[pass2] Processor frequence not same');
    $this->assertEquals('x86 Family 6 Model 23 Stepping 10', $items[0]->comment, '[pass2] Processor comment not same');
    $this->assertEquals(10, $items[0]->stepping, '[pass2] Processor stepping not same');
    $this->assertEquals(2, $items[0]->nbcores_default, '[pass2] Processor nbcores_default not same');
    $this->assertEquals(2, $items[0]->nbthreads_default, '[pass2] Processor nbthreads_default not same');

    // check processor in computer
    $computer->refresh();

    $this->assertEquals(2, count($computer->processors), '[pass2] Must have 2 processors attached to computer');
    $this->assertEqualsCanonicalizing(
      [$serial1pass2, $serial2pass2],
      [$computer->processors[0]->pivot->serial, $computer->processors[1]->pivot->serial],
      '[pass2] processors serial not same'
    );

    foreach ($computer->processors as $proc)
    {
      $this->assertLessThanOrEqual($maxId, $proc->pivot->id, 'pivot Id is too high');
    }
  }

  #[DataProvider('cpuSerialsProvider')]
  public function testCpuDoubleToOne($serial1pass1, $serial2pass1, $serial1pass2, $serial2pass2, $maxId): void
  {
    // delete computers
    \App\Models\Computer::truncate();

    // delete item processor
    \App\Models\ItemDeviceprocessor::truncate();

    // delete processors
    \App\Models\Deviceprocessor::truncate();

    $myData = [
      'name' => 'testCpu',
    ];
    $computer = \App\Models\Computer::create($myData);
    $cpuStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <CPUS>
      <CORE>2</CORE>
      <DESCRIPTION>x86 Family 6 Model 23 Stepping 10</DESCRIPTION>
      <FAMILYNUMBER>6</FAMILYNUMBER>
      <ID>7A 06 01 00 FF FB EB BF</ID>
      <MANUFACTURER>Intel</MANUFACTURER>
      <MODEL>23</MODEL>
      <NAME>Pentium(R) Dual-Core  CPU      E5300  @ 2.60GHz</NAME>
      <SERIAL>[[SERIAL1]]</SERIAL>
      <SPEED>2600</SPEED>
      <STEPPING>10</STEPPING>
      <THREAD>2</THREAD>
    </CPUS>
    <CPUS>
      <CORE>2</CORE>
      <DESCRIPTION>x86 Family 6 Model 23 Stepping 10</DESCRIPTION>
      <FAMILYNUMBER>6</FAMILYNUMBER>
      <ID>7A 06 01 00 FF FB EB BF</ID>
      <MANUFACTURER>Intel</MANUFACTURER>
      <MODEL>23</MODEL>
      <NAME>Pentium(R) Dual-Core  CPU      E5300  @ 2.60GHz</NAME>
      <SERIAL>[[SERIAL2]]</SERIAL>
      <SPEED>2600</SPEED>
      <STEPPING>10</STEPPING>
      <THREAD>2</THREAD>
    </CPUS>
  </CONTENT>
</REQUEST>';

    // ***** FIRST INVENTORY ***** //
    $cpuStr1 = $cpuStr;
    if (is_null($serial1pass1))
    {
      $cpuStr1 = str_replace('[[SERIAL1]]', '', $cpuStr1);
    } else {
      $cpuStr1 = str_replace('[[SERIAL1]]', $serial1pass1, $cpuStr1);
    }
    if (is_null($serial2pass1))
    {
      $cpuStr1 = str_replace('[[SERIAL2]]', '', $cpuStr1);
    } else {
      $cpuStr1 = str_replace('[[SERIAL2]]', $serial2pass1, $cpuStr1);
    }

    $dataXMLEl = simplexml_load_string($cpuStr1);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computerprocessor::parse($dataObj, $computer);

    $items = \App\Models\Deviceprocessor::get();

    $this->assertEquals(1, count($items), '1 processor must be present in database');
    $this->assertEquals('Pentium(R) Dual-Core CPU E5300 @ 2.60GHz', $items[0]->name, 'Processor name not same');
    $this->assertGreaterThan(0, $items[0]->manufacturer_id, 'Processor manufacturer not same');
    $this->assertEquals('7A060100FFFBEBBF', $items[0]->cpuid, 'Processor cpuid not same');
    $this->assertEquals(2600, $items[0]->frequence, 'Processor frequence not same');
    $this->assertEquals('x86 Family 6 Model 23 Stepping 10', $items[0]->comment, 'Processor comment not same');
    $this->assertEquals(10, $items[0]->stepping, 'Processor stepping not same');
    $this->assertEquals(2, $items[0]->nbcores_default, 'Processor nbcores_default not same');
    $this->assertEquals(2, $items[0]->nbthreads_default, 'Processor nbthreads_default not same');

    // check processor in computer
    $computer->refresh();

    $this->assertEquals(2, count($computer->processors), 'Must have 2 processors attached to computer');
    $this->assertEquals($serial1pass1, $computer->processors[0]->pivot->serial, 'Processor 1 serial not same');
    $this->assertEquals($serial2pass1, $computer->processors[1]->pivot->serial, 'Processor 2 serial not same');

    // ***** SECOND INVENTORY ***** //
    $cpuStr = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <CONTENT>
    <CPUS>
      <CORE>2</CORE>
      <DESCRIPTION>x86 Family 6 Model 23 Stepping 10</DESCRIPTION>
      <FAMILYNUMBER>6</FAMILYNUMBER>
      <ID>7A 06 01 00 FF FB EB BF</ID>
      <MANUFACTURER>Intel</MANUFACTURER>
      <MODEL>23</MODEL>
      <NAME>Pentium(R) Dual-Core  CPU      E5300  @ 2.60GHz</NAME>
      <SERIAL>[[SERIAL1]]</SERIAL>
      <SPEED>2600</SPEED>
      <STEPPING>10</STEPPING>
      <THREAD>2</THREAD>
    </CPUS>
  </CONTENT>
</REQUEST>';
    $cpuStr2 = $cpuStr;
    if (is_null($serial1pass2))
    {
      $cpuStr2 = str_replace('[[SERIAL1]]', '', $cpuStr2);
    } else {
      $cpuStr2 = str_replace('[[SERIAL1]]', $serial1pass2, $cpuStr2);
    }

    $dataXMLEl = simplexml_load_string($cpuStr2);
    $json = json_encode($dataXMLEl);
    $dataObj = json_decode($json);

    \App\v1\Controllers\Fusioninventory\Computerprocessor::parse($dataObj, $computer);

    $items = \App\Models\Deviceprocessor::get();

    $this->assertEquals(1, count($items), '[pass2] 1 processor must be present in database');
    $this->assertEquals('Pentium(R) Dual-Core CPU E5300 @ 2.60GHz', $items[0]->name, '[pass2] Processor name not same');
    $this->assertGreaterThan(0, $items[0]->manufacturer_id, '[pass2] Processor manufacturer not same');
    $this->assertEquals('7A060100FFFBEBBF', $items[0]->cpuid, '[pass2] Processor cpuid not same');
    $this->assertEquals(2600, $items[0]->frequence, '[pass2] Processor frequence not same');
    $this->assertEquals('x86 Family 6 Model 23 Stepping 10', $items[0]->comment, '[pass2] Processor comment not same');
    $this->assertEquals(10, $items[0]->stepping, '[pass2] Processor stepping not same');
    $this->assertEquals(2, $items[0]->nbcores_default, '[pass2] Processor nbcores_default not same');
    $this->assertEquals(2, $items[0]->nbthreads_default, '[pass2] Processor nbthreads_default not same');

    // check processor in computer
    $computer->refresh();

    $this->assertEquals(1, count($computer->processors), '[pass2] Must have 1 processor attached to computer');
    $this->assertEquals($serial1pass2, $computer->processors[0]->pivot->serial, '[pass2] processor serial not same');
  }
}
