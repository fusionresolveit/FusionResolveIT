<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostReminder;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Reminder extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;

  protected $model = \App\Models\Reminder::class;
  protected $rootUrl2 = '/reminders/';

  protected function instanciateModel(): \App\Models\Reminder
  {
    return new \App\Models\Reminder();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostReminder((object) $request->getParsedBody());

    $reminder = new \App\Models\Reminder();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($reminder))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $reminder = \App\Models\Reminder::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The reminder has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($reminder, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/reminders/' . $reminder->id)
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
    $data = new PostReminder((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $reminder = \App\Models\Reminder::where('id', $id)->first();
    if (is_null($reminder))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($reminder))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $reminder->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The reminder has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($reminder, 'update');

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
    $reminder = \App\Models\Reminder::withTrashed()->where('id', $id)->first();
    if (is_null($reminder))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($reminder->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $reminder->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The reminder has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/reminders')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $reminder->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The reminder has been soft deleted successfully');
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
    $reminder = \App\Models\Reminder::withTrashed()->where('id', $id)->first();
    if (is_null($reminder))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($reminder->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $reminder->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The reminder has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
