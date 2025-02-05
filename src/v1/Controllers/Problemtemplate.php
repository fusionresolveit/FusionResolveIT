<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Problemtemplate extends Common
{
  protected $model = '\App\Models\Problemtemplate';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Problemtemplate();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Problemtemplate();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Problemtemplate();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
