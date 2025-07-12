<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSolutiontemplate;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Solutiontemplate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Solutiontemplate::class;

  protected function instanciateModel(): \App\Models\Solutiontemplate
  {
    return new \App\Models\Solutiontemplate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSolutiontemplate((object) $request->getParsedBody());

    $solutiontemplate = new \App\Models\Solutiontemplate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($solutiontemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $solutiontemplate = \App\Models\Solutiontemplate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($solutiontemplate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/solutiontemplates/' . $solutiontemplate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/solutiontemplates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSolutiontemplate((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $solutiontemplate = \App\Models\Solutiontemplate::where('id', $id)->first();
    if (is_null($solutiontemplate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($solutiontemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $solutiontemplate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($solutiontemplate, 'update');

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
    $solutiontemplate = \App\Models\Solutiontemplate::withTrashed()->where('id', $id)->first();
    if (is_null($solutiontemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($solutiontemplate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $solutiontemplate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/solutiontemplates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $solutiontemplate->delete();
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
    $solutiontemplate = \App\Models\Solutiontemplate::withTrashed()->where('id', $id)->first();
    if (is_null($solutiontemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($solutiontemplate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $solutiontemplate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
