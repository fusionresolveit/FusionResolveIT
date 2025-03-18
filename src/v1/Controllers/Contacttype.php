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

final class Contacttype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Contacttype::class;

  protected function instanciateModel(): \App\Models\Contacttype
  {
    return new \App\Models\Contacttype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Contacttype::class);

    $contacttype = new \App\Models\Contacttype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($contacttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contacttype = \App\Models\Contacttype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The contact type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($contacttype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/contacttypes/' . $contacttype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/contacttypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Contacttype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contacttype = \App\Models\Contacttype::where('id', $id)->first();
    if (is_null($contacttype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($contacttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contacttype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The contact type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($contacttype, 'update');

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
    $contacttype = \App\Models\Contacttype::withTrashed()->where('id', $id)->first();
    if (is_null($contacttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($contacttype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contacttype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The contact type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/contacttypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contacttype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The contact type has been soft deleted successfully');
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
    $contacttype = \App\Models\Contacttype::withTrashed()->where('id', $id)->first();
    if (is_null($contacttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($contacttype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contacttype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The contact type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
