<?php

declare(strict_types=1);

namespace Tests\view;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\v1\Controllers\Ticket')]
#[CoversClass('\App\v1\Controllers\Common')]
#[CoversClass('\App\Models\Ticket')]
#[CoversClass('\App\Models\Common')]
#[UsesClass('\App\App')]
#[UsesClass('\App\DataInterface\Definition')]
#[UsesClass('\App\DataInterface\DefinitionCollection')]
#[UsesClass('\App\DataInterface\Post')]
#[UsesClass('\App\DataInterface\PostTicket')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\JwtBeforeHandler')]
#[UsesClass('\App\Route')]
#[UsesClass('\App\Models\Definitions\Category')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Change')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Followup')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Knowledgebasearticle')]
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
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Followup')]
#[UsesClass('\App\Models\Location')]
#[UsesClass('\App\Models\Profile')]
#[UsesClass('\App\Models\Rules\Ticket')]
#[UsesClass('\App\Models\Solution')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\Traits\ProcessRules')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Validation')]
#[UsesClass('\App\v1\Controllers\Notification')]
#[UsesClass('\App\v1\Controllers\Profile')]
#[UsesClass('\App\v1\Controllers\Rules\Ticket')]
#[UsesClass('\App\v1\Controllers\Toolbox')]
#[UsesClass('\App\v1\Controllers\Token')]

final class TicketTest extends TestCase
{
  use HttpTestTrait;

  protected $user01Id;
  protected $user02Id;
  protected $app;

  protected function setUp(): void
  {
    $this->app = (new \App\App())->get();

    // create users if not exists
    $user01 = \App\Models\User::where('name', 'test user01')->first();
    if (is_null($user01))
    {
      $user01 = \App\Models\User::create([
        'name' => 'test user01',
      ]);
    }
    $this->user01Id = $user01->id;

    $user02 = \App\Models\User::where('name', 'test user02')->first();
    if (is_null($user02))
    {
      $user02 = \App\Models\User::create([
        'name' => 'test user02',
      ]);
    }
    $this->user02Id = $user02->id;

    // Clean tickets
    $tickets = \App\Models\Ticket::get();
    foreach ($tickets as $ticket)
    {
      $ticket->forceDelete();
    }    
  }

  protected function tearDown(): void
  {
    // Clean user
    $users = \App\Models\User::where('id', '>', 1)->get();
    foreach ($users as $user)
    {
      $user->forceDelete();
    }
  }

  public function testCreateTicketTechs()
  {
    // $data = (object) [
    //   'content'     => 'Test',
    //   'name'        => 'Test',
    //   'technician'  => $this->user01Id . ',' . $this->user02Id,
    //   // save=view
    //   // content=Test+
    //   // name=Test+
    //   // type=1
    //   // status=1
    //   // category=0
    //   // location=0
    //   // urgency=3
    //   // impact=3
    //   // priority=3
    //   // time_to_resolve=
    //   // requester=
    //   // requestergroup=
    //   // watcher=
    //   // watchergroup=
    //   // technician=4099%2C4096
    //   // techniciangroup=
    // ];
    // $ctrl = new \App\v1\Controllers\Ticket();
    // $ticketId = $ctrl->saveItem($data);

    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);

    $request = $this->createRequest(
      'POST',
      '/view/tickets/new',
      ['Content-Type' => 'application/x-www-form-urlencoded'],
      ['token' => $token]
    );
    $clone = $request->withParsedBody([
      'content' => 'Test',
      'name' => 'Test',
      'technician'  => $this->user01Id . ',' . $this->user02Id
    ]);
    $response = $this->app->handle($clone);

    $ticket = \App\Models\Ticket::where('name', 'Test')->first();

    $this->assertNotNull($ticket);

    // check
    // $ticket = \App\Models\Ticket::find($ticketId);
    $this->assertEquals(2, count($ticket->technician), 'must have 2 technicians on this ticket');
    $users = [];
    foreach ($ticket->technician as $user)
    {
      $users[] = $user->name;
    }
    $this->assertEquals(['test user01', 'test user02'], $users, 'users id not right');
  }
}
