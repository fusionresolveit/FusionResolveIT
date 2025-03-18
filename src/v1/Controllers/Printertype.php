<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Printertype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Printertype::class;

  protected function instanciateModel(): \App\Models\Printertype
  {
    return new \App\Models\Printertype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Printertype::class);

    $printertype = new \App\Models\Printertype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($printertype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $printertype = \App\Models\Printertype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The printer type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($printertype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/printertypes/' . $printertype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/printertypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Printertype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $printertype = \App\Models\Printertype::where('id', $id)->first();
    if (is_null($printertype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($printertype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $printertype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The printer type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($printertype, 'update');

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
    $printertype = \App\Models\Printertype::withTrashed()->where('id', $id)->first();
    if (is_null($printertype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($printertype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $printertype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The printer type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/printertypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $printertype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The printer type has been soft deleted successfully');
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
    $printertype = \App\Models\Printertype::withTrashed()->where('id', $id)->first();
    if (is_null($printertype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($printertype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $printertype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The printer type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
