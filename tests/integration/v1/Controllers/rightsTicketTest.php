<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Selective\TestTrait\Traits\HttpTestTrait;
use Tests\Traits\AppTestTrait;

#[CoversClass('\App\v1\Controllers\Notification')]
#[UsesClass('\App\Route')]
#[UsesClass('\App\Translation')]
#[UsesClass('\App\Models\Category')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Category')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Change')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Followup')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Knowbaseitemcategory')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Problem')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\Ticket')]
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
#[UsesClass('\App\v1\Controllers\Toolbox')]

final class RightsTicketTest extends TestCase
{
  use AppTestTrait;
  use HttpTestTrait;

  public static function setUpBeforeClass(): void
  {
    $profile = \App\Models\Profile::find(2);
    if (!is_null($profile))
    {
      $profile->delete();
    }

    $profile = new \App\Models\Profile();
    $profile->id = 2;
    $profile->name = 'Test';
    $profile->interface = 'central';
    $profile->save();
  }

  public static function tearDownAfterClass(): void
  {
    $profile = \App\Models\Profile::find(2);
    $profile->delete();
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
    $request = $this->createRequest('GET', '/view/tickets');
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
    $request = $this->createRequest('GET', '/view/tickets');
    $response = $this->app->handle($request);

    $this->assertEquals(401, $response->getStatusCode());
  }

  public function testRenderTicketsRead(): void
  {
    $this->setRight('read');

    // Create request with method and url
    $request = $this->createRequest('GET', '/view/tickets');
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testRenderTicketsReadMyItems(): void
  {
    $this->setRight('readmyitems');

    // Create request with method and url
    $request = $this->createRequest('GET', '/view/tickets');
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());
  }
}
