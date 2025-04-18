<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSoftwareversion;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Softwareversion extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  protected $model = \App\Models\Softwareversion::class;
  protected $rootUrl2 = '/softwareversions/';

  protected function instanciateModel(): \App\Models\Softwareversion
  {
    return new \App\Models\Softwareversion();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSoftwareversion((object) $request->getParsedBody());

    $softwareversion = new \App\Models\Softwareversion();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($softwareversion))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwareversion = \App\Models\Softwareversion::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The software version has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($softwareversion, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/softwareversions/' . $softwareversion->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/softwareversions')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSoftwareversion((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwareversion = \App\Models\Softwareversion::where('id', $id)->first();
    if (is_null($softwareversion))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($softwareversion))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwareversion->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The software version has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($softwareversion, 'update');

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
    $softwareversion = \App\Models\Softwareversion::withTrashed()->where('id', $id)->first();
    if (is_null($softwareversion))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($softwareversion->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwareversion->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software version has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/softwareversions')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwareversion->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software version has been soft deleted successfully');
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
    $softwareversion = \App\Models\Softwareversion::withTrashed()->where('id', $id)->first();
    if (is_null($softwareversion))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($softwareversion->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwareversion->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software version has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
