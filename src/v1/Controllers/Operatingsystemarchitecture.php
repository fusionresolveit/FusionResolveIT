<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Operatingsystemarchitecture extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Operatingsystemarchitecture::class;

  protected function instanciateModel(): \App\Models\Operatingsystemarchitecture
  {
    return new \App\Models\Operatingsystemarchitecture();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Operatingsystemarchitecture::class);

    $operatingsystemarchitecture = new \App\Models\Operatingsystemarchitecture();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemarchitecture))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemarchitecture = \App\Models\Operatingsystemarchitecture::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem architecture has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemarchitecture, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemarchitectures/' . $operatingsystemarchitecture->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/operatingsystemarchitectures')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Operatingsystemarchitecture::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemarchitecture = \App\Models\Operatingsystemarchitecture::where('id', $id)->first();
    if (is_null($operatingsystemarchitecture))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemarchitecture))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemarchitecture->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem architecture has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemarchitecture, 'update');

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
    $operatingsystemarchitecture = \App\Models\Operatingsystemarchitecture::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemarchitecture))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemarchitecture->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemarchitecture->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem architecture has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemarchitectures')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemarchitecture->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage(
        'The operatingsystem architecture has been soft deleted successfully'
      );
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
    $operatingsystemarchitecture = \App\Models\Operatingsystemarchitecture::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemarchitecture))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemarchitecture->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemarchitecture->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem architecture has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
