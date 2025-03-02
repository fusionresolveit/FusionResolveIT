<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSoftwarecategory;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Softwarecategory extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Softwarecategory::class;
  protected $rootUrl2 = '/dropdowns/softwarecategories/';

  protected function instanciateModel(): \App\Models\Softwarecategory
  {
    return new \App\Models\Softwarecategory();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSoftwarecategory((object) $request->getParsedBody());

    $softwarecategory = new \App\Models\Softwarecategory();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($softwarecategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarecategory = \App\Models\Softwarecategory::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The software category has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($softwarecategory, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/softwarecategorys/' . $softwarecategory->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/softwarecategorys')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSoftwarecategory((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarecategory = \App\Models\Softwarecategory::where('id', $id)->first();
    if (is_null($softwarecategory))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($softwarecategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarecategory->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The software category has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($softwarecategory, 'update');

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
    $softwarecategory = \App\Models\Softwarecategory::withTrashed()->where('id', $id)->first();
    if (is_null($softwarecategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($softwarecategory->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarecategory->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software category has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/softwarecategorys')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarecategory->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software category has been soft deleted successfully');
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
    $softwarecategory = \App\Models\Softwarecategory::withTrashed()->where('id', $id)->first();
    if (is_null($softwarecategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($softwarecategory->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarecategory->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software category has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubSoftwarecategories(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Softwarecategory();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Softwarecategory();
    $myItem2 = $item2::where('softwarecategory_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/softwarecategories');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySoftwarecategories = [];
    foreach ($myItem2 as $softwarecategory)
    {
      $name = $softwarecategory->name;
      if ($name == '')
      {
        $name = '(' . $softwarecategory->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/softwarecategories/', $softwarecategory->id);

      $comment = $softwarecategory->comment;

      $mySoftwarecategories[] = [
        'name'         => $name,
        'url'          => $url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    array_multisort(
      array_column($mySoftwarecategories, 'name'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $mySoftwarecategories
    );

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwarecategories', $mySoftwarecategories);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/softwarecategories.html.twig', (array)$viewData);
  }
}
