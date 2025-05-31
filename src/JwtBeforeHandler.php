<?php

declare(strict_types=1);

namespace App;

use JimTools\JwtAuth\Handlers\BeforeHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class JwtBeforeHandler implements BeforeHandlerInterface
{
  /**
   * @param array{decoded: array<string, mixed>, token: string} $arguments
   */
  public function __invoke(ServerRequestInterface $request, array $arguments): ServerRequestInterface
  {
    /** @var \App\Models\User|null */
    $myUser = \App\Models\User::where('id', $arguments['decoded']['user_id'])->first();
    if (is_null($myUser))
    {
      throw new \Exception('JWT error, user not exists', 400);
    }
    $GLOBALS['user_id'] = $arguments['decoded']['user_id'];
    $GLOBALS['username'] = $myUser->completename;
    $GLOBALS['profile_id'] = $arguments['decoded']['profile_id'];
    $GLOBALS['entity_id'] = $arguments['decoded']['entity_id'];
    $GLOBALS['entity_treepath'] = $arguments['decoded']['entity_treepath'];
    $GLOBALS['entity_recursive'] = $arguments['decoded']['entity_recursive'];
    $GLOBALS['dark_mode'] = $myUser->getAttribute('dark_mode');

    return $request;
  }
}
