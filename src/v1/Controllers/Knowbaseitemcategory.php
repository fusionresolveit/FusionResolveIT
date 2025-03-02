<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostKnowbaseitemcategory;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Knowbaseitemcategory extends Common
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Knowbaseitemcategory::class;
  protected $rootUrl2 = '/dropdowns/knowbaseitemcategories/';

  protected function instanciateModel(): \App\Models\Knowbaseitemcategory
  {
    return new \App\Models\Knowbaseitemcategory();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostKnowbaseitemcategory((object) $request->getParsedBody());

    $knowbaseitemcategory = new \App\Models\Knowbaseitemcategory();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($knowbaseitemcategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowbaseitemcategory = \App\Models\Knowbaseitemcategory::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The knowbase category has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($knowbaseitemcategory, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/knowbaseitemcategories/' . $knowbaseitemcategory->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/knowbaseitemcategories')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostKnowbaseitemcategory((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowbaseitemcategory = \App\Models\Knowbaseitemcategory::where('id', $id)->first();
    if (is_null($knowbaseitemcategory))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($knowbaseitemcategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowbaseitemcategory->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The knowbase category has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($knowbaseitemcategory, 'update');

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
    $knowbaseitemcategory = \App\Models\Knowbaseitemcategory::withTrashed()->where('id', $id)->first();
    if (is_null($knowbaseitemcategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($knowbaseitemcategory->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowbaseitemcategory->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The knowbase category has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/knowbaseitemcategories')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowbaseitemcategory->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The knowbase category has been soft deleted successfully');
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
    $knowbaseitemcategory = \App\Models\Knowbaseitemcategory::withTrashed()->where('id', $id)->first();
    if (is_null($knowbaseitemcategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($knowbaseitemcategory->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowbaseitemcategory->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The knowbase category has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubKnowbaseitemcategories(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Knowbaseitemcategory();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Knowbaseitemcategory();
    $myItem2 = $item2::where('knowbaseitemcategory_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/knowbaseitemcategories');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myBusinesscriticities = [];
    foreach ($myItem2 as $knowbaseitemcategory)
    {
      $name = $knowbaseitemcategory->name;
      if ($name == '')
      {
        $name = '(' . $knowbaseitemcategory->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/knowbaseitemcategories/', $knowbaseitemcategory->id);

      $entity = '';
      $entity_url = '';
      if ($knowbaseitemcategory->entity !== null)
      {
        $entity = $knowbaseitemcategory->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $knowbaseitemcategory->entity->id);
      }

      $comment = $knowbaseitemcategory->comment;

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
    $viewData->addData('knowbaseitemcategories', $myBusinesscriticities);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/knowbaseitemcategories.html.twig', (array)$viewData);
  }
}
