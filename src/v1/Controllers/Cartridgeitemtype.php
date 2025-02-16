<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Cartridgeitemtype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Cartridgeitemtype::class;

  protected function instanciateModel(): \App\Models\Cartridgeitemtype
  {
    return new \App\Models\Cartridgeitemtype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Cartridgeitemtype::class);

    $cartridgeitemtype = new \App\Models\Cartridgeitemtype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($cartridgeitemtype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cartridgeitemtype = \App\Models\Cartridgeitemtype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($cartridgeitemtype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/cartridgeitemtypes/' . $cartridgeitemtype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/cartridgeitemtypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Cartridgeitemtype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cartridgeitemtype = \App\Models\Cartridgeitemtype::where('id', $id)->first();
    if (is_null($cartridgeitemtype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($cartridgeitemtype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cartridgeitemtype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($cartridgeitemtype, 'update');

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
    $cartridgeitemtype = \App\Models\Cartridgeitemtype::withTrashed()->where('id', $id)->first();
    if (is_null($cartridgeitemtype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($cartridgeitemtype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cartridgeitemtype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/cartridgeitemtypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cartridgeitemtype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item type has been soft deleted successfully');
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
    $cartridgeitemtype = \App\Models\Cartridgeitemtype::withTrashed()->where('id', $id)->first();
    if (is_null($cartridgeitemtype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($cartridgeitemtype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cartridgeitemtype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
