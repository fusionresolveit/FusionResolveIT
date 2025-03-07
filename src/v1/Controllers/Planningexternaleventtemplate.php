<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPlanningexternaleventtemplate;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Planningexternaleventtemplate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Planningexternaleventtemplate::class;

  protected function instanciateModel(): \App\Models\Planningexternaleventtemplate
  {
    return new \App\Models\Planningexternaleventtemplate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPlanningexternaleventtemplate((object) $request->getParsedBody());

    $planningexternaleventtemplate = new \App\Models\Planningexternaleventtemplate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($planningexternaleventtemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $planningexternaleventtemplate = \App\Models\Planningexternaleventtemplate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage(
      'The planning external event template has been created successfully'
    );
    \App\v1\Controllers\Notification::prepareNotification($planningexternaleventtemplate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader(
          'Location',
          $basePath . '/view/planningexternaleventtemplates/' . $planningexternaleventtemplate->id
        )
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/planningexternaleventtemplates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPlanningexternaleventtemplate((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $planningexternaleventtemplate = \App\Models\Planningexternaleventtemplate::where('id', $id)->first();
    if (is_null($planningexternaleventtemplate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($planningexternaleventtemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $planningexternaleventtemplate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage(
      'The planning external event template has been updated successfully'
    );
    \App\v1\Controllers\Notification::prepareNotification($planningexternaleventtemplate, 'update');

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
    $planningexternaleventtemplate = \App\Models\Planningexternaleventtemplate::
        withTrashed()
      ->where('id', $id)
      ->first();
    if (is_null($planningexternaleventtemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($planningexternaleventtemplate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $planningexternaleventtemplate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage(
        'The planning external event template has been deleted successfully'
      );

      return $response
        ->withHeader('Location', $basePath . '/view/planningexternaleventtemplates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $planningexternaleventtemplate->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage(
        'The planning external event template has been soft deleted successfully'
      );
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
    $planningexternaleventtemplate = \App\Models\Planningexternaleventtemplate::
        withTrashed()
      ->where('id', $id)
      ->first();
    if (is_null($planningexternaleventtemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($planningexternaleventtemplate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $planningexternaleventtemplate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage(
        'The planning external event template has been restored successfully'
      );
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
