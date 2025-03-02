<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostComputerantivirus;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Computerantivirus extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  protected $model = \App\Models\Computerantivirus::class;

  protected function instanciateModel(): \App\Models\Computerantivirus
  {
    return new \App\Models\Computerantivirus();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostComputerantivirus((object) $request->getParsedBody());

    $computerantivirus = new \App\Models\Computerantivirus();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($computerantivirus))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computerantivirus = \App\Models\Computerantivirus::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The computer antivirus has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($computerantivirus, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/computerantiviruses/' . $computerantivirus->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/computerantiviruses')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostComputerantivirus((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computerantivirus = \App\Models\Computerantivirus::where('id', $id)->first();
    if (is_null($computerantivirus))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($computerantivirus))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computerantivirus->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The computer antivirus has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($computerantivirus, 'update');

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
    $computerantivirus = \App\Models\Computerantivirus::withTrashed()->where('id', $id)->first();
    if (is_null($computerantivirus))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($computerantivirus->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computerantivirus->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer antivirus has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/computerantiviruses')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computerantivirus->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer antivirus has been soft deleted successfully');
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
    $computerantivirus = \App\Models\Computerantivirus::withTrashed()->where('id', $id)->first();
    if (is_null($computerantivirus))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($computerantivirus->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computerantivirus->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer antivirus has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
