<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostBusinesscriticity;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Businesscriticity extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Businesscriticity::class;
  protected $rootUrl2 = '/dropdowns/businesscriticities/';

  protected function instanciateModel(): \App\Models\Businesscriticity
  {
    return new \App\Models\Businesscriticity();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostBusinesscriticity((object) $request->getParsedBody());

    $businesscriticity = new \App\Models\Businesscriticity();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($businesscriticity))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $businesscriticity = \App\Models\Businesscriticity::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($businesscriticity, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/businesscriticities/' . $businesscriticity->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/businesscriticities')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostBusinesscriticity((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $businesscriticity = \App\Models\Businesscriticity::where('id', $id)->first();
    if (is_null($businesscriticity))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($businesscriticity))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $businesscriticity->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($businesscriticity, 'update');

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
    $businesscriticity = \App\Models\Businesscriticity::withTrashed()->where('id', $id)->first();
    if (is_null($businesscriticity))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($businesscriticity->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $businesscriticity->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/businesscriticities')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $businesscriticity->delete();
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
    $businesscriticity = \App\Models\Businesscriticity::withTrashed()->where('id', $id)->first();
    if (is_null($businesscriticity))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($businesscriticity->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $businesscriticity->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubBusinesscriticities(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Businesscriticity();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Businesscriticity();
    $myItem2 = $item2::where('businesscriticity_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/businesscriticities');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myBusinesscriticities = [];
    foreach ($myItem2 as $businesscriticity)
    {
      $name = $businesscriticity->name;
      if ($name == '')
      {
        $name = '(' . $businesscriticity->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/businesscriticities/', $businesscriticity->id);

      $entity = '';
      $entity_url = '';
      if ($businesscriticity->entity !== null)
      {
        $entity = $businesscriticity->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $businesscriticity->entity->id);
      }

      $comment = $businesscriticity->comment;

      $myBusinesscriticities[] = [
        'name'         => $name,
        'url'          => $url,
        'entity'       => $entity,
        'entity_url'   => $entity_url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    array_multisort(
      array_column($myBusinesscriticities, 'name'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myBusinesscriticities
    );

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('businesscriticities', $myBusinesscriticities);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('comment', npgettext('global', 'Comment', 'Comments', 2));

    return $view->render($response, 'subitem/businesscriticities.html.twig', (array)$viewData);
  }
}
