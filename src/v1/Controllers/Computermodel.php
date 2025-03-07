<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostComputermodel;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Computermodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Computermodel::class;

  protected function instanciateModel(): \App\Models\Computermodel
  {
    return new \App\Models\Computermodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostComputermodel((object) $request->getParsedBody());

    $computermodel = new \App\Models\Computermodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($computermodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computermodel = \App\Models\Computermodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The computer model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($computermodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/computermodels/' . $computermodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/computermodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostComputermodel((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computermodel = \App\Models\Computermodel::where('id', $id)->first();
    if (is_null($computermodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($computermodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computermodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The computer model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($computermodel, 'update');

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
    $computermodel = \App\Models\Computermodel::withTrashed()->where('id', $id)->first();
    if (is_null($computermodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($computermodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computermodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/computermodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computermodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer model has been soft deleted successfully');
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
    $computermodel = \App\Models\Computermodel::withTrashed()->where('id', $id)->first();
    if (is_null($computermodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($computermodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computermodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
