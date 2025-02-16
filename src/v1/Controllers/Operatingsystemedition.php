<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Operatingsystemedition extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Operatingsystemedition::class;

  protected function instanciateModel(): \App\Models\Operatingsystemedition
  {
    return new \App\Models\Operatingsystemedition();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Operatingsystemedition::class);

    $operatingsystemedition = new \App\Models\Operatingsystemedition();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemedition))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemedition = \App\Models\Operatingsystemedition::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem edition has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemedition, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemeditions/' . $operatingsystemedition->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/operatingsystemeditions')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Operatingsystemedition::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemedition = \App\Models\Operatingsystemedition::where('id', $id)->first();
    if (is_null($operatingsystemedition))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemedition))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemedition->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem edition has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemedition, 'update');

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
    $operatingsystemedition = \App\Models\Operatingsystemedition::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemedition))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemedition->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemedition->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem edition has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemeditions')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemedition->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystemedition has been soft deleted successfully');
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
    $operatingsystemedition = \App\Models\Operatingsystemedition::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemedition))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemedition->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemedition->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem edition has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
