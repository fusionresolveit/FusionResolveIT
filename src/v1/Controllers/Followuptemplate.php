<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostFollowuptemplate;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Followuptemplate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Followuptemplate::class;

  protected function instanciateModel(): \App\Models\Followuptemplate
  {
    return new \App\Models\Followuptemplate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostFollowuptemplate((object) $request->getParsedBody());

    $followuptemplate = new \App\Models\Followuptemplate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($followuptemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $followuptemplate = \App\Models\Followuptemplate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The followup template has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($followuptemplate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/followuptemplates/' . $followuptemplate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/followuptemplates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostFollowuptemplate((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $followuptemplate = \App\Models\Followuptemplate::where('id', $id)->first();
    if (is_null($followuptemplate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($followuptemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $followuptemplate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The followup template has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($followuptemplate, 'update');

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
    $followuptemplate = \App\Models\Followuptemplate::withTrashed()->where('id', $id)->first();
    if (is_null($followuptemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($followuptemplate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $followuptemplate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The followup template has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/followuptemplates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $followuptemplate->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The followup template has been soft deleted successfully');
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
    $followuptemplate = \App\Models\Followuptemplate::withTrashed()->where('id', $id)->first();
    if (is_null($followuptemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($followuptemplate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $followuptemplate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The followup template has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
