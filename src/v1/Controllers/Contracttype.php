<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Contracttype extends Common
{
  protected $model = '\App\Models\Contracttype';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contracttype();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contracttype();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contracttype();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
