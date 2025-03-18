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

final class Ssovariable extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Ssovariable::class;

  protected function instanciateModel(): \App\Models\Ssovariable
  {
    return new \App\Models\Ssovariable();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Ssovariable::class);

    $ssovariable = new \App\Models\Ssovariable();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($ssovariable))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $ssovariable = \App\Models\Ssovariable::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The SSO variable has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($ssovariable, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/ssovariables/' . $ssovariable->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/ssovariables')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Ssovariable::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $ssovariable = \App\Models\Ssovariable::where('id', $id)->first();
    if (is_null($ssovariable))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($ssovariable))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $ssovariable->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The SSO variable has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($ssovariable, 'update');

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
    $ssovariable = \App\Models\Ssovariable::withTrashed()->where('id', $id)->first();
    if (is_null($ssovariable))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($ssovariable->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ssovariable->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The SSO variable has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/ssovariables')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ssovariable->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The SSO variable has been soft deleted successfully');
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
    $ssovariable = \App\Models\Ssovariable::withTrashed()->where('id', $id)->first();
    if (is_null($ssovariable))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($ssovariable->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ssovariable->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The SSO variable has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
