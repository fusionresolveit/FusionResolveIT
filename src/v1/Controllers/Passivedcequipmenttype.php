<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Passivedcequipmenttype extends Common
{
  protected $model = '\App\Models\Passivedcequipmenttype';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Passivedcequipmenttype();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Passivedcequipmenttype();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Passivedcequipmenttype();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
