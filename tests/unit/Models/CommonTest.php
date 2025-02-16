<?php

declare(strict_types=1);

namespace Tests\unit\Models;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass('\App\v1\Controllers\Common')]
#[UsesClass('\App\DataInterface\Definition')]
#[UsesClass('\App\DataInterface\DefinitionCollection')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Appliance')]

final class CommonTest extends TestCase
{
  public function testConstructFillable()
  {
    $appliance = new \App\Models\Appliance();

    $fillables = $appliance->getFillable();
    $expected = [
      'name',
      'state_id',
      'location_id',
      'manufacturer_id',
      'appliancetype_id',
      'applianceenvironment_id',
      'user_id_tech',
      'group_id_tech',
      'user_id',
      'group_id',
      'serial',
      'otherserial',
      'is_helpdesk_visible',
      'comment',
      'is_recursive',
    ];
    $this->assertEquals($expected, $fillables);
  }

  public function testGetDefinitions()
  {
    $appliance = new \App\Models\Appliance();

    $defData = $appliance->getDefinitions();

    $this->assertInstanceOf(\App\DataInterface\DefinitionCollection::class, $defData);

    $this->assertEquals(16, iterator_count($defData));
  }

  /**
   * test with ticket data
   */
  // public function testGetFormDataTicket()
  // {
  //   // create category
  //   $category = \App\Models\Category::create(['name' => 'servers']);

  //   // create ticket
  //   $myTicket = \App\Models\Ticket::create([
  //     'name'        => 'Server problem',
  //     'urgency'     => 5,
  //     'category_id' => $category->id,
  //   ]);

  //   $ticket = new \App\Models\Ticket();
  //   $formData = $ticket->getFormData($myTicket);

  //   // print_r($formData);
  // }
}
