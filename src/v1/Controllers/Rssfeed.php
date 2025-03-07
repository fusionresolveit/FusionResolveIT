<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostRssfeed;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Rssfeed extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Rssfeed::class;

  protected function instanciateModel(): \App\Models\Rssfeed
  {
    return new \App\Models\Rssfeed();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostRssfeed((object) $request->getParsedBody());

    $rssfeed = new \App\Models\Rssfeed();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($rssfeed))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rssfeed = \App\Models\Rssfeed::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The RSS feed has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($rssfeed, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/rssfeeds/' . $rssfeed->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/rssfeeds')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostRssfeed((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rssfeed = \App\Models\Rssfeed::where('id', $id)->first();
    if (is_null($rssfeed))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($rssfeed))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rssfeed->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The RSS feed has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($rssfeed, 'update');

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
    $rssfeed = \App\Models\Rssfeed::withTrashed()->where('id', $id)->first();
    if (is_null($rssfeed))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($rssfeed->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rssfeed->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The RSS feed has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/rssfeeds')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rssfeed->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The RSS feed has been soft deleted successfully');
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
    $rssfeed = \App\Models\Rssfeed::withTrashed()->where('id', $id)->first();
    if (is_null($rssfeed))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($rssfeed->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rssfeed->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The RSS feed has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
