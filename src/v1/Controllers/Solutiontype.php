<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardentity;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Solutiontype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Solutiontype::class;

  protected function instanciateModel(): \App\Models\Solutiontype
  {
    return new \App\Models\Solutiontype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Solutiontype::class);

    $solutiontype = new \App\Models\Solutiontype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($solutiontype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $solutiontype = \App\Models\Solutiontype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The solution type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($solutiontype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/solutiontypes/' . $solutiontype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/solutiontypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Solutiontype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $solutiontype = \App\Models\Solutiontype::where('id', $id)->first();
    if (is_null($solutiontype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($solutiontype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $solutiontype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The solution type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($solutiontype, 'update');

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
    $solutiontype = \App\Models\Solutiontype::withTrashed()->where('id', $id)->first();
    if (is_null($solutiontype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($solutiontype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $solutiontype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The solution type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/solutiontypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $solutiontype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The solution type has been soft deleted successfully');
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
    $solutiontype = \App\Models\Solutiontype::withTrashed()->where('id', $id)->first();
    if (is_null($solutiontype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($solutiontype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $solutiontype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The solution type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
