<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPdumodel;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Pdumodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Pdumodel::class;

  protected function instanciateModel(): \App\Models\Pdumodel
  {
    return new \App\Models\Pdumodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPdumodel((object) $request->getParsedBody());

    $pdumodel = new \App\Models\Pdumodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($pdumodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdumodel = \App\Models\Pdumodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pdu model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($pdumodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/pdumodels/' . $pdumodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/pdumodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPdumodel((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdumodel = \App\Models\Pdumodel::where('id', $id)->first();
    if (is_null($pdumodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($pdumodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdumodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pdu model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($pdumodel, 'update');

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
    $pdumodel = \App\Models\Pdumodel::withTrashed()->where('id', $id)->first();
    if (is_null($pdumodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($pdumodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdumodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/pdumodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdumodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu model has been soft deleted successfully');
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
    $pdumodel = \App\Models\Pdumodel::withTrashed()->where('id', $id)->first();
    if (is_null($pdumodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($pdumodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdumodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
