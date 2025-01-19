<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\v1\Controllers\Menubookmark')]
#[CoversClass('\App\Route')]
#[CoversClass('\App\Models\Definitions\Menubookmark')]
#[CoversClass('\App\Models\Menubookmark')]
#[CoversClass('\App\v1\Controllers\Menu')]
#[UsesClass('\App\App')]
#[UsesClass('\App\JwtBeforeHandler')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\ProfileUser')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Location')]
#[UsesClass('\App\Models\Profile')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\Traits\Relationships\Documents')]
#[UsesClass('\App\Traits\Relationships\Entity')]
#[UsesClass('\App\Traits\Relationships\Knowbaseitems')]
#[UsesClass('\App\Traits\Relationships\Location')]
#[UsesClass('\App\Traits\Relationships\Notes')]
#[UsesClass('\App\v1\Controllers\Token')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]


final class MenubookmarkTest extends TestCase
{
  use HttpTestTrait;

  protected $app;

  protected function setUp(): void
  {
    $GLOBALS['profile_id'] = 1;
    $this->app = (new \App\App())->get();
  }

  public static function tearDownAfterClass(): void
  {
    $items = \App\Models\Menubookmark::get();
    foreach ($items as $item)
    {
      $item->forceDelete();
    }
  }

  public function testSetBookmarkUnknownEndpoint(): void
  {
    // Create request with method and url
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);
    $request = $this->createRequest('GET', '/view/menubookmarks/view/computertos', [], ['token' => $token]);
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testSetBookmarkUrlNotMatchRoute(): void
  {
    // Create request with method and url
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);
    $request = $this->createRequest('GET', '/view/menubookmarks/view/computers2me', [], ['token' => $token]);
    $response = $this->app->handle($request);

    $this->assertEquals(404, $response->getStatusCode());
  }

  public function testSetBookmarkComputer2Times(): void
  {
    // Create request with method and url
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);
    $request = $this->createRequest('GET', '/view/menubookmarks/view/computers', [], ['token' => $token]);
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());

    $items = \App\Models\Menubookmark::get();
    $this->assertEquals(1, count($items));

    $item = $items[0];
    $this->assertEquals('/view/computers', $item->endpoint);
    $this->assertEquals(1, $item->user->id);
  }
}
