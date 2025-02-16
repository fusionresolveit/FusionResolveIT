<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Applianceenvironment extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Applianceenvironment::class;

  protected function instanciateModel(): \App\Models\Applianceenvironment
  {
    return new \App\Models\Applianceenvironment();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Applianceenvironment::class);

    $applianceenv = new \App\Models\Applianceenvironment();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($applianceenv))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $applianceenv = \App\Models\Applianceenvironment::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The appliance environment has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($applianceenv, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/applianceenvironments/' . $applianceenv->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/applianceenvironments')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Applianceenvironment::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $applianceenv = \App\Models\Applianceenvironment::where('id', $id)->first();
    if (is_null($applianceenv))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($applianceenv))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $applianceenv->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The appliance environment has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($applianceenv, 'update');

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
    $applianceenv = \App\Models\Applianceenvironment::withTrashed()->where('id', $id)->first();
    if (is_null($applianceenv))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($applianceenv->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $applianceenv->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The appliance environment has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/applianceenvironments')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $applianceenv->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The appliance environment has been soft deleted successfully');
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
    $applianceenv = \App\Models\Applianceenvironment::withTrashed()->where('id', $id)->first();
    if (is_null($applianceenv))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($applianceenv->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $applianceenv->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The appliance environment has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
