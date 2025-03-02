<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicesimcardtype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Devicesimcardtype::class;

  protected function instanciateModel(): \App\Models\Devicesimcardtype
  {
    return new \App\Models\Devicesimcardtype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicesimcardtype::class);

    $devicesimcardtype = new \App\Models\Devicesimcardtype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesimcardtype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesimcardtype = \App\Models\Devicesimcardtype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sim card type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesimcardtype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicesimcardtypes/' . $devicesimcardtype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicesimcardtypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicesimcardtype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesimcardtype = \App\Models\Devicesimcardtype::where('id', $id)->first();
    if (is_null($devicesimcardtype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesimcardtype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesimcardtype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sim card type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesimcardtype, 'update');

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
    $devicesimcardtype = \App\Models\Devicesimcardtype::withTrashed()->where('id', $id)->first();
    if (is_null($devicesimcardtype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesimcardtype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesimcardtype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sim card type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicesimcardtypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesimcardtype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sim card type has been soft deleted successfully');
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
    $devicesimcardtype = \App\Models\Devicesimcardtype::withTrashed()->where('id', $id)->first();
    if (is_null($devicesimcardtype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesimcardtype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesimcardtype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sim card type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
