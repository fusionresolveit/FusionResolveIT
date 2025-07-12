<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostEnclosure;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Component;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Item;
use App\Traits\Subs\Itil;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Enclosure extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use Contract;
  use Itil;
  use History;
  use Component;
  use Infocom;

  protected $model = \App\Models\Enclosure::class;
  protected $rootUrl2 = '/enclosures/';
  protected $choose = 'enclosures';

  protected function instanciateModel(): \App\Models\Enclosure
  {
    return new \App\Models\Enclosure();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostEnclosure((object) $request->getParsedBody());

    $enclosure = new \App\Models\Enclosure();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($enclosure))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $enclosure = \App\Models\Enclosure::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($enclosure, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/enclosures/' . $enclosure->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/enclosures')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostEnclosure((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $enclosure = \App\Models\Enclosure::where('id', $id)->first();
    if (is_null($enclosure))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($enclosure))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $enclosure->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($enclosure, 'update');

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
    $enclosure = \App\Models\Enclosure::withTrashed()->where('id', $id)->first();
    if (is_null($enclosure))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($enclosure->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $enclosure->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/enclosures')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $enclosure->delete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('softdeleted');
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
    $enclosure = \App\Models\Enclosure::withTrashed()->where('id', $id)->first();
    if (is_null($enclosure))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($enclosure->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $enclosure->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
