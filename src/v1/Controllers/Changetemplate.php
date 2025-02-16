<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardentity;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Changetemplate extends Common implements \App\Interfaces\Crud
{
  protected $model = \App\Models\Changetemplate::class;

  protected function instanciateModel(): \App\Models\Changetemplate
  {
    return new \App\Models\Changetemplate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Changetemplate::class);

    $changetemplate = new \App\Models\Changetemplate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($changetemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $changetemplate = \App\Models\Changetemplate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The change template has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($changetemplate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/changetemplates/' . $changetemplate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/changetemplates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Changetemplate::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $changetemplate = \App\Models\Changetemplate::where('id', $id)->first();
    if (is_null($changetemplate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($changetemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $changetemplate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The change template has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($changetemplate, 'update');

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
    $changetemplate = \App\Models\Changetemplate::withTrashed()->where('id', $id)->first();
    if (is_null($changetemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($changetemplate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $changetemplate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The change template has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/changetemplates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $changetemplate->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The change template has been soft deleted successfully');
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
    $changetemplate = \App\Models\Changetemplate::withTrashed()->where('id', $id)->first();
    if (is_null($changetemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($changetemplate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $changetemplate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The change template has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
