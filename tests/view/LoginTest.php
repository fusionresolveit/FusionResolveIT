<?php

declare(strict_types=1);

namespace Tests\view;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\Route')]
#[CoversClass('\App\v1\Controllers\Fusioninventory\Communication')]
#[CoversClass('\App\v1\Controllers\Login')]
#[CoversClass('\App\v1\Controllers\Token')]
#[UsesClass('\App\App')]
#[UsesClass('\App\DataInterface\Definition')]
#[UsesClass('\App\DataInterface\DefinitionCollection')]
#[UsesClass('\App\DataInterface\Post')]
#[UsesClass('\App\DataInterface\PostTicket')]
#[UsesClass('\App\JwtBeforeHandler')]
#[UsesClass('\App\Route')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Authldap')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Location')]
#[UsesClass('\App\Models\Profile')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\Traits\ProcessRules')]
#[UsesClass('\App\Traits\Relationships\Documents')]
#[UsesClass('\App\Traits\Relationships\Entity')]
#[UsesClass('\App\Traits\Relationships\Knowbaseitems')]
#[UsesClass('\App\Traits\Relationships\Location')]
#[UsesClass('\App\Traits\Relationships\Notes')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Validation')]
#[UsesClass('\App\v1\Controllers\Profile')]
#[UsesClass('\App\v1\Controllers\Rules\Ticket')]
#[UsesClass('\App\v1\Controllers\Token')]

final class LoginTest extends TestCase
{
  use HttpTestTrait;

  protected $app;

  protected function setUp(): void
  {
    $this->app = (new \App\App())->get();
  }

  public function testLogin(): void
  {
    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/view/login',
      ['Content-Type' => 'application/x-www-form-urlencoded'],
    );
    $clone = $request->withParsedBody(['login' => 'admin', 'password' => 'adminIT']);

    $response = $this->app->handle($clone);

    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testBadLogin(): void
  {
    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/view/login',
      ['Content-Type' => 'application/x-www-form-urlencoded'],
    );
    $clone = $request->withParsedBody(['login' => 'admine', 'password' => 'adminIt']);

    $response = $this->app->handle($clone);

    $this->assertEquals(401, $response->getStatusCode());
  }

  public function testBadArgs(): void
  {
    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/view/login',
      ['Content-Type' => 'application/x-www-form-urlencoded'],
    );
    $clone = $request->withParsedBody(['Check' => 'admine']);

    $response = $this->app->handle($clone);

    $this->assertEquals(401, $response->getStatusCode());
  }
}
