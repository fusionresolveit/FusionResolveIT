<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Operatingsystem extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Operatingsystem::class;

  protected function instanciateModel(): \App\Models\Operatingsystem
  {
    return new \App\Models\Operatingsystem();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Operatingsystem::class);

    $operatingsystem = new \App\Models\Operatingsystem();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystem = \App\Models\Operatingsystem::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystem, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystems/' . $operatingsystem->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/operatingsystems')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Operatingsystem::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystem = \App\Models\Operatingsystem::where('id', $id)->first();
    if (is_null($operatingsystem))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystem->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystem, 'update');

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
    $operatingsystem = \App\Models\Operatingsystem::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystem->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystem->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystems')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystem->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem has been soft deleted successfully');
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
    $operatingsystem = \App\Models\Operatingsystem::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystem->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystem->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
