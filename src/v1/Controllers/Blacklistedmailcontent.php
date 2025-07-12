<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostBlacklistedmailcontent;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Blacklistedmailcontent extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  protected $model = \App\Models\Blacklistedmailcontent::class;

  protected function instanciateModel(): \App\Models\Blacklistedmailcontent
  {
    return new \App\Models\Blacklistedmailcontent();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostBlacklistedmailcontent((object) $request->getParsedBody());

    $blacklistedmailcontent = new \App\Models\Blacklistedmailcontent();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($blacklistedmailcontent))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $blacklistedmailcontent = \App\Models\Blacklistedmailcontent::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($blacklistedmailcontent, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/blacklistedmailcontents/' . $blacklistedmailcontent->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/blacklistedmailcontents')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostBlacklistedmailcontent((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $blacklistedmailcontent = \App\Models\Blacklistedmailcontent::where('id', $id)->first();
    if (is_null($blacklistedmailcontent))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($blacklistedmailcontent))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $blacklistedmailcontent->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($blacklistedmailcontent, 'update');

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
    $blacklistedmailcontent = \App\Models\Blacklistedmailcontent::withTrashed()->where('id', $id)->first();
    if (is_null($blacklistedmailcontent))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($blacklistedmailcontent->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $blacklistedmailcontent->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/blacklistedmailcontents')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $blacklistedmailcontent->delete();
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
    $blacklistedmailcontent = \App\Models\Blacklistedmailcontent::withTrashed()->where('id', $id)->first();
    if (is_null($blacklistedmailcontent))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($blacklistedmailcontent->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $blacklistedmailcontent->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
