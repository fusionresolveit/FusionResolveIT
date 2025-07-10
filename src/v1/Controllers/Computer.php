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

    \App\v1\Controllers\Toolbox::addSessionMessage('The computer has been created successfully');
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

    \App\v1\Controllers\Toolbox::addSessionMessage('The computer has been updated successfully');
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
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/computers')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $computer->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer has been soft deleted successfully');
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
      \App\v1\Controllers\Toolbox::addSessionMessage('The computer has been restored successfully');
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
    global $translator;

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
        $is_dynamic_val = $translator->translate('Yes');
      }
      else
      {
        $is_dynamic_val = $translator->translate('No');
      }

      $is_active = $antivirus->is_active;
      if ($is_active == 1)
      {
        $is_active_val = $translator->translate('Yes');
      }
      else
      {
        $is_active_val = $translator->translate('No');
      }

      $is_uptodate = $antivirus->is_uptodate;
      if ($is_uptodate == 1)
      {
        $is_uptodate_val = $translator->translate('Yes');
      }
      else
      {
        $is_uptodate_val = $translator->translate('No');
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

    $viewData->addTranslation('software', $translator->translatePlural('Software', 'Software', 1));
    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));
    $viewData->addTranslation('antivirus', $translator->translatePlural('Antivirus', 'Antiviruses', 1));
    $viewData->addTranslation('antivirus_version', $translator->translate('Antivirus version'));
    $viewData->addTranslation('manufacturer', $translator->translatePlural('Manufacturer', 'Manufacturers', 1));
    $viewData->addTranslation('is_dynamic', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('is_active', $translator->translate('Active'));
    $viewData->addTranslation('is_uptodate', $translator->translate('Up to date'));
    $viewData->addTranslation('signature', $translator->translate('Signature database version'));

    return $view->render($response, 'subitem/softwares.html.twig', (array)$viewData);
  }

  /**
   * @param \App\Models\Computer $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    global $translator, $basePath;

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
        'value' => $translator->translate('Automatically inventoried'),
        'link'  => null,
      ];

      $tabInfos[] = [
        'key'   => 'fusioninventoried',
        'value' => $translator->translate('Last automatic inventory') . ' : ' .
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
      'value' => $translator->translatePlural('Operating system', 'Operating systems', 1) . ' : ' . $operatingsystem,
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
        'value' => $translator->translate('Total memory') . ' : ' . ceil($memoryTotalSize / 1048576) . ' Tio',
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
    }
    elseif ($memoryTotalSize >= 1024)
    {
      $tabInfos[] = [
        'key'   => 'memorytotalsize',
        'value' => $translator->translate('Total memory') . ' : ' . ceil($memoryTotalSize / 1024) . ' Gio',
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
    } else {
      $tabInfos[] = [
        'key'   => 'memorytotalsize',
        'value' => $translator->translate('Total memory') . ' : ' . $memoryTotalSize . ' Mio',
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
    }

    foreach ($myItem->processors as $processor)
    {
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id,
        'value' => $translator->translatePlural('Processor', 'Processors', 1) . ' : ' . $processor->name,
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id . '_frequency',
        'value' => ' - ' . $translator->translate('Fréquence (MHz)') . ' : ' .
                   $processor->getRelationValue('pivot')->frequency,
        'link'  => null,
      ];
      $tabInfos[] = [
        'key'   => 'processor_' . $processor->id . '_nbcores_nbthreads',
        'value' => ' - ' . $translator->translate('Nombre de cœurs') . ' / ' .
                   $translator->translate('Nombre de threads') . ' : ' .
                   $processor->getRelationValue('pivot')->nbcores . ' / ' .
                   $processor->getRelationValue('pivot')->nbthreads,
        'link'  => null,
      ];
    }

    foreach ($myItem->storages as $storage)
    {
      $tabInfos[] = [
        'key'   => 'storage_' . $storage->id,
        'value' => $translator->translatePlural('Hard drive', 'Hard drives', 1) . ' : ' . $storage->name,
        'link'  => $basePath . '/view/computers/' . $item->id . '/components',
      ];
      $tabInfos[] = [
        'key'   => 'storage_' . $storage->id . '_capacity',
        'value' => ' - ' . $translator->translate('Capacité (Mio)') . ' : ' .
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
    global $translator;

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
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
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

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation(
      'virtualmachinesystem',
      $translator->translatePlural('Virtualization system', 'Virtualization systems', 1)
    );
    $viewData->addTranslation(
      'virtualmachinemodel',
      $translator->translatePlural('Virtualization model', 'Virtualization models', 1)
    );
    $viewData->addTranslation(
      'virtualmachinestate',
      $translator->translate('Status')
    );
    $viewData->addTranslation('uuid', $translator->translate('UUID'));
    $viewData->addTranslation('nb_proc', $translator->translate('processor number'));
    $viewData->addTranslation(
      'memory',
      sprintf('%1$s (%2$s)', $translator->translatePlural('Memory', 'Memories', 1), $translator->translate('Mio'))
    );
    $viewData->addTranslation('machine_host', 'Machine hote');

    return $view->render($response, 'subitem/virtualization.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubConnections(Request $request, Response $response, array $args): Response
  {
    global $translator;

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
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
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

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation('no_connection_found', $translator->translate('Not connected.'));

    return $view->render($response, 'subitem/connections.html.twig', (array)$viewData);
  }
}
