<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Usertitle extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;

  protected $model = \App\Models\Usertitle::class;

  protected function instanciateModel(): \App\Models\Usertitle
  {
    return new \App\Models\Usertitle();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Usertitle::class);

    $usertitle = new \App\Models\Usertitle();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($usertitle))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $usertitle = \App\Models\Usertitle::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The user title has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($usertitle, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/usertitles/' . $usertitle->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/usertitles')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Usertitle::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $usertitle = \App\Models\Usertitle::where('id', $id)->first();
    if (is_null($usertitle))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($usertitle))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $usertitle->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The user title has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($usertitle, 'update');

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
    $usertitle = \App\Models\Usertitle::withTrashed()->where('id', $id)->first();
    if (is_null($usertitle))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($usertitle->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $usertitle->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user title has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/usertitles')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $usertitle->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user title has been soft deleted successfully');
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
    $usertitle = \App\Models\Usertitle::withTrashed()->where('id', $id)->first();
    if (is_null($usertitle))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($usertitle->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $usertitle->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user title has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
