<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPdu;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Pdu extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Document;
  use Contract;
  use Itil;
  use History;
  use Infocom;

  protected $model = \App\Models\Pdu::class;
  protected $rootUrl2 = '/pdus/';

  protected function instanciateModel(): \App\Models\Pdu
  {
    return new \App\Models\Pdu();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPdu((object) $request->getParsedBody());

    $pdu = new \App\Models\Pdu();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($pdu))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdu = \App\Models\Pdu::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pdu has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($pdu, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/pdus/' . $pdu->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/pdus')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPdu((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdu = \App\Models\Pdu::where('id', $id)->first();
    if (is_null($pdu))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($pdu))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $pdu->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pdu has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($pdu, 'update');

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
    $pdu = \App\Models\Pdu::withTrashed()->where('id', $id)->first();
    if (is_null($pdu))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($pdu->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdu->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/pdus')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdu->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu has been soft deleted successfully');
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
    $pdu = \App\Models\Pdu::withTrashed()->where('id', $id)->first();
    if (is_null($pdu))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($pdu->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $pdu->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pdu has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubPlugs(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Pdu();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/plugs');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myPlugs = [];
    foreach ($myItem->plugs as $current_plug)
    {
      $name = $current_plug->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/plugs/', $current_plug->id);

      $number_plugs = $current_plug->getRelationValue('pivot')->number_plugs;

      $myPlugs[] = [
        'name'            => $name,
        'url'             => $url,
        'number_plugs'    => $number_plugs,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myPlugs, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myPlugs);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('plugs', $myPlugs);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('number', $translator->translate('Number'));

    return $view->render($response, 'subitem/plugs.html.twig', (array)$viewData);
  }
}
