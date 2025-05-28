<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\v1\Controllers\Menu')]

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
        $substring = '<a href="' . $subItem->endpoint . '">';
        if ($subItem->endpoint == '/view/computers')
        {
          $substring = '<a href="' . $subItem->endpoint . '">Computers';
        }
        $this->assertStringContainsString(
          $substring,
          $payload,
          'The menu name `' . $subItem->title . '` not have display key ' . $subItem->endpoint
        );
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
    $this->assertStringEndsWith('/view/computers', $menuCleaned[0]['sub'][0]->endpoint);
  }
}
