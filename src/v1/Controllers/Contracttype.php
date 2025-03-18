<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Contracttype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Contracttype::class;

  protected function instanciateModel(): \App\Models\Contracttype
  {
    return new \App\Models\Contracttype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Contracttype::class);

    $contracttype = new \App\Models\Contracttype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($contracttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contracttype = \App\Models\Contracttype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The contract type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($contracttype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/contracttypes/' . $contracttype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/contracttypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Contracttype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contracttype = \App\Models\Contracttype::where('id', $id)->first();
    if (is_null($contracttype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($contracttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contracttype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The contract type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($contracttype, 'update');

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
    $contracttype = \App\Models\Contracttype::withTrashed()->where('id', $id)->first();
    if (is_null($contracttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($contracttype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contracttype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The contract type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/contracttypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contracttype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The contract type has been soft deleted successfully');
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
    $contracttype = \App\Models\Contracttype::withTrashed()->where('id', $id)->first();
    if (is_null($contracttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($contracttype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contracttype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The contract type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
