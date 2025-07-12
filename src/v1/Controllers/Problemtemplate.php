<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardentity;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Problemtemplate extends Common implements \App\Interfaces\Crud
{
  protected $model = \App\Models\Problemtemplate::class;

    /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Problemtemplate::class);

    $problemtemplate = new \App\Models\Problemtemplate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($problemtemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $problemtemplate = \App\Models\Problemtemplate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($problemtemplate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/problemtemplates/' . $problemtemplate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/problemtemplates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Problemtemplate::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $problemtemplate = \App\Models\Problemtemplate::where('id', $id)->first();
    if (is_null($problemtemplate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($problemtemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $problemtemplate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($problemtemplate, 'update');

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
    $problemtemplate = \App\Models\Problemtemplate::withTrashed()->where('id', $id)->first();
    if (is_null($problemtemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($problemtemplate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $problemtemplate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/problemtemplates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $problemtemplate->delete();
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
    $problemtemplate = \App\Models\Problemtemplate::withTrashed()->where('id', $id)->first();
    if (is_null($problemtemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($problemtemplate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $problemtemplate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
