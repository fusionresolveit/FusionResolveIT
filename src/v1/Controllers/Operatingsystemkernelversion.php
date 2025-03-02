<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostOperatingsystemkernelversion;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Operatingsystemkernelversion extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Operatingsystemkernelversion::class;

  protected function instanciateModel(): \App\Models\Operatingsystemkernelversion
  {
    return new \App\Models\Operatingsystemkernelversion();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostOperatingsystemkernelversion((object) $request->getParsedBody());

    $operatingsystemkernelversion = new \App\Models\Operatingsystemkernelversion();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemkernelversion))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemkernelversion = \App\Models\Operatingsystemkernelversion::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem kernel version has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemkernelversion, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemkernelversions/' . $operatingsystemkernelversion->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/operatingsystemkernelversions')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostOperatingsystemkernelversion((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemkernelversion = \App\Models\Operatingsystemkernelversion::where('id', $id)->first();
    if (is_null($operatingsystemkernelversion))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemkernelversion))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemkernelversion->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem kernel version has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemkernelversion, 'update');

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
    $operatingsystemkernelversion = \App\Models\Operatingsystemkernelversion::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemkernelversion))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemkernelversion->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemkernelversion->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage(
        'The operatingsystem kernel version has been deleted successfully'
      );

      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemkernelversions')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemkernelversion->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage(
        'The operatingsystem kernel version has been soft deleted successfully'
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
    $operatingsystemkernelversion = \App\Models\Operatingsystemkernelversion::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemkernelversion))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemkernelversion->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemkernelversion->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage(
        'The operatingsystem kernel version has been restored successfully'
      );
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
