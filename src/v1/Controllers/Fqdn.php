<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostFqdn;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Fqdn extends Common
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Fqdn::class;
  protected $rootUrl2 = '/dropdowns/fqdns/';
  protected $choose = 'fqdns';

  protected function instanciateModel(): \App\Models\Fqdn
  {
    return new \App\Models\Fqdn();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostFqdn((object) $request->getParsedBody());

    $fqdn = new \App\Models\Fqdn();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($fqdn))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fqdn = \App\Models\Fqdn::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($fqdn, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/fqdns/' . $fqdn->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/fqdns')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostFqdn((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fqdn = \App\Models\Fqdn::where('id', $id)->first();
    if (is_null($fqdn))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($fqdn))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fqdn->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($fqdn, 'update');

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
    $fqdn = \App\Models\Fqdn::withTrashed()->where('id', $id)->first();
    if (is_null($fqdn))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($fqdn->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fqdn->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/fqdns')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fqdn->delete();
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
    $fqdn = \App\Models\Fqdn::withTrashed()->where('id', $id)->first();
    if (is_null($fqdn))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($fqdn->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fqdn->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
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
    $item = new \App\Models\Fqdn();
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

      $computername = '';
      $computername_url = '';
      $networkname = \App\Models\Networkname::where('id', $current_item->networkname_id)->first();
      if ($networkname !== null)
      {
        $computername = $networkname->name . '.' . $myItem->fqdn;
        $computername_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/networkname/',
          $current_item->networkname_id
        );
      }

      $comment = $current_item->comment;

      $myNetworkAlias[] = [
        'name'                => $name,
        'url'                 => $url,
        'computername'        => $computername,
        'computername_url'    => $computername_url,
        'comment'             => $comment,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myNetworkAlias, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myNetworkAlias);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('networkalias', $myNetworkAlias);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('networkalias', npgettext('network', 'Network alias', 'Network aliases', 1));
    $viewData->addTranslation('computername', pgettext('inventory device', "Computer's name"));
    $viewData->addTranslation('comment', npgettext('global', 'Comment', 'Comments', 2));

    return $view->render($response, 'subitem/networkalias.html.twig', (array)$viewData);
  }
}
