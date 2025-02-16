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
#[UsesClass('\App\App')]
#[UsesClass('\App\DataInterface\Definition')]
#[UsesClass('\App\DataInterface\DefinitionCollection')]
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
#[UsesClass('\App\Models\Deviceprocessormodel')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Software')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\Traits\Relationships\Documents')]
#[UsesClass('\App\Traits\Relationships\Entity')]
#[UsesClass('\App\Traits\Relationships\Knowbaseitems')]
#[UsesClass('\App\Traits\Relationships\Location')]
#[UsesClass('\App\v1\Controllers\Dropdown')]
#[UsesClass('\App\v1\Controllers\Token')]

final class DropdownTest extends TestCase
{
  use HttpTestTrait;

  protected $app;

  protected function setUp(): void
  {
    $this->app = (new \App\App())->get();
  }

  public function testGetEntities(): void
  {
    $user = \App\Models\User::find(1);
    $token = $this->setTokenForUser($user);

    // Create request with method and url
    $request = $this->createRequest(
      'GET',
      '/view/dropdown',
      [],
      ['token' => $token]
    );
    $clone = $request->withQueryParams([
      'q' => '',
      'itemtype' => '\App\Models\Entity',
    ]);
    $response = $this->app->handle($clone);
    $this->assertEquals(200, $response->getStatusCode());
    $data = json_decode((string) $response->getBody());

    $this->assertObjectHasProperty('success', $data, '`success` property missing');
    $this->assertObjectHasProperty('results', $data, '`results` property missing');

    $this->assertEquals(1, count($data->results), 'must have 1 result');

    $result = $data->results[0];
    $this->assertEquals('main', $result->name, 'name not right');
    $this->assertEquals('1', $result->value, 'value not right');
    $this->assertEquals('item treelvl1', $result->class, 'class not right');
  }
}
