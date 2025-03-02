<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Filesystem extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Filesystem::class;

  protected function instanciateModel(): \App\Models\Filesystem
  {
    return new \App\Models\Filesystem();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Filesystem::class);

    $filesystem = new \App\Models\Filesystem();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($filesystem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $filesystem = \App\Models\Filesystem::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The filesystem has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($filesystem, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/filesystems/' . $filesystem->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/filesystems')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Filesystem::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $filesystem = \App\Models\Filesystem::where('id', $id)->first();
    if (is_null($filesystem))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($filesystem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $filesystem->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The filesystem has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($filesystem, 'update');

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
    $filesystem = \App\Models\Filesystem::withTrashed()->where('id', $id)->first();
    if (is_null($filesystem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($filesystem->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $filesystem->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The filesystem has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/filesystems')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $filesystem->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The filesystem has been soft deleted successfully');
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
    $filesystem = \App\Models\Filesystem::withTrashed()->where('id', $id)->first();
    if (is_null($filesystem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($filesystem->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $filesystem->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The filesystem has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
