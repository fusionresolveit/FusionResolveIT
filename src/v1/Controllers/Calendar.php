<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardentity;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Calendar extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Calendar::class;
  protected $rootUrl2 = '/dropdowns/calendars/';

  protected function instanciateModel(): \App\Models\Calendar
  {
    return new \App\Models\Calendar();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Calendar::class);

    $calendar = new \App\Models\Calendar();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($calendar))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $calendar = \App\Models\Calendar::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($calendar, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/calendars/' . $calendar->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/calendars')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Calendar::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $calendar = \App\Models\Calendar::where('id', $id)->first();
    if (is_null($calendar))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($calendar))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $calendar->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($calendar, 'update');

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
    $calendar = \App\Models\Calendar::withTrashed()->where('id', $id)->first();
    if (is_null($calendar))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($calendar->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $calendar->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/calendars')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $calendar->delete();
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
    $calendar = \App\Models\Calendar::withTrashed()->where('id', $id)->first();
    if (is_null($calendar))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($calendar->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $calendar->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubTimeranges(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Calendar();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/timeranges');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myTimeranges = [];
    foreach ($myItem->timeranges as $timeranges)
    {
      $day = $timeranges->day;
      $name = $this->getDaysOfWeekArray()[$day];
      $begin = $timeranges->begin;
      $end = $timeranges->end;

      $myTimeranges[] = [
        'day'      => $day,
        'name'     => $name,
        'begin'    => $begin,
        'end'      => $end,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myTimeranges, 'day'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myTimeranges);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('timeranges', $myTimeranges);

    $viewData->addTranslation('name', npgettext('calendar', 'Day', 'Days', 1));
    $viewData->addTranslation('begin', pgettext('calendar', 'Start'));
    $viewData->addTranslation('end', pgettext('calendar', 'End'));

    return $view->render($response, 'subitem/timeranges.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubHolidays(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Calendar();
    $view = Twig::fromRequest($request);

    $calendar = \App\Models\Calendar::where('id', $args['id'])->first();
    if (is_null($calendar))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/holidays');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myHolidays = [];
    foreach ($calendar->holidays as $holiday)
    {
      $name = $holiday->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/holidays/', $holiday->id);

      $begin = $holiday->begin_date;

      $end = $holiday->end_date;

      $recurrent = $holiday->is_perpetual;
      if ($recurrent)
      {
        $recurrent_val = pgettext('global', 'Yes');
      } else {
        $recurrent_val = pgettext('global', 'No');
      }

      $myHolidays[] = [
        'name'            => $name,
        'url'             => $url,
        'begin'           => $begin,
        'end'             => $end,
        'recurrent'       => $recurrent,
        'recurrent_val'   => $recurrent_val,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myHolidays, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myHolidays);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($calendar, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($calendar));
    $viewData->addData('holidays', $myHolidays);

    $viewData->addTranslation('name', npgettext('calendar', 'Day', 'Days', 1));
    $viewData->addTranslation('begin', pgettext('calendar', 'Start'));
    $viewData->addTranslation('end', pgettext('calendar', 'End'));
    $viewData->addTranslation('recurrent', pgettext('calendar', 'Recurrent'));

    return $view->render($response, 'subitem/holidays.html.twig', (array)$viewData);
  }
}
