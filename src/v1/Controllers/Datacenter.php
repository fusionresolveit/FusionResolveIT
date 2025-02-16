<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDatacenter;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Datacenter extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Datacenter::class;
  protected $rootUrl2 = '/datacenters/';

  protected function instanciateModel(): \App\Models\Datacenter
  {
    return new \App\Models\Datacenter();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDatacenter((object) $request->getParsedBody());

    $datacenter = new \App\Models\Datacenter();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($datacenter))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $datacenter = \App\Models\Datacenter::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The datacenter has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($datacenter, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/datacenters/' . $datacenter->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/datacenters')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDatacenter((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $datacenter = \App\Models\Datacenter::where('id', $id)->first();
    if (is_null($datacenter))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($datacenter))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $datacenter->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The datacenter has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($datacenter, 'update');

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
    $datacenter = \App\Models\Datacenter::withTrashed()->where('id', $id)->first();
    if (is_null($datacenter))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($datacenter->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $datacenter->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The datacenter has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/datacenters')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $datacenter->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The datacenter has been soft deleted successfully');
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
    $datacenter = \App\Models\Datacenter::withTrashed()->where('id', $id)->first();
    if (is_null($datacenter))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($datacenter->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $datacenter->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The datacenter has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubDcrooms(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Datacenter();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/dcrooms');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDcrooms = [];
    foreach ($myItem->dcrooms as $current_dcroom)
    {
      $name = $current_dcroom->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dcrooms/', $current_dcroom->id);

      $myDcrooms[] = [
        'name'       => $name,
        'url'        => $url,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myDcrooms, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myDcrooms);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('dcrooms', $myDcrooms);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/dcrooms.html.twig', (array)$viewData);
  }
}
