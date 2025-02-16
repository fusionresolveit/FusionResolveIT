<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDeviceprocessor;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Deviceprocessor extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Deviceprocessor::class;
  protected $rootUrl2 = '/devices/deviceprocessors/';
  protected $choose = 'deviceprocessors';

  protected function instanciateModel(): \App\Models\Deviceprocessor
  {
    return new \App\Models\Deviceprocessor();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers' => new \App\Models\Computer(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDeviceprocessor((object) $request->getParsedBody());

    $deviceprocessor = new \App\Models\Deviceprocessor();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($deviceprocessor))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceprocessor = \App\Models\Deviceprocessor::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The processor has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($deviceprocessor, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/deviceprocessors/' . $deviceprocessor->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/deviceprocessors')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDeviceprocessor((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceprocessor = \App\Models\Deviceprocessor::where('id', $id)->first();
    if (is_null($deviceprocessor))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($deviceprocessor))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceprocessor->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The processor has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($deviceprocessor, 'update');

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
    $deviceprocessor = \App\Models\Deviceprocessor::withTrashed()->where('id', $id)->first();
    if (is_null($deviceprocessor))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($deviceprocessor->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceprocessor->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The processor has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/deviceprocessors')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceprocessor->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The processor has been soft deleted successfully');
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
    $deviceprocessor = \App\Models\Deviceprocessor::withTrashed()->where('id', $id)->first();
    if (is_null($deviceprocessor))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($deviceprocessor->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceprocessor->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The processor has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
