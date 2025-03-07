<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicepci;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicepci extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicepci::class;
  protected $rootUrl2 = '/devices/devicepcis/';
  protected $choose = 'devicepcis';

  protected function instanciateModel(): \App\Models\Devicepci
  {
    return new \App\Models\Devicepci();
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

    $data = new PostDevicepci((object) $request->getParsedBody());

    $devicepci = new \App\Models\Devicepci();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicepci))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepci = \App\Models\Devicepci::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pci has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicepci, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicepcis/' . $devicepci->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicepcis')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicepci((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepci = \App\Models\Devicepci::where('id', $id)->first();
    if (is_null($devicepci))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicepci))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepci->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pci has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicepci, 'update');

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
    $devicepci = \App\Models\Devicepci::withTrashed()->where('id', $id)->first();
    if (is_null($devicepci))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicepci->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepci->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pci has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicepcis')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepci->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pci has been soft deleted successfully');
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
    $devicepci = \App\Models\Devicepci::withTrashed()->where('id', $id)->first();
    if (is_null($devicepci))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicepci->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepci->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pci has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
