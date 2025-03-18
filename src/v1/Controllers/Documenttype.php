<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDocumenttype;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Documenttype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Documenttype::class;

  protected function instanciateModel(): \App\Models\Documenttype
  {
    return new \App\Models\Documenttype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDocumenttype((object) $request->getParsedBody());

    $documenttype = new \App\Models\Documenttype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($documenttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $documenttype = \App\Models\Documenttype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The document type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($documenttype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/documenttypes/' . $documenttype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/documenttypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDocumenttype((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $documenttype = \App\Models\Documenttype::where('id', $id)->first();
    if (is_null($documenttype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($documenttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $documenttype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The document type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($documenttype, 'update');

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
    $documenttype = \App\Models\Documenttype::withTrashed()->where('id', $id)->first();
    if (is_null($documenttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($documenttype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $documenttype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The document type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/documenttypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $documenttype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The document type has been soft deleted successfully');
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
    $documenttype = \App\Models\Documenttype::withTrashed()->where('id', $id)->first();
    if (is_null($documenttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($documenttype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $documenttype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The document type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
