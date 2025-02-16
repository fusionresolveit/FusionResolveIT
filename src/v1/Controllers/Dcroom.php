<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDcroom;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Dcroom extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Externallink;
  use Document;
  use Contract;
  use Itil;
  use History;
  use Infocom;

  protected $model = \App\Models\Dcroom::class;
  protected $rootUrl2 = '/dcrooms/';

  protected function instanciateModel(): \App\Models\Dcroom
  {
    return new \App\Models\Dcroom();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDcroom((object) $request->getParsedBody());

    $dcroom = new \App\Models\Dcroom();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($dcroom))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $dcroom = \App\Models\Dcroom::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The dcroom has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($dcroom, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/dcrooms/' . $dcroom->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/dcrooms')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDcroom((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $dcroom = \App\Models\Dcroom::where('id', $id)->first();
    if (is_null($dcroom))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($dcroom))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $dcroom->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The dcroom has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($dcroom, 'update');

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
    $dcroom = \App\Models\Dcroom::withTrashed()->where('id', $id)->first();
    if (is_null($dcroom))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($dcroom->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $dcroom->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The dcroom has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/dcrooms')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $dcroom->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The dcroom has been soft deleted successfully');
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
    $dcroom = \App\Models\Dcroom::withTrashed()->where('id', $id)->first();
    if (is_null($dcroom))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($dcroom->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $dcroom->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The dcroom has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
