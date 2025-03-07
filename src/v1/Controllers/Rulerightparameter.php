<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostRulerightparameter;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Rulerightparameter extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Rulerightparameter::class;

  protected function instanciateModel(): \App\Models\Rulerightparameter
  {
    return new \App\Models\Rulerightparameter();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostRulerightparameter((object) $request->getParsedBody());

    $rulerightparameter = new \App\Models\Rulerightparameter();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($rulerightparameter))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rulerightparameter = \App\Models\Rulerightparameter::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The rule right parameter has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($rulerightparameter, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/rulerightparameters/' . $rulerightparameter->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/rulerightparameters')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostRulerightparameter((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rulerightparameter = \App\Models\Rulerightparameter::where('id', $id)->first();
    if (is_null($rulerightparameter))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($rulerightparameter))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rulerightparameter->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The rule right parameter has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($rulerightparameter, 'update');

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
    $rulerightparameter = \App\Models\Rulerightparameter::withTrashed()->where('id', $id)->first();
    if (is_null($rulerightparameter))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($rulerightparameter->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rulerightparameter->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rule right parameter has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/rulerightparameters')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rulerightparameter->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rule right parameter has been soft deleted successfully');
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
    $rulerightparameter = \App\Models\Rulerightparameter::withTrashed()->where('id', $id)->first();
    if (is_null($rulerightparameter))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($rulerightparameter->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rulerightparameter->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rule right parameter has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
