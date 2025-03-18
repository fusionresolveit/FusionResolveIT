<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostChange;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Approval;
use App\Traits\Subs\Cost;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use App\Traits\Subs\Knowbaseitem;
use App\Traits\Subs\Note;
use App\Traits\Subs\Project;
use App\Traits\Subs\Ticket;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Change extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Note;
  use Knowbaseitem;
  use History;
  use Cost;
  use Ticket;
  use Approval;
  use Project;
  use Item;

  protected $model = \App\Models\Change::class;
  protected $rootUrl2 = '/changes/';
  protected $choose = 'changes';

  protected function instanciateModel(): \App\Models\Change
  {
    return new \App\Models\Change();
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

    $data = new PostChange((object) $request->getParsedBody());

    $change = new \App\Models\Change();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($change))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $change = \App\Models\Change::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The change has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($change, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/changes/' . $change->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/changes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostChange((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $change = \App\Models\Change::where('id', $id)->first();
    if (is_null($change))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($change))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $change->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The change has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($change, 'update');

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
    $change = \App\Models\Change::withTrashed()->where('id', $id)->first();
    if (is_null($change))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($change->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $change->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The change has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/changes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $change->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The change has been soft deleted successfully');
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
    $change = \App\Models\Change::withTrashed()->where('id', $id)->first();
    if (is_null($change))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($change->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $change->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The change has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showProblem(Request $request, Response $response, array $args): Response
  {
    global $translator;
    $item = new \App\Models\Change();
    $view = Twig::fromRequest($request);

    $change = \App\Models\Change::where('id', $args['id'])->first();
    if (is_null($change))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/problem');

    $problems = [];
    foreach ($change->problems as $problem)
    {
      $problem = \App\Models\Problem::where('id', $problem->id)->first();
      if ($problem !== null)
      {
        $problems[] = [
          'id'          => $problem->id,
          'name'        => $problem->name,
          'updated_at'  => $problem->updated_at,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($change, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($change));
    $viewData->addData('feeds', $item->getFeeds(intval($args['id'])));
    $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($change->content));
    $viewData->addData('problems', $problems);

    $viewData->addTranslation('attachItem', $translator->translate('Attach to an existant problem'));
    $viewData->addTranslation('selectItem', $translator->translate('Select problem...'));
    $viewData->addTranslation('buttonAttach', $translator->translate('Attach'));
    $viewData->addTranslation('addItem', $translator->translate('Add new problem'));
    $viewData->addTranslation('buttonCreate', $translator->translate('Create'));
    $viewData->addTranslation('attachedItems', $translator->translate('Problems attached'));
    $viewData->addTranslation('updated', $translator->translate('Last update'));
    $viewData->addTranslation('or', $translator->translate('Ou'));

    return $view->render($response, 'subitem/problem.html.twig', (array) $viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function postProblem(Request $request, Response $response, array $args): Response
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'problem') && is_numeric($data->problem))
    {
      $change = \App\Models\Change::where('id', $args['id'])->first();
      if (is_null($change))
      {
        throw new \Exception('Id not found', 404);
      }

      $change->problems()->attach((int)$data->problem);

      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage("The ticket has been attached to problem successfully");
    }
    else
    {
      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage('Error to attache ticket to problem', 'error');
    }

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri);
  }

  /**
   * @param array<string, string> $args
   */
  public function showAnalysis(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Change();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/analysis');

    $myAnalysis = [];
    $myAnalysis = [
      'impactcontent'       => $myItem->impactcontent,
      'controlistcontent'   => $myItem->controlistcontent,
    ];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionAnalysis');
    $myItemData = [
      'impactcontent'       => $myAnalysis['impactcontent'],
      'controlistcontent'   => $myAnalysis['controlistcontent'],
    ];

    $jsonStr = json_encode($myItemData);
    if ($jsonStr === false)
    {
      $jsonStr = '{}';
    }
    $myItemDataObject = json_decode($jsonStr);

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));

    $viewData->addTranslation('impactcontent', $translator->translate('Impacts'));
    $viewData->addTranslation('controlistcontent', $translator->translate('Control list'));

    return $view->render($response, 'subitem/analysis.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showPlans(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Change();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/plans');

    $myPlans = [];
    $myPlans = [
      'rolloutplancontent'  => $myItem->rolloutplancontent,
      'backoutplancontent'  => $myItem->backoutplancontent,
      'checklistcontent'    => $myItem->checklistcontent,
    ];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionPlans');
    $myItemData = [
      'rolloutplancontent'  => $myPlans['rolloutplancontent'],
      'backoutplancontent'  => $myPlans['backoutplancontent'],
      'checklistcontent'    => $myPlans['checklistcontent'],
    ];
    $jsonStr = json_encode($myItemData);
    if ($jsonStr === false)
    {
      $jsonStr = '{}';
    }
    $myItemDataObject = json_decode($jsonStr);

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));
    $viewData->addData('show', $this->choose);
    $viewData->addData('plans', $myPlans);

    $viewData->addTranslation('rolloutplancontent', $translator->translate('Deployment plan'));
    $viewData->addTranslation('backoutplancontent', $translator->translate('Backup plan'));
    $viewData->addTranslation('checklistcontent', $translator->translate('Checklist'));

    return $view->render($response, 'subitem/plans.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showStats(Request $request, Response $response, array $args): Response
  {
    global $translator;

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
      'text'  => $translator->translate('Opening date'),
      'icon'  => 'pencil alternate',
      'color' => 'blue'
    ];

    $feeds[] = [
      'date'  => $myItem->time_to_resolve,
      'text'  => $translator->translate('Time to resolve'),
      'icon'  => 'hourglass half',
      'color' => 'blue'
    ];
    if ($myItem->status >= 5)
    {
      $feeds[] = [
        'date'  => $myItem->solvedate,
        'text'  => $translator->translate('Resolution date'),
        'icon'  => 'check circle',
        'color' => 'blue'
      ];
    }
    if ($myItem->status == 6)
    {
      $feeds[] = [
        'date'  => $myItem->closedate,
        'text'  => $translator->translate('Closing date'),
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
