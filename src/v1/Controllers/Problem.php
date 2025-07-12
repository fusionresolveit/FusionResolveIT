<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostProblem;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Change;
use App\Traits\Subs\Cost;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use App\Traits\Subs\Knowledgebasearticle;
use App\Traits\Subs\Note;
use App\Traits\Subs\Project;
use App\Traits\Subs\Ticket;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Problem extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Note;
  use Knowledgebasearticle;
  use History;
  use Cost;
  use Ticket;
  use Change;
  use Project;
  use Item;

  protected $model = \App\Models\Problem::class;
  protected $rootUrl2 = '/problems/';
  protected $choose = 'problems';

  protected function instanciateModel(): \App\Models\Problem
  {
    return new \App\Models\Problem();
  }

    /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *          'itemMonitors': \App\Models\Monitor,
   *          'itemNetworkequipments': \App\Models\Networkequipment,
   *          'itemPeripherals': \App\Models\Peripheral,
   *          'itemPhones': \App\Models\Phone,
   *          'itemPrinters': \App\Models\Printer,
   *          'itemSoftwares': \App\Models\Software,
   *          'itemSoftwarelicenses': \App\Models\Softwarelicense,
   *          'itemCertificates': \App\Models\Certificate,
   *          'itemLines': \App\Models\Line,
   *          'itemDcrooms': \App\Models\Dcroom,
   *          'itemRacks': \App\Models\Rack,
   *          'itemEnclosures': \App\Models\Enclosure,
   *          'itemClusters': \App\Models\Cluster,
   *          'itemPdus': \App\Models\Pdu,
   *          'itemDomains': \App\Models\Domain,
   *          'itemDomainrecords': \App\Models\Domainrecord,
   *          'itemAppliances': \App\Models\Appliance,
   *          'itemPassivedcequipments': \App\Models\Passivedcequipment
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'           => new \App\Models\Computer(),
      'itemMonitors'            => new \App\Models\Monitor(),
      'itemNetworkequipments'   => new \App\Models\Networkequipment(),
      'itemPeripherals'         => new \App\Models\Peripheral(),
      'itemPhones'              => new \App\Models\Phone(),
      'itemPrinters'            => new \App\Models\Printer(),
      'itemSoftwares'           => new \App\Models\Software(),
      'itemSoftwarelicenses'    => new \App\Models\Softwarelicense(),
      'itemCertificates'        => new \App\Models\Certificate(),
      'itemLines'               => new \App\Models\Line(),
      'itemDcrooms'             => new \App\Models\Dcroom(),
      'itemRacks'               => new \App\Models\Rack(),
      'itemEnclosures'          => new \App\Models\Enclosure(),
      'itemClusters'            => new \App\Models\Cluster(),
      'itemPdus'                => new \App\Models\Pdu(),
      'itemDomains'             => new \App\Models\Domain(),
      'itemDomainrecords'       => new \App\Models\Domainrecord(),
      'itemAppliances'          => new \App\Models\Appliance(),
      'itemPassivedcequipments' => new \App\Models\Passivedcequipment(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostProblem((object) $request->getParsedBody());

    $problem = new \App\Models\Problem();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($problem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $problem = \App\Models\Problem::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($problem, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/problems/' . $problem->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/problems')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostProblem((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $problem = \App\Models\Problem::where('id', $id)->first();
    if (is_null($problem))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($problem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $problem->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($problem, 'update');

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
    $problem = \App\Models\Problem::withTrashed()->where('id', $id)->first();
    if (is_null($problem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($problem->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $problem->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/problems')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $problem->delete();
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
    $problem = \App\Models\Problem::withTrashed()->where('id', $id)->first();
    if (is_null($problem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($problem->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $problem->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showAnalysis(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Problem();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/analysis');

    $myAnalysis = [];
    $myAnalysis = [
      'impactcontent'   => $myItem->impactcontent,
      'causecontent'    => $myItem->causecontent,
      'symptomcontent'  => $myItem->symptomcontent,
    ];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionAnalysis');
    $myItemData = [
      'impactcontent'   => $myAnalysis['impactcontent'],
      'causecontent'    => $myAnalysis['causecontent'],
      'symptomcontent'  => $myAnalysis['symptomcontent'],
    ];
    $jsonStr = json_encode($myItemData);
    if ($jsonStr === false)
    {
      $jsonStr = '{}';
    }
    $myItemDataObject = json_decode($jsonStr);

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    $viewData->addTranslation('impactcontent', pgettext('ITIL', 'Impacts'));
    $viewData->addTranslation('causecontent', pgettext('problem', 'Causes'));
    $viewData->addTranslation('symptomcontent', pgettext('problem', 'Symptoms'));

    return $view->render($response, 'subitem/analysis.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showStats(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/stats');

    $feeds = [];

    $feeds[] = [
      'date'  => $myItem->created_at,
      'text'  => pgettext('ITIL', 'Opening date'),
      'icon'  => 'pencil alternate',
      'color' => 'blue'
    ];

    $feeds[] = [
      'date'  => $myItem->time_to_resolve,
      'text'  => pgettext('ITIL', 'Time to resolve'),
      'icon'  => 'hourglass half',
      'color' => 'blue'
    ];
    if ($myItem->status >= 5)
    {
      $feeds[] = [
        'date'  => $myItem->solvedate,
        'text'  => pgettext('ITIL', 'Resolution date'),
        'icon'  => 'check circle',
        'color' => 'blue'
      ];
    }
    if ($myItem->status == 6)
    {
      $feeds[] = [
        'date'  => $myItem->closedate,
        'text'  => pgettext('ITIL', 'Closing date'),
        'icon'  => 'flag checkered',
        'color' => 'blue'
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('feeds', $feeds);

    return $view->render($response, 'subitem/stats.html.twig', (array) $viewData);
  }
}
