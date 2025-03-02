<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicebatterytype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  protected $model = \App\Models\Devicebatterytype::class;

  protected function instanciateModel(): \App\Models\Devicebatterytype
  {
    return new \App\Models\Devicebatterytype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicebatterytype::class);

    $devicebatterytype = new \App\Models\Devicebatterytype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicebatterytype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicebatterytype = \App\Models\Devicebatterytype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The battery type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicebatterytype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
     {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicebatterytypes/' . $devicebatterytype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicebatterytypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicebatterytype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicebatterytype = \App\Models\Devicebatterytype::where('id', $id)->first();
    if (is_null($devicebatterytype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicebatterytype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicebatterytype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The battery type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicebatterytype, 'update');

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
    $devicebatterytype = \App\Models\Devicebatterytype::withTrashed()->where('id', $id)->first();
    if (is_null($devicebatterytype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicebatterytype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicebatterytype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The battery type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicebatterytypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicebatterytype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The battery type has been soft deleted successfully');
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
    $devicebatterytype = \App\Models\Devicebatterytype::withTrashed()->where('id', $id)->first();
    if (is_null($devicebatterytype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicebatterytype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicebatterytype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The battery type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
