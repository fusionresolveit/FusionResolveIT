<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostKnowbaseitem;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Knowbaseitem extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Knowbaseitem::class;
  protected $rootUrl2 = '/knowbaseitems/';
  protected $choose = 'knowbaseitems';

  protected function instanciateModel(): \App\Models\Knowbaseitem
  {
    return new \App\Models\Knowbaseitem();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostKnowbaseitem((object) $request->getParsedBody());

    $knowbaseitem = new \App\Models\Knowbaseitem();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($knowbaseitem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowbaseitem = \App\Models\Knowbaseitem::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The knowbaseitem has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($knowbaseitem, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/knowbaseitems/' . $knowbaseitem->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/knowbaseitems')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostKnowbaseitem((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowbaseitem = \App\Models\Knowbaseitem::where('id', $id)->first();
    if (is_null($knowbaseitem))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($knowbaseitem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowbaseitem->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The knowbaseitem has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($knowbaseitem, 'update');

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
    $knowbaseitem = \App\Models\Knowbaseitem::withTrashed()->where('id', $id)->first();
    if (is_null($knowbaseitem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($knowbaseitem->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowbaseitem->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The knowbaseitem has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/knowbaseitems')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowbaseitem->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The knowbaseitem has been soft deleted successfully');
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
    $knowbaseitem = \App\Models\Knowbaseitem::withTrashed()->where('id', $id)->first();
    if (is_null($knowbaseitem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($knowbaseitem->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowbaseitem->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The knowbaseitem has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
