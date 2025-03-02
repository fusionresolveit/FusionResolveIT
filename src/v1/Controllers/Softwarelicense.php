<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSoftwarelicense;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Certificate;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use App\Traits\Subs\Knowbaseitem;
use App\Traits\Subs\Note;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Softwarelicense extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Note;
  use Certificate;
  use Knowbaseitem;
  use Document;
  use Contract;
  use Itil;
  use History;
  use Infocom;

  protected $model = \App\Models\Softwarelicense::class;
  protected $rootUrl2 = '/softwarelicenses/';

  protected function instanciateModel(): \App\Models\Softwarelicense
  {
    return new \App\Models\Softwarelicense();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSoftwarelicense((object) $request->getParsedBody());

    $softwarelicense = new \App\Models\Softwarelicense();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($softwarelicense))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarelicense = \App\Models\Softwarelicense::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The software license has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($softwarelicense, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/softwarelicenses/' . $softwarelicense->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/vs')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSoftwarelicense((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarelicense = \App\Models\Softwarelicense::where('id', $id)->first();
    if (is_null($softwarelicense))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($softwarelicense))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $softwarelicense->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The software license has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($softwarelicense, 'update');

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
    $softwarelicense = \App\Models\Softwarelicense::withTrashed()->where('id', $id)->first();
    if (is_null($softwarelicense))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($softwarelicense->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarelicense->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software license has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/vs')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarelicense->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software license has been soft deleted successfully');
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
    $softwarelicense = \App\Models\Softwarelicense::withTrashed()->where('id', $id)->first();
    if (is_null($softwarelicense))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($softwarelicense->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $softwarelicense->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software license has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubSoftwarelicenses(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Softwarelicense();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('childs')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/licenses');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySoftwarelicenses = [];
    foreach ($myItem->childs as $child)
    {
      $name = $child->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/softwarelicenses/', $child->id);

      $entity = '';
      $entity_url = '';
      if ($child->entity !== null)
      {
        $entity = $child->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $child->entity->id);
      }

      $comment = $child->comment;

      $mySoftwarelicenses[] = [
        'name'        => $name,
        'url'         => $url,
        'entity'      => $entity,
        'entity_url'  => $entity_url,
        'comment'     => $comment,
      ];
    }

    // tri ordre alpha
    array_multisort(
      array_column($mySoftwarelicenses, 'name'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $mySoftwarelicenses
    );

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwarelicenses', $mySoftwarelicenses);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/softwarelicenses.html.twig', (array)$viewData);
  }
}
