<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Virtualmachinestate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Virtualmachinestate::class;

  protected function instanciateModel(): \App\Models\Virtualmachinestate
  {
    return new \App\Models\Virtualmachinestate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Virtualmachinestate::class);

    $virtualmachinestate = new \App\Models\Virtualmachinestate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($virtualmachinestate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $virtualmachinestate = \App\Models\Virtualmachinestate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The virtual machine state has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($virtualmachinestate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/virtualmachinestates/' . $virtualmachinestate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/virtualmachinestates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Virtualmachinestate::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $virtualmachinestate = \App\Models\Virtualmachinestate::where('id', $id)->first();
    if (is_null($virtualmachinestate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($virtualmachinestate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $virtualmachinestate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The virtual machine state has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($virtualmachinestate, 'update');

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
    $virtualmachinestate = \App\Models\Virtualmachinestate::withTrashed()->where('id', $id)->first();
    if (is_null($virtualmachinestate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($virtualmachinestate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $virtualmachinestate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The virtual machine state has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/virtualmachinestates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $virtualmachinestate->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The virtual machine state has been soft deleted successfully');
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
    $virtualmachinestate = \App\Models\Virtualmachinestate::withTrashed()->where('id', $id)->first();
    if (is_null($virtualmachinestate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($virtualmachinestate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $virtualmachinestate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The virtual machine state has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
