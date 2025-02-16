<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Operatingsystemkernel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Operatingsystemkernel::class;

  protected function instanciateModel(): \App\Models\Operatingsystemkernel
  {
    return new \App\Models\Operatingsystemkernel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Operatingsystemkernel::class);

    $operatingsystemkernel = new \App\Models\Operatingsystemkernel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemkernel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemkernel = \App\Models\Operatingsystemkernel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem kernel has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemkernel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemkernels/' . $operatingsystemkernel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/operatingsystemkernels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Operatingsystemkernel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemkernel = \App\Models\Operatingsystemkernel::where('id', $id)->first();
    if (is_null($operatingsystemkernel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemkernel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemkernel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem kernel has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemkernel, 'update');

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
    $operatingsystemkernel = \App\Models\Operatingsystemkernel::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemkernel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemkernel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemkernel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem kernel has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemkernels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemkernel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem kernel has been soft deleted successfully');
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
    $operatingsystemkernel = \App\Models\Operatingsystemkernel::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemkernel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemkernel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemkernel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The operatingsystem kernel has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
