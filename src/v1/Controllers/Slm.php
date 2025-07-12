<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSlm;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Slm extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Slm::class;
  protected $rootUrl2 = '/slms/';

  protected function instanciateModel(): \App\Models\Slm
  {
    return new \App\Models\Slm();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSlm((object) $request->getParsedBody());

    $slm = new \App\Models\Slm();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($slm))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $slm = \App\Models\Slm::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($slm, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/slms/' . $slm->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/slms')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSlm((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $slm = \App\Models\Slm::where('id', $id)->first();
    if (is_null($slm))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($slm))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $slm->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($slm, 'update');

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
    $slm = \App\Models\Slm::withTrashed()->where('id', $id)->first();
    if (is_null($slm))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($slm->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $slm->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/slms')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $slm->delete();
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
    $slm = \App\Models\Slm::withTrashed()->where('id', $id)->first();
    if (is_null($slm))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($slm->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $slm->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubSlas(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Slm();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/slas');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySlas = [];
    foreach ($myItem->slas as $current_sla)
    {
      $name = $current_sla->name;

      $url = '';
      // $url = $this->genereRootUrl2Link($rootUrl2, '/slas/', $current_sla->id);     // TODO

      $type = $this->getTtrTto()[$current_sla->type];

      $max_duration = null;
      if (!is_null($current_sla->definition_time))
      {
        $max_duration = $this->getValueWithUnit($current_sla->number_time, $current_sla->definition_time, 0);
      }

      $calendar_name = '';
      $calendar_url = '';
      if ($myItem->calendar !== null)
      {
        $calendar_name = $myItem->calendar->name;
        $calendar_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/calendars/', $myItem->calendar->id);
      }

      $mySlas[] = [
        'name'                => $name,
        'url'                 => $url,
        'type'                => $type,
        'max_duration'        => $max_duration,
        'calendar_name'       => $calendar_name,
        'calendar_url'        => $calendar_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('slaola', $mySlas);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('max_duration', pgettext('time', 'Maximum time'));
    $viewData->addTranslation('calendar', npgettext('global', 'Calendar', 'Calendars', 1));

    return $view->render($response, 'subitem/slaola.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubOlas(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Slm();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/olas');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myOlas = [];
    foreach ($myItem->olas as $current_ola)
    {
      $name = $current_ola->name;

      $url = '';
      // $url = $this->genereRootUrl2Link($rootUrl2, '/olas/', $current_ola->id);   // TODO

      $type = $this->getTtrTto()[$current_ola->type];
      $max_duration = null;
      if (!is_null($current_ola->definition_time))
      {
        $max_duration = $this->getValueWithUnit($current_ola->number_time, $current_ola->definition_time, 0);
      }

      $calendar_name = '';
      $calendar_url = '';
      if ($myItem->calendar !== null)
      {
        $calendar_name = $myItem->calendar->name;
        $calendar_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/calendars/', $myItem->calendar->id);
      }

      $myOlas[] = [
        'name'                => $name,
        'url'                 => $url,
        'type'                => $type,
        'max_duration'        => $max_duration,
        'calendar_name'       => $calendar_name,
        'calendar_url'        => $calendar_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('slaola', $myOlas);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('max_duration', pgettext('time', 'Maximum time'));
    $viewData->addTranslation('calendar', npgettext('global', 'Calendar', 'Calendars', 1));

    return $view->render($response, 'subitem/slaola.html.twig', (array)$viewData);
  }

  /**
   * @return array<mixed>
   */
  public function getTtrTto(): array
  {
    return [
      $this->TTR => [
        'title' => pgettext('ITIL', 'Time to resolve'),
      ],
      $this->TTO => [
        'title' => pgettext('ticket', 'Time to own'),
      ],
    ];
  }
}
