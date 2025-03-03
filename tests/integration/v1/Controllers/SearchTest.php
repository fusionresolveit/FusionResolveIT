<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\v1\Controllers\Search')]
#[UsesClass('\App\App')]
#[UsesClass('\App\DataInterface\Definition')]
#[UsesClass('\App\DataInterface\DefinitionCollection')]
#[UsesClass('\App\DataInterface\Post')]
#[UsesClass('\App\DataInterface\PostTicket')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\JwtBeforeHandler')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Category')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Change')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Followup')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Notification')]
#[UsesClass('\App\Models\Definitions\Problem')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\Rule')]
#[UsesClass('\App\Models\Definitions\Solution')]
#[UsesClass('\App\Models\Definitions\Ticket')]
#[UsesClass('\App\Models\Definitions\Ticketcost')]
#[UsesClass('\App\Models\Definitions\Ticketvalidation')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Displaypreference')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Followup')]
#[UsesClass('\App\Models\Rules\Ticket')]
#[UsesClass('\App\Models\Ticket')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\Route')]
#[UsesClass('\App\Traits\ProcessRules')]
#[UsesClass('\App\Traits\Relationships\Documents')]
#[UsesClass('\App\Traits\Relationships\Entity')]
#[UsesClass('\App\Traits\Relationships\Knowbaseitems')]
#[UsesClass('\App\Traits\Relationships\Location')]
#[UsesClass('\App\Traits\Relationships\Notes')]
#[UsesClass('\App\v1\Controllers\Common')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Validation')]
#[UsesClass('\App\v1\Controllers\Notification')]
#[UsesClass('\App\v1\Controllers\Profile')]
#[UsesClass('\App\v1\Controllers\Rules\Ticket')]
#[UsesClass('\App\v1\Controllers\Ticket')]
#[UsesClass('\App\v1\Controllers\Token')]
#[UsesClass('\App\v1\Controllers\Toolbox')]

final class SearchTest extends TestCase
{
  use HttpTestTrait;

  protected $app;

  public static function setUpBeforeClass(): void
  {
    // create location
    \App\Models\Location::create(['name' => 'Propières']);

    $user = new \App\Models\User();
    $user->id = 5;
    $user->name = 'user nb 5';
    $user->save();

    $user = new \App\Models\User();
    $user->id = 6;
    $user->name = 'user nb 6';
    $user->save();
  }

  protected function setUp(): void
  {
    $GLOBALS['profile_id'] = 1;
    $this->app = (new \App\App())->get();
  }

  public static function tearDownAfterClass(): void
  {
    // clean location
    \App\Models\Location::truncate();

    // Clean users
    $items = \App\Models\User::where('id', '>', 1)->get();
    foreach ($items as $item)
    {
      $item->forceDelete();
    }

    // Clean tickets
    $items = \App\Models\Ticket::get();
    foreach ($items as $item)
    {
      $item->forceDelete();
    }
  }

  public function testGetRightColumns(): void
  {
    // delete preferences for ticket
    $displayprefs = \App\Models\Displaypreference::where('itemtype', \App\Models\Ticket::class)->get();
    foreach ($displayprefs as $dpref)
    {
      $dpref->forceDelete();
    }

    // create preferences we want to test
    $myData = [
      'itemtype' => \App\Models\Ticket::class,
      'num'      => 1, //get from definition = name
      'rank'     => 1,
      'user_id'  => 0,
    ];
    \App\Models\Displaypreference::create($myData);

    $myData = [
      'itemtype' => \App\Models\Ticket::class,
      'num'      => 83, //get from definition = location
      'rank'     => 2,
      'user_id'  => 0,
    ];
    \App\Models\Displaypreference::create($myData);

    $myData = [
      'itemtype' => \App\Models\Ticket::class,
      'num'      => 4, //get from definition = requester
      'rank'     => 3,
      'user_id'  => 0,
    ];
    \App\Models\Displaypreference::create($myData);

    $location = \App\Models\Location::where('name', 'Propières')->first();

    // create the ticket
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);

    $request = $this->createRequest(
      'POST',
      '/view/tickets/new',
      ['Content-Type' => 'application/x-www-form-urlencoded'],
      ['token' => $token]
    );
    $clone = $request->withParsedBody([
      'content'   => 'Test',
      'name'      => 'Test ticket',
      'location'  => $location->id,
      'requester' => '5,6'
    ]);
    $response = $this->app->handle($clone);

    // now test fields in search

    $ticket = new \App\Models\Ticket();
    $search = new \App\v1\Controllers\Search();

    $fields = $search->getData($ticket, '');

    // print_r($fields);
    $expected = ['id', 'Title', 'Location', 'Requester'];
    $this->assertEquals($expected, $fields['header']);

    $myTicket = \App\Models\Ticket::where('name', 'Test ticket')->first();

    // Check values now
    $this->assertEquals($myTicket->id, $fields['data'][0]['id']['value']);
    $this->assertEquals('Test ticket', $fields['data'][0]['name']['value']);
    $this->assertEquals('Propières', $fields['data'][0]['location']['value']);
    $this->assertEquals('user nb 5, user nb 6', $fields['data'][0]['requester']['value']);
  }
}
