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
    $myUser = \App\Models\User::find($arguments['decoded']['user_id']);
    // $jwtid = $myUser->getPropertyAttribute('userjwtid');
    // if (is_null($jwtid) || $jwtid != $arguments['decoded']['jti'])
    // {
    //   throw new Exception('jti changed, ask for a new token ' . $myUser['jwtid'] . ' != ' .
    //                       $arguments['decoded']['jti'], 401);
    // }

    $GLOBALS['user_id'] = $arguments['decoded']['user_id'];
    $GLOBALS['username'] = $myUser->completename;
    $GLOBALS['profile_id'] = $arguments['decoded']['profile_id'];
    $GLOBALS['entity_id'] = $arguments['decoded']['entity_id'];
    $GLOBALS['entity_treepath'] = $arguments['decoded']['entity_treepath'];
    $GLOBALS['entity_recursive'] = $arguments['decoded']['entity_recursive'];

    return $request;
  }
}
