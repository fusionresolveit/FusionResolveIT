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

final class Projecttasktype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Projecttasktype::class;

  protected function instanciateModel(): \App\Models\Projecttasktype
  {
    return new \App\Models\Projecttasktype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Projecttasktype::class);

    $projecttasktype = new \App\Models\Projecttasktype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($projecttasktype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttasktype = \App\Models\Projecttasktype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The project task type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($projecttasktype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/projecttasktypes/' . $projecttasktype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/projecttasktypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Projecttasktype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttasktype = \App\Models\Projecttasktype::where('id', $id)->first();
    if (is_null($projecttasktype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($projecttasktype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttasktype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The project task type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($projecttasktype, 'update');

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
    $projecttasktype = \App\Models\Projecttasktype::withTrashed()->where('id', $id)->first();
    if (is_null($projecttasktype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($projecttasktype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttasktype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project task type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/projecttasktypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttasktype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project task type has been soft deleted successfully');
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
    $projecttasktype = \App\Models\Projecttasktype::withTrashed()->where('id', $id)->first();
    if (is_null($projecttasktype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($projecttasktype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttasktype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project task type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
