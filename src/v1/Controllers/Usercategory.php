<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Usercategory extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Usercategory::class;

  protected function instanciateModel(): \App\Models\Usercategory
  {
    return new \App\Models\Usercategory();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Usercategory::class);

    $usercategory = new \App\Models\Usercategory();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($usercategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $usercategory = \App\Models\Usercategory::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The user category has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($usercategory, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/usercategorys/' . $usercategory->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/usercategorys')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Usercategory::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $usercategory = \App\Models\Usercategory::where('id', $id)->first();
    if (is_null($usercategory))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($usercategory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $usercategory->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The user category has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($usercategory, 'update');

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
    $usercategory = \App\Models\Usercategory::withTrashed()->where('id', $id)->first();
    if (is_null($usercategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($usercategory->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $usercategory->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user category has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/usercategorys')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $usercategory->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user category has been soft deleted successfully');
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
    $usercategory = \App\Models\Usercategory::withTrashed()->where('id', $id)->first();
    if (is_null($usercategory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($usercategory->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $usercategory->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user category has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
