<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;
use Selective\TestTrait\Traits\HttpTestTrait;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Http\Environment;
use Tests\Traits\AppTestTrait;

#[CoversClass('\App\v1\Controllers\Notification')]
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

final class RightsTicketTest extends TestCase
{
  use AppTestTrait;
  use HttpTestTrait;

  public function testRenderNotificationTwoFollowups(): void
  {
    // $this->mock(\App\v1\Controllers\Ticket::class)
    //      ->method('')

    // Create request with method and url
    $request = $this->createRequest('GET', '/view/tickets');
    $response = $this->app->handle($request);

    print_r($response);

    $this->assertEquals('yo', 'yo');
  }
}


// https://gist.githubusercontent.com/shahariaazam/8523437/raw/95cf2ea2c66e1512120a60cccc0ab3fbb8139faf/Slim_RoutesTest.php
// https://blog.shaharia.com/write-unit-test-for-slim-framework
// https://akrabat.com/testing-slim-framework-actions/
