<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostProjectstate;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Projectstate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Projectstate::class;

  protected function instanciateModel(): \App\Models\Projectstate
  {
    return new \App\Models\Projectstate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostProjectstate((object) $request->getParsedBody());

    $projectstate = new \App\Models\Projectstate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($projectstate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projectstate = \App\Models\Projectstate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The project state has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($projectstate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/projectstates/' . $projectstate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/projectstates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostProjectstate((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projectstate = \App\Models\Projectstate::where('id', $id)->first();
    if (is_null($projectstate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($projectstate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projectstate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The project state has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($projectstate, 'update');

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
    $projectstate = \App\Models\Projectstate::withTrashed()->where('id', $id)->first();
    if (is_null($projectstate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($projectstate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projectstate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project state has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/projectstates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projectstate->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project state has been soft deleted successfully');
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
    $projectstate = \App\Models\Projectstate::withTrashed()->where('id', $id)->first();
    if (is_null($projectstate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($projectstate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projectstate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project state has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
