<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardentity;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Pdutype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Pdutype::class;

  protected function instanciateModel(): \App\Models\Pdutype
  {
    return new \App\Models\Pdutype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Pdutype::class);

    $pdutype = new \App\Models\Pdutype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($pdutype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdutype = \App\Models\Pdutype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pdu type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($pdutype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/pdutypes/' . $pdutype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/pdutypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Pdutype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdutype = \App\Models\Pdutype::where('id', $id)->first();
    if (is_null($pdutype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($pdutype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdutype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pdu type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($pdutype, 'update');

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $id = intval($args['id']);
    $pdutype = \App\Models\Pdutype::withTrashed()->where('id', $id)->first();
    if (is_null($pdutype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($pdutype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdutype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/pdutypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdutype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu type has been soft deleted successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function restoreItem(Request $request, Response $response, array $args): Response
  {
    $id = intval($args['id']);
    $pdutype = \App\Models\Pdutype::withTrashed()->where('id', $id)->first();
    if (is_null($pdutype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($pdutype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdutype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
