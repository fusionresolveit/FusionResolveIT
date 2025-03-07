<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostWifinetwork;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Wifinetwork extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Wifinetwork::class;

  protected function instanciateModel(): \App\Models\Wifinetwork
  {
    return new \App\Models\Wifinetwork();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostWifinetwork((object) $request->getParsedBody());

    $wifinetwork = new \App\Models\Wifinetwork();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($wifinetwork))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $wifinetwork = \App\Models\Wifinetwork::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The wifi network has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($wifinetwork, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/wifinetworks/' . $wifinetwork->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/wifinetworks')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostWifinetwork((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $wifinetwork = \App\Models\Wifinetwork::where('id', $id)->first();
    if (is_null($wifinetwork))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($wifinetwork))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $wifinetwork->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The wifi network has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($wifinetwork, 'update');

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
    $wifinetwork = \App\Models\Wifinetwork::withTrashed()->where('id', $id)->first();
    if (is_null($wifinetwork))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($wifinetwork->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $wifinetwork->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The wifi network has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/wifinetworks')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $wifinetwork->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The wifi network has been soft deleted successfully');
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
    $wifinetwork = \App\Models\Wifinetwork::withTrashed()->where('id', $id)->first();
    if (is_null($wifinetwork))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($wifinetwork->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $wifinetwork->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The wifi network has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
