<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\v1\Controllers\Notification')]
#[UsesClass('\App\App')]
#[UsesClass('\App\Route')]
#[UsesClass('\App\Translation')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Models\Category')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Category')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Change')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Followup')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\ItemTicket')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Knowbaseitemcategory')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Problem')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\ProfileUser')]
#[UsesClass('\App\Models\Definitions\ProjecttaskTicket')]
#[UsesClass('\App\Models\Definitions\Solution')]
#[UsesClass('\App\Models\Definitions\Ticket')]
#[UsesClass('\App\Models\Definitions\Ticketcost')]
#[UsesClass('\App\Models\Definitions\Ticketvalidation')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Followup')]
#[UsesClass('\App\Models\Group')]
#[UsesClass('\App\Models\Location')]
#[UsesClass('\App\Models\Ticket')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\v1\Controllers\Common')]
#[UsesClass('\App\v1\Controllers\Ticket')]

#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Displaypreference')]
#[UsesClass('\App\v1\Controllers\Datastructures\Header')]
#[UsesClass('\App\v1\Controllers\Datastructures\Information')]
#[UsesClass('\App\v1\Controllers\Datastructures\Translation')]
#[UsesClass('\App\v1\Controllers\Datastructures\Viewdata')]
#[UsesClass('\App\v1\Controllers\Menu')]
#[UsesClass('\App\v1\Controllers\Search')]
#[UsesClass('\App\v1\Controllers\Token')]
#[UsesClass('\App\v1\Controllers\Toolbox')]


final class RightsTicketTest extends TestCase
{
  use HttpTestTrait;

  protected $app;

  public static function setUpBeforeClass(): void
  {
    $profile = new \App\Models\Profile();
    $profile->id = 2;
    $profile->name = 'Test';
    $profile->interface = 'central';
    $profile->save();
  }

  public static function tearDownAfterClass(): void
  {
    $GLOBALS['profile_id'] = 1;

    $profile = \App\Models\Profile::find(2);
    $profile->forceDelete();
  }

  protected function setUp(): void
  {
    $GLOBALS['profile_id'] = 1;
    $this->app = (new \App\App())->get();

    $user = \App\Models\User::find(1);
    $this->setTokenForUser($user);

    // TODO manage token for requests
  }


  private function setRight($rightname)
  {
    $profileright = \App\Models\Profileright::where('profile_id', 2)->first();
    if (is_null($profileright))
    {
      $profileright = new \App\Models\Profileright();
      $profileright->profile_id = 2;
      $profileright->model = 'App\Models\Ticket';
    }
    $profileright->read = false;

    $profileright->{$rightname} = true;
    $profileright->save();
  }

  public function testRenderTicketsOnDefaultInstallation(): void
  {
    $GLOBALS['profile_id'] = 1;

    // Create request with method and url
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);
    $request = $this->createRequest('GET', '/view/tickets', [], ['token' => $token]);
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testRenderTicketsNoRights(): void
  {
    $GLOBALS['profile_id'] = 2;

    $profileright = \App\Models\Profileright::where('profile_id', 2)->first();
    if (!is_null($profileright))
    {
      $profileright->delete();
    }

    // Create request with method and url
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user, 2);
    $request = $this->createRequest('GET', '/view/tickets', [], ['token' => $token]);
    $response = $this->app->handle($request);

    $this->assertEquals(401, $response->getStatusCode());
  }

  public function testRenderTicketsRead(): void
  {
    $this->setRight('read');

    // Create request with method and url
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);
    $request = $this->createRequest('GET', '/view/tickets', [], ['token' => $token]);
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testRenderTicketsReadMyItems(): void
  {
    $this->setRight('readmyitems');

    // Create request with method and url
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);
    $request = $this->createRequest('GET', '/view/tickets', [], ['token' => $token]);
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());
  }
}
