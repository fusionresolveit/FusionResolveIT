<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostVlan;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Vlan extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Vlan::class;

  protected function instanciateModel(): \App\Models\Vlan
  {
    return new \App\Models\Vlan();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostVlan((object) $request->getParsedBody());

    $vlan = new \App\Models\Vlan();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($vlan))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $vlan = \App\Models\Vlan::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The vlan has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($vlan, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/vlans/' . $vlan->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/vlans')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostVlan((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $vlan = \App\Models\Vlan::where('id', $id)->first();
    if (is_null($vlan))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($vlan))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $vlan->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The vlan has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($vlan, 'update');

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
    $vlan = \App\Models\Vlan::withTrashed()->where('id', $id)->first();
    if (is_null($vlan))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($vlan->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $vlan->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The vlan has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/vlans')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $vlan->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The vlan has been soft deleted successfully');
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
    $vlan = \App\Models\Vlan::withTrashed()->where('id', $id)->first();
    if (is_null($vlan))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($vlan->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $vlan->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The vlan has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
