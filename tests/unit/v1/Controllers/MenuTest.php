<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\v1\Controllers\Menu')]
#[UsesClass('\App\App')]
#[UsesClass('\App\Route')]
#[UsesClass('\App\Translation')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Computer')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Computer')]
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
#[UsesClass('\App\Models\Displaypreference')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\v1\Controllers\Common')]
#[UsesClass('\App\v1\Controllers\Computer')]
#[UsesClass('\App\v1\Controllers\Datastructures\Header')]
#[UsesClass('\App\v1\Controllers\Datastructures\Information')]
#[UsesClass('\App\v1\Controllers\Datastructures\Translation')]
#[UsesClass('\App\v1\Controllers\Datastructures\Viewdata')]
#[UsesClass('\App\v1\Controllers\Search')]
#[UsesClass('\App\v1\Controllers\Token')]
#[UsesClass('\App\v1\Controllers\Toolbox')]

final class MenuTest extends TestCase
{
  use HttpTestTrait;

  protected $app;

  protected function setUp(): void
  {
    $this->app = (new \App\App())->get();
  }

  public function testMenuDataHasDisplayField(): void
  {
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);
    $request = $this->createRequest('GET', '/view/computers', [], ['token' => $token]);
    $request = $request->withQueryParams([]);

    $response = $this->app->handle($request);
    $payload = (string) $response->getBody();

    $menu = new \App\v1\Controllers\Menu();

    $reflection = new \ReflectionClass($menu);
    $method = $reflection->getMethod('menuData');
    $method->setAccessible(true);

    $menuItems = $method->invoke($menu, $request);

    foreach ($menuItems as $menuItem)
    {
      foreach ($menuItem['sub'] as $subItem)
      {
        $substring = '<a class="item " href="' . $subItem['link'] . '">';
        if ($subItem['link'] == '/view/computers')
        {
          $substring = '<a class="item active blue" href="' . $subItem['link'] . '">';
        }
        $this->assertStringContainsString(
          $substring,
          $payload,
          'The menu name `' . $subItem['name'] . '` not have display key ' . $subItem['link']
        );
      }
      if (isset($menuItem['dropdown']))
      {
        foreach ($menuItem['dropdown'] as $subItem)
        {
          $substring = '<a class="item " href="' . $subItem['link'] . '">';
          $this->assertStringContainsString(
            $substring,
            $payload,
            'The menu dropdown name `' . $subItem['name'] . '` not have display key ' . $subItem['link']
          );
        }
      }
      if (isset($menuItem['component']))
      {
        foreach ($menuItem['component'] as $subItem)
        {
          $substring = '<a class="item " href="' . $subItem['link'] . '">';
          $this->assertStringContainsString(
            $substring,
            $payload,
            'The menu component name `' . $subItem['name'] . '` not have display key ' . $subItem['link']
          );
        }
      }
    }
  }

  public function testCleanMenuByDisplayEmpty(): void
  {
    $request = $this->createRequest('GET', '/view/computers');

    $menu = new \App\v1\Controllers\Menu();

    $reflection = new \ReflectionClass($menu);
    $method = $reflection->getMethod('menuData');
    $method->setAccessible(true);

    $menuItems = $method->invoke($menu, $request);

    $method = $reflection->getMethod('cleanMenuByDisplay');
    $method->setAccessible(true);

    $menuCleaned = $method->invoke($menu, $menuItems);

    $this->assertEmpty($menuCleaned, 'The menu must be empty because no rights defined');
  }

  public function testCleanMenuByDisplayComputer(): void
  {
    $request = $this->createRequest('GET', '/view/computers');

    $menu = new \App\v1\Controllers\Menu();

    $reflection = new \ReflectionClass($menu);
    $menuData = $reflection->getMethod('menuData');
    $menuData->setAccessible(true);
    $setRightForModel = $reflection->getMethod('setRightForModel');
    $setRightForModel->setAccessible(true);
    $cleanMenuByDisplay = $reflection->getMethod('cleanMenuByDisplay');
    $cleanMenuByDisplay->setAccessible(true);

    $setRightForModel->invoke($menu, 'App\Models\Computer');
    $menuItems = $menuData->invoke($menu, $request);
    $menuCleaned = $cleanMenuByDisplay->invoke($menu, $menuItems);

    $this->assertcount(1, $menuCleaned, 'The menu must have an entry');
    $this->assertArrayHasKey('sub', $menuCleaned[0], 'The menu must have sub key');
    $this->assertcount(1, $menuCleaned[0]['sub'], 'The sub menu must have an entry');
    $this->assertStringEndsWith('/view/computers', $menuCleaned[0]['sub'][0]['link']);
  }
}
