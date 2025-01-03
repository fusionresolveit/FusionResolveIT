<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Document extends Common
{
  protected $model = '\App\Models\Document';
  protected $rootUrl2 = '/documents/';
  protected $associateditems_model = '\App\Models\Documentitem';
  protected $associateditems_model_id = 'document_id';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Document();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Document();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Document();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
