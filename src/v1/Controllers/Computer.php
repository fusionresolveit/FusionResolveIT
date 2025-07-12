<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostComputer;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Appliance;
use App\Traits\Subs\Certificate;
use App\Traits\Subs\Component;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Domain;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use App\Traits\Subs\Knowledgebasearticle;
use App\Traits\Subs\Note;
use App\Traits\Subs\Operatingsystem;
use App\Traits\Subs\Reservation;
use App\Traits\Subs\Software;
use App\Traits\Subs\Volume;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Computer extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Reservation;
  use Note;
  use Domain;
  use Appliance;
  use Certificate;
  use Externallink;
  use Knowledgebasearticle;
  use Document;
  use Contract;
  use Software;
  use Operatingsystem;
  use Itil;
  use History;
  use Component;
  use Volume;
  use Infocom;

  protected $model = \App\Models\Computer::class;
  protected $rootUrl2 = '/computers/';
  protected $choose = 'computers';

  protected function instanciateModel(): \App\Models\Computer
  {
    return new \App\Models\Computer();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostComputer((object) $request->getParsedBody());

    $computer = new \App\Models\Computer();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($computer))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computer = \App\Models\Computer::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($computer, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/computers/' . $computer->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/computers')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostComputer((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computer = \App\Models\Computer::where('id', $id)->first();
    if (is_null($computer))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($computer))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $computer->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($computer, 'update');

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
    $computer = \App\Models\Computer::withTrashed()->where('id', $id)->first();
    if (is_null($computer))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($computer->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computer->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/computers')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computer->delete();
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
    $computer = \App\Models\Computer::withTrashed()->where('id', $id)->first();
    if (is_null($computer))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($computer->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computer->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubSoftwares(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('softwareversions', 'antiviruses')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/softwares');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAntiviruses = [];
    foreach ($myItem->antiviruses as $antivirus)
    {
      $antivirus_url = $this->genereRootUrl2Link($rootUrl2, '/computerantivirus/', $antivirus->id);

      $manufacturer = '';
      $manufacturer_url = '';
      if ($antivirus->manufacturer !== null)
      {
        $manufacturer = $antivirus->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $antivirus->manufacturer->id
        );
      }

      $is_dynamic = $antivirus->is_dynamic;
      if ($is_dynamic == 1)
      {
        $is_dynamic_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_dynamic_val = pgettext('global', 'No');
      }

      $is_active = $antivirus->is_active;
      if ($is_active == 1)
      {
        $is_active_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_active_val = pgettext('global', 'No');
      }

      $is_uptodate = $antivirus->is_uptodate;
      if ($is_uptodate == 1)
      {
        $is_uptodate_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_uptodate_val = pgettext('global', 'No');
      }

      $myAntiviruses[] = [
        'name'                => $antivirus->name,
        'antivirus_url'       => $antivirus_url,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'is_dynamic'          => $is_dynamic,
        'is_dynamic_val'      => $is_dynamic_val,
        'version'             => $antivirus->antivirus_version,
        'signature'           => $antivirus->signature_version,
        'is_active'           => $is_active,
        'is_active_val'       => $is_active_val,
        'is_uptodate'         => $is_uptodate,
        'is_uptodate_val'     => $is_uptodate_val,
      ];
    }

    $softwares = [];
    foreach ($myItem->softwareversions as $softwareversion)
    {
      if (is_null($softwareversion->software))
      {
        throw new \Exception('Wrong data request', 400);
      }
      $softwareversion_url = $this->genereRootUrl2Link($rootUrl2, '/softwareversions/', $softwareversion->id);

      $software_url = $this->genereRootUrl2Link($rootUrl2, '/softwares/', $softwareversion->software->id);

      $softwares[] = [
        'id'        => $softwareversion->id,
        'name'      => $softwareversion->name,
        'url'       => $softwareversion_url,
        'software'  => [
          'id' => $softwareversion->software->id,
          'name' => $softwareversion->software->name,
          'url' => $software_url,
        ]
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/softwares');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('show', 'computer');
    $viewData->addData('softwares', $softwares);
    $viewData->addData('antiviruses', $myAntiviruses);

    $viewData->addTranslation('software', npgettext('global', 'Software', 'Software', 1));
    $viewData->addTranslation('version', npgettext('global', 'Version', 'Versions', 1));
    $viewData->addTranslation('antivirus', npgettext('global', 'Antivirus', 'Antiviruses', 1));
    $viewData->addTranslation('antivirus_version', pgettext('inventory device', 'Antivirus version'));
    $viewData->addTranslation('manufacturer', npgettext('global', 'Manufacturer', 'Manufacturers', 1));
    $viewData->addTranslation('is_dynamic', pgettext('inventory device', 'Automatic inventory'));
    $viewData->addTranslation('is_active', pgettext('global', 'Active'));
    $viewData->addTranslation('is_uptodate', pgettext('antivirus', 'Up to date'));
    $viewData->addTranslation('signature', pgettext('antivirus', 'Signature database version'));

    return $view->render($response, 'subitem/softwares.html.twig', (array)$viewData);
  }

  /**
   * @param \App\Models\Computer $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    global $basePath;

    $myItem = \App\Models\Computer::
        with('operatingsystems', 'memoryslots', 'processors', 'storages')
      ->withTrashed()
      ->where('id', $item->id)
      ->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $tabInfos = [];

    $fusioninventoried_at = $myItem->getAttribute('fusioninventoried_at');
    if (!is_null($fusioninventoried_at))
    {
      $tabInfos[] = [
        'key'   => 'labelfusioninventoried',
        'value' => pgettext('inventory device', 'Automatically inventoried'),
        'link'  => null,
      ];

      $tabInfos[] = [
        'key'   => 'fusioninventoried',
        'value' => pgettext('inventory device', 'Last automatic inventory') . ' : ' .
                   $fusioninventoried_at->toDateTimeString(),
        'link'  => null,
      ];
    }

    $operatingsystem = '';
    foreach ($myItem->operatingsystems as $os)
    {
      $operatingsystem = $os->name;
      $lts = '';
      if ($os->getRelationValue('pivot')->operatingsystemversion_id > 0)
      {
        $version = \App\Models\Operatingsystemversion::
            where('id', $os->getRelationValue('pivot')
          ->operatingsystemversion_id)
          ->first();
        if (!is_null($version))
        {
          $operatingsystem .= ' ' . $version->name;
          if ($version->is_lts)
          {
            $lts = ' - LTS (Long-Term Support)';
          }
        }
      }
      if ($os->getRelationValue('pivot')->operatingsystemedition_id > 0)
      {
        $edition = \App\Models\Operatingsystemedition::
            where('id', $os->getRelationValue('pivot')
          ->operatingsystemedition_id)
          ->first();
        if (!is_null($edition))
        {
          $operatingsystem .= ' ' . $edition->name;
        }
      }
      $operatingsystem .= $lts;
    }
    $tabInfos[] = [
      'key'   => 'operatingsystem',
      'value' => npgettext('inventory device', 'Operating System', 'Operating Systems', 1) . ' : ' . $operatingsystem,
      'link'  => $basePath . '/view/computers/' . $item->id . '/operatingsystem',
    ];

    $memoryTotalSize = 0;
    $slotIds = [];
    foreach ($myItem->memoryslots as $slot)
    {
      $slotIds[] = $slot->id;
      if (!is_null($slot->memorymodule))
      {
        if ($slot->memorymodule->size > 0)
        {
          $memoryTotalSize = $memoryTotalSize + $slot->memorymodule->size;
        }
      }
    }

    if ($memoryTotalSize >= 1048576)
    {
      $tabInfos[] = [
        'key'   => 'memorytotalsize',
        'value' => pgettext('inventory device', 'Total memory') . ' : ' . ceil($memoryTotalSize / 1048576) . ' Tio',
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
    }
    elseif ($memoryTotalSize >= 1024)
    {
      $tabInfos[] = [
        'key'   => 'memorytotalsize',
        'value' => pgettext('inventory device', 'Total memory') . ' : ' . ceil($memoryTotalSize / 1024) . ' Gio',
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
    } else {
      $tabInfos[] = [
        'key'   => 'memorytotalsize',
        'value' => pgettext('inventory device', 'Total memory') . ' : ' . $memoryTotalSize . ' Mio',
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
    }

    foreach ($myItem->processors as $processor)
    {
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id,
        'value' => npgettext('global', 'Processor', 'Processors', 1) . ' : ' . $processor->name,
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id . '_frequency',
        'value' => ' - ' . pgettext('global', 'Frequency (MHz)') . ' : ' .
                   $processor->getRelationValue('pivot')->frequency,
        'link'  => null,
      ];
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id . '_nbcores_nbthreads',
        'value' => ' - ' . pgettext('inventory device', 'Number of cores') . ' / ' .
                   pgettext('inventory device', 'Number of threads') . ' : ' .
                   $processor->getRelationValue('pivot')->nbcores . ' / ' .
                   $processor->getRelationValue('pivot')->nbthreads,
        'link'  => null,
      ];
    }

    foreach ($myItem->storages as $storage)
    {
      $tabInfos[] = [
        'key'   => 'storage_' . $storage->id,
        'value' => npgettext('global', 'Storage', 'Storages', 1) . ' : ' . $storage->name,
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
      $tabInfos[] = [
        'key'   => 'storage_' . $storage->id . '_capacity',
        'value' => ' - ' . pgettext('global', 'Size (Mio)') . ' : ' .
                   $storage->size,
        'link'  => null,
      ];
    }

    return $tabInfos;
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubVirtualization(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Computer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('virtualization')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/virtualization');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myVirtualmachines = [];
    foreach ($myItem->virtualization as $virtualization)
    {
      $virtualmachinesystem = '';
      $virtualmachinesystem_url = '';
      if ($virtualization->system !== null)
      {
        $virtualmachinesystem = $virtualization->system->name;
        $virtualmachinestate_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/virtualmachinesystems/',
          $virtualization->system->id
        );
      }

      $virtualmachinemodel = '';
      $virtualmachinemodel_url = '';
      if ($virtualization->type !== null)
      {
        $virtualmachinemodel = $virtualization->type->name;
        $virtualmachinemodel_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/virtualmachinetypes/',
          $virtualization->type->id
        );
      }

      $virtualmachinestate = '';
      $virtualmachinestate_url = '';
      if ($virtualization->state !== null)
      {
        $virtualmachinestate = $virtualization->state->name;
        $virtualmachinestate_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/virtualmachinestates/',
          $virtualization->state->id
        );
      }

      if ($virtualization->is_dynamic == 1)
      {
        $auto_val = pgettext('global', 'Yes');
      }
      else
      {
        $auto_val = pgettext('global', 'No');
      }

      $machine_host = '';
      if ($virtualization->uuid != '' && $virtualization->uuid !== null)
      {
        $item2 = new \App\Models\Computer();
        $myItem2 = $item2::where('uuid', $virtualization->uuid)->get();

        foreach ($myItem2 as $host)
        {
          $machine_host = $host->name;
        }
      }

      $myVirtualmachines[] = [
        'name'                        => $virtualization->name,
        'comment'                     => $virtualization->comment,
        'auto'                        => $virtualization->is_dynamic,
        'auto_val'                    => $auto_val,
        'virtualmachinesystem'        => $virtualmachinesystem,
        'virtualmachinesystem_url'    => $virtualmachinesystem_url,
        'virtualmachinemodel'         => $virtualmachinemodel,
        'virtualmachinemodel_url'     => $virtualmachinemodel_url,
        'virtualmachinestate'         => $virtualmachinestate,
        'virtualmachinestate_url'     => $virtualmachinestate_url,
        'uuid'                        => $virtualization->uuid,
        'nb_proc'                     => $virtualization->vcpu,
        'memory'                      => $virtualization->ram,
        'machine_host'                => $machine_host,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('virtualmachines', $myVirtualmachines);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('comment', npgettext('global', 'Comment', 'Comments', 2));
    $viewData->addTranslation('auto', pgettext('inventory device', 'Automatic inventory'));
    $viewData->addTranslation(
      'virtualmachinesystem',
      npgettext('global', 'Virtualization system', 'Virtualization systems', 1)
    );
    $viewData->addTranslation(
      'virtualmachinemodel',
      npgettext('global', 'Virtualization model', 'Virtualization models', 1)
    );
    $viewData->addTranslation(
      'virtualmachinestate',
      pgettext('inventory device', 'Status')
    );
    $viewData->addTranslation('uuid', pgettext('global', 'UUID'));
    $viewData->addTranslation('nb_proc', pgettext('global', 'processor number'));
    $viewData->addTranslation(
      'memory',
      sprintf('%1$s (%2$s)', pgettext('inventory device', 'Memory size'), pgettext('global', 'Mio'))
    );
    $viewData->addTranslation('machine_host', 'Machine hote');

    return $view->render($response, 'subitem/virtualization.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubConnections(Request $request, Response $response, array $args): Response
  {
    $view = Twig::fromRequest($request);

    $computer = \App\Models\Computer::
        where('id', $args['id'])
      ->with(['connectionMonitors', 'connectionPeripherals', 'connectionPhones', 'connectionPrinters'])
      ->first();
    if (is_null($computer))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/connections');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $items = [];
    foreach ($computer->connectionMonitors as $connection)
    {
      $items[] = $connection;
    }
    foreach ($computer->connectionPeripherals as $connection)
    {
      $items[] = $connection;
    }
    foreach ($computer->connectionPhones as $connection)
    {
      $items[] = $connection;
    }
    foreach ($computer->connectionPrinters as $connection)
    {
      $items[] = $connection;
    }

    $myConnections = [];
    foreach ($items as $connection)
    {
      $type_fr = $connection->getTitle();
      $type = $connection->getTable();

      $name = $connection->name;
      if ($name == '')
      {
        $name = '(' . $connection->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $connection->id);

      $entity = '';
      $entity_url = '';
      if ($connection->entity !== null)
      {
        $entity = $connection->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $connection->entity->id);
      }

      if ($connection->is_dynamic == 1)
      {
        $auto_val = pgettext('global', 'Yes');
      }
      else
      {
        $auto_val = pgettext('global', 'No');
      }


      $serial_number = $connection->serial;

      $inventaire_number = $connection->otherserial;

      $myConnections[] = [
        'type'                 => $type_fr,
        'name'                 => $name,
        'url'                  => $url,
        'auto'                 => $connection->is_dynamic,
        'auto_val'             => $auto_val,
        'entity'               => $entity,
        'entity_url'           => $entity_url,
        'serial_number'        => $serial_number,
        'inventaire_number'    => $inventaire_number,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myConnections, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myConnections);
    array_multisort(array_column($myConnections, 'type'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myConnections);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($computer, $request);
    $viewData->addRelatedPages($computer->getRelatedPages($rootUrl));

    $viewData->addData('fields', $computer->getFormData($computer));
    $viewData->addData('connections', $myConnections);
    $viewData->addData('show', 'computer');

    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('auto', pgettext('inventory device', 'Automatic inventory'));
    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', pgettext('inventory device', 'Serial number'));
    $viewData->addTranslation('inventaire_number', pgettext('inventory device', 'Inventory number'));
    $viewData->addTranslation('no_connection_found', pgettext('inventory device', 'Not connected.'));

    return $view->render($response, 'subitem/connections.html.twig', (array)$viewData);
  }
}
