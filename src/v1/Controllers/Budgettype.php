<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Budgettype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Budgettype::class;

  protected function instanciateModel(): \App\Models\Budgettype
  {
    return new \App\Models\Budgettype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Budgettype::class);

    $budgettype = new \App\Models\Budgettype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($budgettype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $budgettype = \App\Models\Budgettype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The budget type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($budgettype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/budgettypes/' . $budgettype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/budgettypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Budgettype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $budgettype = \App\Models\Budgettype::where('id', $id)->first();
    if (is_null($budgettype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($budgettype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $budgettype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The budget type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($budgettype, 'update');

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
    $budgettype = \App\Models\Budgettype::withTrashed()->where('id', $id)->first();
    if (is_null($budgettype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($budgettype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $budgettype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The budget type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/budgettypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $budgettype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The budget type has been soft deleted successfully');
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
    $budgettype = \App\Models\Budgettype::withTrashed()->where('id', $id)->first();
    if (is_null($budgettype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($budgettype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $budgettype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The budget type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
