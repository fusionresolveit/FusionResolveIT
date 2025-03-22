<?php

declare(strict_types=1);

namespace Tests\view;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;
use Illuminate\Support\Carbon;

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

  public static function setUpBeforeClass(): void
  {
    $user = new \App\Models\User();
    $user->id = 5;
    $user->name = 'Steve Rogers';
    $user->password = \App\v1\Controllers\Token::generateDBHashPassword('mypass');
    $user->save();

    $audits = \App\Models\Audit::get();
    foreach ($audits as $audit)
    {
      $audit->forceDelete();
    }
  }

  public static function tearDownAfterClass(): void
  {
    // Clean user
    $users = \App\Models\User::where('id', '>', 1)->get();
    foreach ($users as $user)
    {
      $user->forceDelete();
    }
  }

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
      [],
      ['REMOTE_ADDR' => '127.0.0.1']
    );
    $clone = $request->withParsedBody(['login' => 'admin', 'password' => 'adminIT']);

    $response = $this->app->handle($clone);

    $this->assertEquals(302, $response->getStatusCode());
  }

  public function testBadLogin(): void
  {
    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/view/login',
      ['Content-Type' => 'application/x-www-form-urlencoded'],
      [],
      ['REMOTE_ADDR' => '127.0.0.1']
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
      [],
      ['REMOTE_ADDR' => '127.0.0.1']
    );
    $clone = $request->withParsedBody(['Check' => 'admine']);

    $response = $this->app->handle($clone);

    $this->assertEquals(401, $response->getStatusCode());
  }

  private function loginSteveRogersBadPassword($message): void
  {
    // Create request with method and url
    $request = $this->createRequest(
      'POST',
      '/view/login',
      ['Content-Type' => 'application/x-www-form-urlencoded'],
      [],
      ['REMOTE_ADDR' => '127.0.0.1']
    );
    $clone = $request->withParsedBody(['login' => 'Steve Rogers', 'password' => 'Shield']);

    $response = $this->app->handle($clone);

    $this->assertEquals(401, $response->getStatusCode());
    $this->assertMatchesRegularExpression($message, (string) $response->getBody());
  }

  public function testSecurityAccountFirstTry(): void
  {
    // check field attemtp is set to 0
    $user = \App\Models\User::where('name', 'Steve Rogers')->first();
    $this->assertNotNull($user);
    $this->assertEquals(0 ,$user->security_attempt);
    $this->assertNull($user->security_last_attempt);

    $message = '/Login or password error, first attempt, wait 30 seconds before try again/';
    $this->loginSteveRogersBadPassword($message);

    // check field attempt is set to 1
    $user->refresh();
    $this->assertEquals(1 ,$user->security_attempt);
    $this->assertNotNull($user->security_last_attempt);

    // check have entry in audit
  }

  /**
   * @depends testSecurityAccountFirstTry
   */
  public function testSecurityAccountSecondTry(): void
  {
    // check field attemtp is set to 1
    $user = \App\Models\User::where('name', 'Steve Rogers')->first();
    $this->assertEquals(1 ,$user->security_attempt);
    $lastSecurityAttemptDate = $user->security_last_attempt;

    $message = '/Security, wait (30|29) seconds before try again/';
    $this->loginSteveRogersBadPassword($message);
    $user->refresh();
    $this->assertEquals(1 ,$user->security_attempt);
    $this->assertEquals($lastSecurityAttemptDate ,$user->security_last_attempt);

    // update last attempty date to go to next security level
    $lastSecurityAttemptDate = Carbon::now()->subMinute();
    $user->security_last_attempt = $lastSecurityAttemptDate;
    $user->save();

    $now = Carbon::now();
    $message = '/Login or password error, second attempt, wait 2 minutes before try again/';
    $this->loginSteveRogersBadPassword($message);

    // check field attempt is set to 2
    $user->refresh();
    $this->assertEquals(2 ,$user->security_attempt);
    $this->assertGreaterThan($lastSecurityAttemptDate, $user->security_last_attempt);

    // check have entry in audit
  }

  /**
   * @depends testSecurityAccountSecondTry
   */
  public function testSecurityAccountThirdTry(): void
  {
    // check field attemtp is set to 2
    $user = \App\Models\User::where('name', 'Steve Rogers')->first();
    $this->assertEquals(2 ,$user->security_attempt);
    $lastSecurityAttemptDate = $user->security_last_attempt;

    $message = '/Security, wait (120|119) seconds before try again/';
    $this->loginSteveRogersBadPassword($message);
    $user->refresh();
    $this->assertEquals(2 ,$user->security_attempt);
    $this->assertEquals($lastSecurityAttemptDate ,$user->security_last_attempt);

    // update last attempty date to go to next security level
    $lastSecurityAttemptDate = Carbon::now()->subMinutes(3);
    $user->security_last_attempt = $lastSecurityAttemptDate;

    $user->save();

    $now = Carbon::now();
    $message = '/Login or password error, third attempt, wait 5 minutes before try again/';
    $this->loginSteveRogersBadPassword($message);

    // check field attempt is set to 3
    $user->refresh();
    $this->assertEquals(3 ,$user->security_attempt);
    $this->assertGreaterThan($lastSecurityAttemptDate, $user->security_last_attempt);

    // check have entry in audit
  }

  /**
   * @depends testSecurityAccountThirdTry
   */
  public function testSecurityAccountfourthTry(): void
  {
    // check field attemtp is set to 3
    $user = \App\Models\User::where('name', 'Steve Rogers')->first();
    $this->assertEquals(3 ,$user->security_attempt);
    $lastSecurityAttemptDate = $user->security_last_attempt;

    $message = '/Security, wait (300|299) seconds before try again/';
    $this->loginSteveRogersBadPassword($message);
    $user->refresh();
    $this->assertEquals(3 ,$user->security_attempt);
    $this->assertEquals($lastSecurityAttemptDate ,$user->security_last_attempt);

    // update last attempty date to go to next security level
    $lastSecurityAttemptDate = Carbon::now()->subMinutes(6);
    $user->security_last_attempt = $lastSecurityAttemptDate;

    $user->save();

    $now = Carbon::now();
    $message = '/Login or password error, fourth attempt, wait 10 minutes before try again/';
    $this->loginSteveRogersBadPassword($message);

    // check field attempt is set to 4
    $user->refresh();
    $this->assertEquals(4 ,$user->security_attempt);
    $this->assertGreaterThan($lastSecurityAttemptDate, $user->security_last_attempt);

    // check have entry in audit
  }

  /**
   * @depends testSecurityAccountfourthTry
   */
  public function testSecurityAccountfifthTry(): void
  {
    // check field attemtp is set to 4
    $user = \App\Models\User::where('name', 'Steve Rogers')->first();
    $this->assertEquals(4 ,$user->security_attempt);
    $lastSecurityAttemptDate = $user->security_last_attempt;

    $message = '/Security, wait (600|599) seconds before try again/';
    $this->loginSteveRogersBadPassword($message);
    $user->refresh();
    $this->assertEquals(4 ,$user->security_attempt);
    $this->assertEquals($lastSecurityAttemptDate ,$user->security_last_attempt);

    // update last attempty date to go to next security level
    $lastSecurityAttemptDate = Carbon::now()->subMinutes(15);
    $user->security_last_attempt = $lastSecurityAttemptDate;

    $user->save();

    $now = Carbon::now();
    $message = '/Login or password error, fifth attempt, wait 1 hour before try again/';
    $this->loginSteveRogersBadPassword($message);

    // check field attempt is set to 5
    $user->refresh();
    $this->assertEquals(5 ,$user->security_attempt);
    $this->assertGreaterThan($lastSecurityAttemptDate, $user->security_last_attempt);

    // check have entry in audit
  }

  /**
   * @depends testSecurityAccountfifthTry
   */
  public function testSecurityAccountLastLevelTry(): void
  {
    // check field attemtp is set to 5
    $user = \App\Models\User::where('name', 'Steve Rogers')->first();
    $this->assertEquals(5 ,$user->security_attempt);
    $lastSecurityAttemptDate = $user->security_last_attempt;

    $message = '/Security, wait (3600|3599) seconds before try again/';
    $this->loginSteveRogersBadPassword($message);
    $user->refresh();
    $this->assertEquals(5 ,$user->security_attempt);
    $this->assertEquals($lastSecurityAttemptDate ,$user->security_last_attempt);

    $lastSecurityAttemptDate = Carbon::now()->subMinutes(10);
    $user->security_last_attempt = $lastSecurityAttemptDate;
    $user->save();

    $message = '/Security, wait (3000|2999|2998) seconds before try again/';
    $this->loginSteveRogersBadPassword($message);
    
    $lastSecurityAttemptDate = Carbon::now()->subMinutes(59);
    $user->security_last_attempt = $lastSecurityAttemptDate;
    $user->save();

    $message = '/Security, wait (60|59|58|57) seconds before try again/';
    $this->loginSteveRogersBadPassword($message);

    $lastSecurityAttemptDate = Carbon::now()->subMinutes(61);
    $user->security_last_attempt = $lastSecurityAttemptDate;
    $user->save();

    $message = '/Login or password error, fifth attempt, wait 1 hour before try again/';
    $this->loginSteveRogersBadPassword($message);

    // check field attempt is set to 5
    $user->refresh();
    $this->assertEquals(5 ,$user->security_attempt);

    $lastSecurityAttemptDate = Carbon::now()->subHours(2);
    $user->security_last_attempt = $lastSecurityAttemptDate;
    $user->save();

    $message = '/Login or password error, fifth attempt, wait 1 hour before try again/';
    $this->loginSteveRogersBadPassword($message);

    // check field attempt is set to 5
    $user->refresh();
    $this->assertEquals(5 ,$user->security_attempt);

    // check have entry in audit
  }

  /**
   * @depends testSecurityAccountLastLevelTry
   */
  public function testSecurityAccountSuccessAfterFailed(): void
  {
    // check field attemtp is set to 5
    $user = \App\Models\User::where('name', 'Steve Rogers')->first();
    $this->assertEquals(5 ,$user->security_attempt);

    $message = '/Security, wait (3600|3599|3598) seconds before try again/';
    $this->loginSteveRogersBadPassword($message);

    $request = $this->createRequest(
      'POST',
      '/view/login',
      ['Content-Type' => 'application/x-www-form-urlencoded'],
      [],
      ['REMOTE_ADDR' => '127.0.0.1']
    );
    $clone = $request->withParsedBody(['login' => 'Steve Rogers', 'password' => 'mypass']);

    $response = $this->app->handle($clone);

    $this->assertEquals(401, $response->getStatusCode());
    $this->assertMatchesRegularExpression($message, (string) $response->getBody());
   
    $lastSecurityAttemptDate = Carbon::now()->subHours(2);
    $user->security_last_attempt = $lastSecurityAttemptDate;
    $user->save();
    
    $response = $this->app->handle($clone);
    $this->assertEquals(302, $response->getStatusCode());

    $user->refresh();
    $this->assertEquals(0 ,$user->security_attempt);
    $this->assertNull($user->security_last_attempt);
  }

  private function prepareAddAudit(): \App\Models\Audit
  {
    $audit = new \App\Models\Audit();
    $audit->action = 'CONNECTION';
    $audit->subaction = 'FAIL';
    $audit->message = 'fail, login: Steve Rogers';
    $audit->ip = '127.0.0.1';
    $audit->httpmethod = 'POST';
    $audit->endpoint = '/view/login';
    $audit->httpcode = 401;
    return $audit;
  }

  /**
   * @depends testSecurityAccountSuccessAfterFailed
   */
  public function testSecurityBruteForceByIP()
  {
    $audits = \App\Models\Audit::get();
    foreach ($audits as $audit)
    {
      $audit->forceDelete();
    }

    $user = \App\Models\User::where('name', 'Steve Rogers')->first();
    $user->security_attempt = 0;
    $user->save();

    // add audit in DB
    foreach (range(1, 8) as $i) {
      $audit = $this->prepareAddAudit();
      $audit->created_at = Carbon::now()->subHour()->subMinutes(20);
      $audit->save();
    }

    $cnt = \App\Models\Audit::count();
    $this->assertEquals(8, $cnt);

    $message = '/Login or password error, first attempt, wait 30 seconds before try again/';
    $this->loginSteveRogersBadPassword($message);

    // Now, add failures, but under 10 by minute
    $user->refresh();
    $user->security_attempt = 0;
    $user->save();

    foreach (range(1, 8) as $i) {
      $audit = $this->prepareAddAudit();
      $audit->created_at = Carbon::now()->subMinutes(20);
      $audit->save();
    }

    $cnt = \App\Models\Audit::count();
    $this->assertEquals(17, $cnt);

    $message = '/Login or password error, first attempt, wait 30 seconds before try again/';
    $this->loginSteveRogersBadPassword($message);

    $user->refresh();
    $user->security_attempt = 0;
    $user->save();

    // now add 12 failures in 1 minute

    foreach (range(1, 12) as $i) {
      $audit = $this->prepareAddAudit();
      $audit->created_at = Carbon::now()->subMinutes(42);
      $audit->save();
    }

    $message = '/Too many attempts/';
    $this->loginSteveRogersBadPassword($message);
  }
}
