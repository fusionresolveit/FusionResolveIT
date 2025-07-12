<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostIpnetwork;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Ipnetwork extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Ipnetwork::class;
  protected $rootUrl2 = '/dropdowns/ipnetwork/';

  protected function instanciateModel(): \App\Models\Ipnetwork
  {
    return new \App\Models\Ipnetwork();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostIpnetwork((object) $request->getParsedBody());

    $ipnetwork = new \App\Models\Ipnetwork();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($ipnetwork))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $ipnetwork = \App\Models\Ipnetwork::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($ipnetwork, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/ipnetworks/' . $ipnetwork->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/ipnetworks')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostIpnetwork((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $ipnetwork = \App\Models\Ipnetwork::where('id', $id)->first();
    if (is_null($ipnetwork))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($ipnetwork))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $ipnetwork->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($ipnetwork, 'update');

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
    $ipnetwork = \App\Models\Ipnetwork::withTrashed()->where('id', $id)->first();
    if (is_null($ipnetwork))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($ipnetwork->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ipnetwork->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/ipnetworks')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ipnetwork->delete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('softdeleted');
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
    $ipnetwork = \App\Models\Ipnetwork::withTrashed()->where('id', $id)->first();
    if (is_null($ipnetwork))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($ipnetwork->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ipnetwork->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubVlans(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Ipnetwork();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/vlans');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myVlans = [];
    foreach ($myItem->vlans as $vlan)
    {
      $name = $vlan->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/vlans/', $vlan->id);

      $entity = '';
      $entity_url = '';
      if ($vlan->entity !== null)
      {
        $entity = $vlan->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $vlan->entity->id);
      }

      $tagid = $vlan->tag;

      $myVlans[] = [
        'name'          => $name,
        'url'           => $url,
        'entity'        => $entity,
        'entity_url'    => $entity_url,
        'tagid'         => $tagid,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myVlans, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myVlans);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('vlans', $myVlans);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('tagid', pgettext('network', 'ID TAG'));

    return $view->render($response, 'subitem/vlans.html.twig', (array)$viewData);
  }
}
