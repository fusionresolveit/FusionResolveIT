<?php

namespace Tests\Traits;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as slimResponse;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

trait HttpTestTrait
{
  /**
   * @param string $method
   * @param string $path
   * @param array  $headers
   * @param array  $cookies
   * @param array  $serverParams
   * @return Request
   */
  protected function createRequest(
    string $method,
    string $path,
    array $headers = [],
    array $cookies = [],
    array $serverParams = [],
    $content = null,
  ): Request
  {
    $uri = new Uri('', '', 80, $path);

    $handle = fopen('php://temp', 'w+');
    if (is_null($content))
    {
      $stream = (new StreamFactory())->createStreamFromResource($handle);
    } else {
      fwrite($handle, $content);
      rewind($handle);
      $stream = (new StreamFactory())->createStreamFromResource($handle);
    }

    $h = new Headers();
    foreach ($headers as $name => $value)
    {
      $h->addHeader($name, $value);
    }
    $h->addHeader('Referer', 'http://localhost/FusionResolveIT/view/home');
    return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
  }

  protected function setTokenForUser($user, $profileId = 1, $entityId = 1)
  {
    $response = new slimResponse();
    $token = new \App\v1\Controllers\Token();
    $jwt = $token->generateJWTToken($user, $response, $profileId, $entityId);
    return $jwt['token'];
  }
}
