<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostNetworkname;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Networkname extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Networkname::class;
  protected $rootUrl2 = '/dropdowns/networknames/';
  protected $choose = 'networknames';

  protected function instanciateModel(): \App\Models\Networkname
  {
    return new \App\Models\Networkname();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostNetworkname((object) $request->getParsedBody());

    $networkname = new \App\Models\Networkname();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($networkname))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkname = \App\Models\Networkname::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The network name has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkname, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/networknames/' . $networkname->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/networknames')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostNetworkname((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkname = \App\Models\Networkname::where('id', $id)->first();
    if (is_null($networkname))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($networkname))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkname->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The network name has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkname, 'update');

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
    $networkname = \App\Models\Networkname::withTrashed()->where('id', $id)->first();
    if (is_null($networkname))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkname->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkname->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network name has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/networknames')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkname->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network name has been soft deleted successfully');
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
    $networkname = \App\Models\Networkname::withTrashed()->where('id', $id)->first();
    if (is_null($networkname))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkname->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkname->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network name has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubNetworkalias(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Networkname();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/networkalias');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myNetworkAlias = [];
    foreach ($myItem->alias as $current_item)
    {
      $name = $current_item->name;

      $url = '';
      // $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/networkalias/', $current_item->id);    // TODO

      $domain = '';
      $domain_url = '';
      if ($myItem->fqdn !== null)
      {
        $domain = $myItem->fqdn->name;
        $domain_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/fqdns/', $myItem->fqdn->id);
      }

      $entity = '';
      $entity_url = '';
      if ($current_item->entity !== null)
      {
        $entity = $current_item->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $current_item->entity->id);
      }

      $comment = $current_item->comment;

      $myNetworkAlias[] = [
        'name'            => $name,
        'url'             => $url,
        'domain'          => $domain,
        'domain_url'      => $domain_url,
        'entity'          => $entity,
        'entity_url'      => $entity_url,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myNetworkAlias, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myNetworkAlias);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('networkalias', $myNetworkAlias);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translatePlural('Name', 'Names', 1));
    $viewData->addTranslation('domain', $translator->translatePlural('Internet domain', 'Internet domains', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));

    return $view->render($response, 'subitem/networkalias.html.twig', (array)$viewData);
  }
}
