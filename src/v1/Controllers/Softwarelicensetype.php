<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSoftwarelicensetype;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Softwarelicensetype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Softwarelicensetype::class;
  protected $rootUrl2 = '/dropdowns/softwarelicensetypes/';

  protected function instanciateModel(): \App\Models\Softwarelicensetype
  {
    return new \App\Models\Softwarelicensetype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSoftwarelicensetype((object) $request->getParsedBody());

    $softwarelicensetype = new \App\Models\Softwarelicensetype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($softwarelicensetype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarelicensetype = \App\Models\Softwarelicensetype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($softwarelicensetype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/softwarelicensetypes/' . $softwarelicensetype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/softwarelicensetypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSoftwarelicensetype((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarelicensetype = \App\Models\Softwarelicensetype::where('id', $id)->first();
    if (is_null($softwarelicensetype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($softwarelicensetype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarelicensetype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($softwarelicensetype, 'update');

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
    $softwarelicensetype = \App\Models\Softwarelicensetype::withTrashed()->where('id', $id)->first();
    if (is_null($softwarelicensetype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($softwarelicensetype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarelicensetype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/softwarelicensetypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarelicensetype->delete();
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
    $softwarelicensetype = \App\Models\Softwarelicensetype::withTrashed()->where('id', $id)->first();
    if (is_null($softwarelicensetype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($softwarelicensetype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarelicensetype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubLicencetypes(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Softwarelicensetype();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Softwarelicensetype();
    $myItem2 = $item2::where('softwarelicensetype_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/licencetypes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myLicencetypes = [];
    foreach ($myItem2 as $softwarelicensetype)
    {
      $name = $softwarelicensetype->name;
      if ($name == '')
      {
        $name = '(' . $softwarelicensetype->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/softwarelicensetypes/', $softwarelicensetype->id);

      $entity = '';
      $entity_url = '';
      if ($softwarelicensetype->entity !== null)
      {
        $entity = $softwarelicensetype->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $softwarelicensetype->entity->id);
      }

      $comment = $softwarelicensetype->comment;

      $myLicencetypes[] = [
        'name'         => $name,
        'url'          => $url,
        'entity'       => $entity,
        'entity_url'   => $entity_url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    array_multisort(
      array_column($myLicencetypes, 'name'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myLicencetypes
    );

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('licensetypes', $myLicencetypes);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('comment', npgettext('global', 'Comment', 'Comments', 2));

    return $view->render($response, 'subitem/softwarelicensetypes.html.twig', (array)$viewData);
  }
}
