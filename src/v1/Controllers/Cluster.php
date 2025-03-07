<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostCluster;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Appliance;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use App\Traits\Subs\Itil;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Cluster extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Appliance;
  use Document;
  use Contract;
  use Itil;
  use History;
  use Item;

  protected $model = \App\Models\Cluster::class;
  protected $rootUrl2 = '/clusters/';
  protected $choose = 'clusters';

  protected function instanciateModel(): \App\Models\Cluster
  {
    return new \App\Models\Cluster();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *          'itemNetworkequipments': \App\Models\Networkequipment,
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'         => new \App\Models\Computer(),
      'itemNetworkequipments' => new \App\Models\Networkequipment(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostCluster((object) $request->getParsedBody());

    $cluster = new \App\Models\Cluster();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($cluster))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cluster = \App\Models\Cluster::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The cluster has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($cluster, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/clusters/' . $cluster->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/clusters')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostCluster((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cluster = \App\Models\Cluster::where('id', $id)->first();
    if (is_null($cluster))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($cluster))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cluster->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The cluster has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($cluster, 'update');

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
    $cluster = \App\Models\Cluster::withTrashed()->where('id', $id)->first();
    if (is_null($cluster))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($cluster->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cluster->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cluster has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/clusters')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cluster->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cluster has been soft deleted successfully');
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
    $cluster = \App\Models\Cluster::withTrashed()->where('id', $id)->first();
    if (is_null($cluster))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($cluster->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cluster->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cluster has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
