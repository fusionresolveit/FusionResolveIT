<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostFieldunicity;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Fieldunicity extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Fieldunicity::class;

  protected function instanciateModel(): \App\Models\Fieldunicity
  {
    return new \App\Models\Fieldunicity();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostFieldunicity((object) $request->getParsedBody());

    $fieldunicity = new \App\Models\Fieldunicity();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($fieldunicity))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fieldunicity = \App\Models\Fieldunicity::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The field unicity has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($fieldunicity, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/fieldunicities/' . $fieldunicity->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/fieldunicities')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostFieldunicity((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fieldunicity = \App\Models\Fieldunicity::where('id', $id)->first();
    if (is_null($fieldunicity))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($fieldunicity))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fieldunicity->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The field unicity has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($fieldunicity, 'update');

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
    $fieldunicity = \App\Models\Fieldunicity::withTrashed()->where('id', $id)->first();
    if (is_null($fieldunicity))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($fieldunicity->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fieldunicity->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The field unicity has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/fieldunicities')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fieldunicity->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The field unicity has been soft deleted successfully');
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
    $fieldunicity = \App\Models\Fieldunicity::withTrashed()->where('id', $id)->first();
    if (is_null($fieldunicity))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($fieldunicity->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $fieldunicity->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The field unicity has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
