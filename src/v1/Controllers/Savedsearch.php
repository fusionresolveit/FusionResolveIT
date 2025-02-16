<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSavedsearch;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Savedsearch extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  protected $model = \App\Models\Savedsearch::class;

  protected function instanciateModel(): \App\Models\Savedsearch
  {
    return new \App\Models\Savedsearch();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSavedsearch((object) $request->getParsedBody());

    $savedsearch = new \App\Models\Savedsearch();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($savedsearch))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $savedsearch = \App\Models\Savedsearch::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The savedsearch has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($savedsearch, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/savedsearchs/' . $savedsearch->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/savedsearchs')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSavedsearch((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $savedsearch = \App\Models\Savedsearch::where('id', $id)->first();
    if (is_null($savedsearch))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($savedsearch))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $savedsearch->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The savedsearch has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($savedsearch, 'update');

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
    $savedsearch = \App\Models\Savedsearch::withTrashed()->where('id', $id)->first();
    if (is_null($savedsearch))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($savedsearch->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $savedsearch->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The savedsearch has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/savedsearchs')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $savedsearch->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The savedsearch has been soft deleted successfully');
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
    $savedsearch = \App\Models\Savedsearch::withTrashed()->where('id', $id)->first();
    if (is_null($savedsearch))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($savedsearch->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $savedsearch->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The savedsearch has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
