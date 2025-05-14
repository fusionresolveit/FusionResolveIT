<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

#[CoversClass('\App\v1\Controllers\Token')]

final class TokenTest extends TestCase
{
  public static function tearDownAfterClass(): void
  {
    // Clean user account
    $userDavid = \App\Models\User::where('name', 'david')->first();
    if (!is_null($userDavid))
    {
      $userDavid->forceDelete();
    }
    // Clean profile
    $profileMyTest = \App\Models\Profile::where('name', 'profileTest')->first();
    if (!is_null($profileMyTest))
    {
      $profileMyTest->forceDelete();
    }
  }

  public static function checkPawwsordProvider(): array
  {
    return [
      [
        'adminIT',
        'c58a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933ea788ce51e0336a15dc597f92d71d18f',
        true
      ],
      [
        'admin IT',
        'c58a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933ea788ce51e0336a15dc597f92d71d18f',
        false
      ],
      [
        'adminIT',
        'c88a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933ea788ce51e0336a15dc597f92d71d18f',
        false
      ],
      [
        'adminIT',
        'c58a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933eb788ce51e0336a15dc597f92d71d18f',
        false
      ],
      ['adminIT', 'c58a5addefacdd3629c6a960ba7f5a08.', false],
      ['adminIT', null, false],
      ['adminIT', '', false],
      ['adminIT', '.', false],
      [
        'adminIT',
        'c58a5addefacdd3629c6a960ba7f5a08.da2b2a51e01cd89edc6d58a7b1878933ea788ce51e0336a15dc597f92d71d18f.test',
        false
      ],
    ];
  }

  #[DataProvider('checkPawwsordProvider')]
  public function testCheckPassword($password, $hash, $expected)
  {
    $state = \App\v1\Controllers\Token::checkPassword($password, $hash);
    $this->assertEquals($expected, $state);
  }

  public function testGenerateJWTTokenSuccess()
  {
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $token = new \App\v1\Controllers\Token();
    $ret = $token->generateJWTToken($user);
    $this->assertIsArray($ret);
    $this->assertArrayHasKey('token', $ret);
    $this->assertArrayHasKey('refreshtoken', $ret);
    $this->assertArrayHasKey('expires', $ret);
    $this->assertIsInt($ret['expires']);
  }

  public function testGenerateJWTTokenNoProfile()
  {
    $userDavid = \App\Models\User::create(['name' => 'david']);
    $this->assertNotNull($userDavid);
    $token = new \App\v1\Controllers\Token();
    try {
      $token->generateJWTToken($userDavid);
      $this->fail('Exception was not thrown');
    } catch (\Exception $e) {
      $this->assertEquals('Unauthorized access', $e->getMessage());
      $this->assertEquals(401, $e->getCode());
    }
  }

  public function testGenerateJWTTokenForceProfile()
  {
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);

    $profileMyTest = \App\Models\Profile::create(['name' => 'profileTest']);

    $token = new \App\v1\Controllers\Token();
    $ret = $token->generateJWTToken($user, $profileMyTest->id, 1);
    $this->assertIsArray($ret);
    $this->assertArrayHasKey('token', $ret);
    $this->assertArrayHasKey('refreshtoken', $ret);
    $this->assertArrayHasKey('expires', $ret);
    $this->assertIsInt($ret['expires']);

    // decode JWT
    $secret = sodium_base642bin('TEST', SODIUM_BASE64_VARIANT_ORIGINAL);
    $token = JWT::decode($ret['token'], new Key($secret, "HS256"));

    $this->assertEquals($profileMyTest->id, $token->profile_id);
  }

  public function testGenerateJWTTokenForceProfileNotExists()
  {
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $token = new \App\v1\Controllers\Token();
    try {
      $token->generateJWTToken($user, 4056, 1);
      $this->fail('Exception was not thrown');
    } catch (\Exception $e) {
      $this->assertEquals('Unauthorized access', $e->getMessage());
      $this->assertEquals(401, $e->getCode());
    }
  }

  public function testGenerateJWTTokenForceEntityNotExists()
  {
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $token = new \App\v1\Controllers\Token();
    try {
      $token->generateJWTToken($user, 1, 6877);
      $this->fail('Exception was not thrown');
    } catch (\Exception $e) {
      $this->assertEquals('Unauthorized access', $e->getMessage());
      $this->assertEquals(401, $e->getCode());
    }
  }

  public function testGenerateJWTTokenSuccessWithRefreshtokenExists()
  {
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $user->refreshtoken = 'gtrighihf5f98h43276d3';
    $user->save();
    $token = new \App\v1\Controllers\Token();
    $ret = $token->generateJWTToken($user);

    $user->refresh();
    $this->assertEquals('gtrighihf5f98h43276d3', $user->refreshtoken);
    $this->assertEquals('gtrighihf5f98h43276d3', $ret['refreshtoken']);
  }

  public function testGenerateJWTTokenSuccessWithRefreshtokenNotExists()
  {
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $user->refreshtoken = null;
    $user->save();
    $user->refresh();
    $this->assertNull($user->refreshtoken);
    $token = new \App\v1\Controllers\Token();
    $ret = $token->generateJWTToken($user);

    $user->refresh();
    $this->assertNotNull($user->refreshtoken);
    $this->assertNotNull($ret['refreshtoken']);
  }

  public function testManageExpiredTokenWithCookieRefreshtokenUserRefreshtoken()
  {
    $_COOKIE['refresh-token'] = 'nfiu3h2f76gfhde21dtg2ef';
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $user->refreshtoken = 'nfiu3h2f76gfhde21dtg2ef';
    $user->save();

    $e = new ExpiredException('Expired token');
    $payload = new stdClass();
    $payload->user_id = 1;
    $e->setPayload($payload);

    $ret = \App\v1\Controllers\Token::manageExpiredToken($e);

    $this->assertIsArray($ret);
    $this->assertArrayHasKey('user_id', $ret);
    $this->assertEquals(1, $ret['user_id']);
  }

  public function testManageExpiredTokenWithCookieNotRefreshtokenUserRefreshtoken()
  {
    if (isset($_COOKIE['refresh-token']))
    {
      unset($_COOKIE['refresh-token']);
    }
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $user->refreshtoken = 'nfiu3h2f76gfhde21dtg2ef';
    $user->save();

    $e = new ExpiredException('Expired token');
    $payload = new stdClass();
    $payload->user_id = 1;
    $e->setPayload($payload);

    try {
      $ret = \App\v1\Controllers\Token::manageExpiredToken($e);
      $this->fail('Exception was not thrown');
    } catch (\Exception $ex) {
      $this->assertEquals('Expired token', $e->getMessage());
    }
  }

  public function testManageExpiredTokenWithCookieRefreshtokenUserNotRefreshtoken()
  {
    $_COOKIE['refresh-token'] = 'nfiu3h2f76gfhde21dtg2ef';
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $user->refreshtoken = null;
    $user->save();

    $e = new ExpiredException('Expired token');
    $payload = new stdClass();
    $payload->user_id = 1;
    $e->setPayload($payload);

    try {
      $ret = \App\v1\Controllers\Token::manageExpiredToken($e);
      $this->fail('Exception was not thrown');
    } catch (\Exception $ex) {
      $this->assertEquals('Expired token', $e->getMessage());
    }
  }

  public function testManageExpiredTokenWithCookieNotRefreshtokenUserNotRefreshtoken()
  {
    if (isset($_COOKIE['refresh-token']))
    {
      unset($_COOKIE['refresh-token']);
    }
    $user = \App\Models\User::where('id', 1)->first();
    $this->assertNotNull($user);
    $user->refreshtoken = null;
    $user->save();

    $e = new ExpiredException('Expired token');
    $payload = new stdClass();
    $payload->user_id = 1;
    $e->setPayload($payload);

    try {
      $ret = \App\v1\Controllers\Token::manageExpiredToken($e);
      $this->fail('Exception was not thrown');
    } catch (\Exception $ex) {
      $this->assertEquals('Expired token', $e->getMessage());
    }
  }
}
