<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostHoliday;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Holiday extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Holiday::class;

  protected function instanciateModel(): \App\Models\Holiday
  {
    return new \App\Models\Holiday();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostHoliday((object) $request->getParsedBody());

    $holiday = new \App\Models\Holiday();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($holiday))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $holiday = \App\Models\Holiday::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The holiday has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($holiday, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/holidays/' . $holiday->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/holidays')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostHoliday((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $holiday = \App\Models\Holiday::where('id', $id)->first();
    if (is_null($holiday))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($holiday))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $holiday->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The holiday has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($holiday, 'update');

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
    $holiday = \App\Models\Holiday::withTrashed()->where('id', $id)->first();
    if (is_null($holiday))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($holiday->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $holiday->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The holiday has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/holidays')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $holiday->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The holiday has been soft deleted successfully');
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
    $holiday = \App\Models\Holiday::withTrashed()->where('id', $id)->first();
    if (is_null($holiday))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($holiday->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $holiday->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The holiday has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
