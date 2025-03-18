<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostFieldblacklist;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Fieldblacklist extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Fieldblacklist::class;

  protected function instanciateModel(): \App\Models\Fieldblacklist
  {
    return new \App\Models\Fieldblacklist();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostFieldblacklist((object) $request->getParsedBody());

    $fieldblacklist = new \App\Models\Fieldblacklist();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($fieldblacklist))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fieldblacklist = \App\Models\Fieldblacklist::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The field blacklist has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($fieldblacklist, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/fieldblacklists/' . $fieldblacklist->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/fieldblacklists')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostFieldblacklist((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fieldblacklist = \App\Models\Fieldblacklist::where('id', $id)->first();
    if (is_null($fieldblacklist))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($fieldblacklist))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fieldblacklist->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The field blacklist has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($fieldblacklist, 'update');

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
    $fieldblacklist = \App\Models\Fieldblacklist::withTrashed()->where('id', $id)->first();
    if (is_null($fieldblacklist))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($fieldblacklist->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fieldblacklist->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The field blacklist has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/fieldblacklists')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fieldblacklist->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The field blacklist has been soft deleted successfully');
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
    $fieldblacklist = \App\Models\Fieldblacklist::withTrashed()->where('id', $id)->first();
    if (is_null($fieldblacklist))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($fieldblacklist->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fieldblacklist->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The field blacklist has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
