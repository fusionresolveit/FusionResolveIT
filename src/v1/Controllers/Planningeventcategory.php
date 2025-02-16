<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPlanningeventcategory;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Planningeventcategory extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Planningeventcategory::class;

  protected function instanciateModel(): \App\Models\Planningeventcategory
  {
    return new \App\Models\Planningeventcategory();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPlanningeventcategory((object) $request->getParsedBody());

    $planningeventcategory = new \App\Models\Planningeventcategory();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($planningeventcategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $planningeventcategory = \App\Models\Planningeventcategory::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The planning event category has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($planningeventcategory, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/planningeventcategories/' . $planningeventcategory->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/planningeventcategories')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPlanningeventcategory((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $planningeventcategory = \App\Models\Planningeventcategory::where('id', $id)->first();
    if (is_null($planningeventcategory))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($planningeventcategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $planningeventcategory->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The planning event category has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($planningeventcategory, 'update');

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
    $planningeventcategory = \App\Models\Planningeventcategory::withTrashed()->where('id', $id)->first();
    if (is_null($planningeventcategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($planningeventcategory->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $planningeventcategory->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The planning event category has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/planningeventcategories')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $planningeventcategory->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The planning event category has been soft deleted successfully');
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
    $planningeventcategory = \App\Models\Planningeventcategory::withTrashed()->where('id', $id)->first();
    if (is_null($planningeventcategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($planningeventcategory->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $planningeventcategory->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The planning event category has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
