<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostLink;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Link extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Link::class;
  protected $rootUrl2 = '/links/';
  protected $choose = 'links';

  protected function instanciateModel(): \App\Models\Link
  {
    return new \App\Models\Link();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostLink((object) $request->getParsedBody());

    $link = new \App\Models\Link();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($link))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $link = \App\Models\Link::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The link has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($link, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/links/' . $link->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/links')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostLink((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $link = \App\Models\Link::where('id', $id)->first();
    if (is_null($link))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($link))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $link->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The link has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($link, 'update');

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
    $link = \App\Models\Link::withTrashed()->where('id', $id)->first();
    if (is_null($link))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($link->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $link->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The link has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/links')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $link->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The link has been soft deleted successfully');
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
    $link = \App\Models\Link::withTrashed()->where('id', $id)->first();
    if (is_null($link))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($link->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $link->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The link has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubAssociatedItemType(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Link();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\LinkItemtype();
    $myItem2 = $item2::where('link_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/associateditemtypes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAssociatedItemType = [];
    foreach ($myItem2 as $current_item)
    {
      if (class_exists($current_item->item_type))
      {
        $item3 = new $current_item->item_type();

        if (is_subclass_of($item3, \App\Models\Common::class))
        {
          $type = $item3->getTitle();

          $myAssociatedItemType[$type] = [
            'type'    => $type,
          ];
        }
      }
    }

    // tri ordre alpha
    array_multisort(
      array_column($myAssociatedItemType, 'type'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myAssociatedItemType
    );

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myAssociatedItemType);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }
}
