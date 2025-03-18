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

final class Domainrecordtype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Domainrecordtype::class;

  protected function instanciateModel(): \App\Models\Domainrecordtype
  {
    return new \App\Models\Domainrecordtype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Domainrecordtype::class);

    $domainrecordtype = new \App\Models\Domainrecordtype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($domainrecordtype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domainrecordtype = \App\Models\Domainrecordtype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The domain record type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($domainrecordtype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/domainrecordtypes/' . $domainrecordtype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/domainrecordtypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Domainrecordtype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domainrecordtype = \App\Models\Domainrecordtype::where('id', $id)->first();
    if (is_null($domainrecordtype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($domainrecordtype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domainrecordtype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The domain record type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($domainrecordtype, 'update');

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
    $domainrecordtype = \App\Models\Domainrecordtype::withTrashed()->where('id', $id)->first();
    if (is_null($domainrecordtype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($domainrecordtype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domainrecordtype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain record type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/domainrecordtypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domainrecordtype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain record type has been soft deleted successfully');
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
    $domainrecordtype = \App\Models\Domainrecordtype::withTrashed()->where('id', $id)->first();
    if (is_null($domainrecordtype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($domainrecordtype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domainrecordtype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain record type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
