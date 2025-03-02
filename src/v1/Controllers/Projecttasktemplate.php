<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostProjecttasktemplate;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Projecttasktemplate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Document;
  use History;

  protected $model = \App\Models\Projecttasktemplate::class;
  protected $rootUrl2 = '/projecttasktemplates/';

  protected function instanciateModel(): \App\Models\Projecttasktemplate
  {
    return new \App\Models\Projecttasktemplate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostProjecttasktemplate((object) $request->getParsedBody());

    $projecttasktemplate = new \App\Models\Projecttasktemplate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($projecttasktemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttasktemplate = \App\Models\Projecttasktemplate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The project task template has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($projecttasktemplate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/projecttasktemplates/' . $projecttasktemplate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/projecttasktemplates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostProjecttasktemplate((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttasktemplate = \App\Models\Projecttasktemplate::where('id', $id)->first();
    if (is_null($projecttasktemplate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($projecttasktemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttasktemplate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The project task template has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($projecttasktemplate, 'update');

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
    $projecttasktemplate = \App\Models\Projecttasktemplate::withTrashed()->where('id', $id)->first();
    if (is_null($projecttasktemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($projecttasktemplate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttasktemplate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project task template has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/projecttasktemplates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttasktemplate->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project task template has been soft deleted successfully');
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
    $projecttasktemplate = \App\Models\Projecttasktemplate::withTrashed()->where('id', $id)->first();
    if (is_null($projecttasktemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($projecttasktemplate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttasktemplate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project task template has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
