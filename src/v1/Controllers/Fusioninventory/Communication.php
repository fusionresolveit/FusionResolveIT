<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spatie\ArrayToXml\ArrayToXml;

final class Communication extends \App\v1\Controllers\Common
{
  public function getConfig(Request $request, Response $response, $args): Response
  {
    // define default user
    $GLOBALS['user_id'] = 92368;

    $contentType = $request->getHeaderLine('Content-Type');
    if ($contentType == "application/xml") {
      $data = $request->getBody()->getContents();
    }
    elseif ($contentType == "application/x-compress-zlib")
    {
      $data = @gzuncompress($request->getBody()->getContents());
    } else {
      $data = @gzinflate(substr($request->getBody()->getContents(), 2));
    }
    if ($data === false)
    {
      throw new FusioninventoryXmlException('Data format not right', 400);
    }

    if (strstr($data, '<QUERY>INVENTORY</QUERY>'))
    {
      $computer = new Computer();
      $computer->importComputer($data);
      $payload = [];
    }
    elseif (strstr($data, '<QUERY>PROLOG</QUERY'))
    {
      $payload = [
        "PROLOG_FREQ" => 24,
        "RESPONSE" => "SEND",
      ];
    } else {
      throw new FusioninventoryXmlException('Data not right', 400);
    }

    $response->getBody()->write(ArrayToXml::convert($payload, 'REPLY'));
    return $response->withHeader('Content-Type', 'application/xml');
  }

  public function null(Request $request, Response $response, $args): Response
  {
    $response->getBody()->write(json_encode([]));
    return $response->withHeader('Content-Type', 'application/json');
  }
}
