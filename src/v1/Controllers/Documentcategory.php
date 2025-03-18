<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDocumentcategory;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Documentcategory extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Documentcategory::class;
  protected $rootUrl2 = '/dropdowns/documentcategories/';

  protected function instanciateModel(): \App\Models\Documentcategory
  {
    return new \App\Models\Documentcategory();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDocumentcategory((object) $request->getParsedBody());

    $documentcategory = new \App\Models\Documentcategory();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($documentcategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $documentcategory = \App\Models\Documentcategory::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The document category has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($documentcategory, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/documentcategories/' . $documentcategory->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/documentcategories')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDocumentcategory((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $documentcategory = \App\Models\Documentcategory::where('id', $id)->first();
    if (is_null($documentcategory))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($documentcategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $documentcategory->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The document category has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($documentcategory, 'update');

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
    $documentcategory = \App\Models\Documentcategory::withTrashed()->where('id', $id)->first();
    if (is_null($documentcategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($documentcategory->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $documentcategory->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The document category has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/documentcategories')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $documentcategory->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The document category has been soft deleted successfully');
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
    $documentcategory = \App\Models\Documentcategory::withTrashed()->where('id', $id)->first();
    if (is_null($documentcategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($documentcategory->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $documentcategory->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The document category has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubDocumentcategories(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Documentcategory();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Documentcategory();
    $myItem2 = $item2::where('documentcategory_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/categories');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDocumentcategories = [];
    foreach ($myItem2 as $documentcategory)
    {
      $name = $documentcategory->name;
      if ($name == '')
      {
        $name = '(' . $documentcategory->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/documentcategories/', $documentcategory->id);

      $comment = $documentcategory->comment;

      $myDocumentcategories[] = [
        'name'         => $name,
        'url'          => $url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    array_multisort(
      array_column($myDocumentcategories, 'name'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myDocumentcategories
    );

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('documentcategories', $myDocumentcategories);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/documentcategories.html.twig', (array)$viewData);
  }
}
