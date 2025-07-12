<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostState;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class State extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\State::class;
  protected $rootUrl2 = '/dropdown/categories/';

  protected function instanciateModel(): \App\Models\State
  {
    return new \App\Models\State();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostState((object) $request->getParsedBody());

    $state = new \App\Models\State();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($state))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $state = \App\Models\State::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($state, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/states/' . $state->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/states')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostState((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $state = \App\Models\State::where('id', $id)->first();
    if (is_null($state))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($state))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $state->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($state, 'update');

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
    $state = \App\Models\State::withTrashed()->where('id', $id)->first();
    if (is_null($state))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($state->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $state->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/states')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $state->delete();
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
    $state = \App\Models\State::withTrashed()->where('id', $id)->first();
    if (is_null($state))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($state->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $state->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubStates(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\State();
    $view = Twig::fromRequest($request);

    $myItem = \App\Models\State::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\State();
    $myItem2 = $item2->where('state_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/categories');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myStates = [];
    foreach ($myItem2 as $current_category)
    {
      $name = $current_category->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdown/categories/', $current_category->id);

      $entity = '';
      $entity_url = '';
      if ($current_category->entity !== null)
      {
        $entity = $current_category->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $current_category->entity->id);
      }

      $is_visible_computer = $current_category->is_visible_computer;
      if ($current_category->is_visible_computer == 1)
      {
        $is_visible_computer_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_computer_val = pgettext('global', 'No');
      }

      $is_visible_monitor = $current_category->is_visible_monitor;
      if ($current_category->is_visible_monitor == 1)
      {
        $is_visible_monitor_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_monitor_val = pgettext('global', 'No');
      }

      $is_visible_networkequipment = $current_category->is_visible_networkequipment;
      if ($current_category->is_visible_networkequipment == 1)
      {
        $is_visible_networkequipment_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_networkequipment_val = pgettext('global', 'No');
      }

      $is_visible_peripheral = $current_category->is_visible_peripheral;
      if ($current_category->is_visible_peripheral == 1)
      {
        $is_visible_peripheral_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_peripheral_val = pgettext('global', 'No');
      }

      $is_visible_phone = $current_category->is_visible_phone;
      if ($current_category->is_visible_phone == 1)
      {
        $is_visible_phone_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_phone_val = pgettext('global', 'No');
      }

      $is_visible_printer = $current_category->is_visible_printer;
      if ($current_category->is_visible_printer == 1)
      {
        $is_visible_printer_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_printer_val = pgettext('global', 'No');
      }

      $is_visible_certificate = $current_category->is_visible_certificate;
      if ($current_category->is_visible_certificate == 1)
      {
        $is_visible_certificate_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_certificate_val = pgettext('global', 'No');
      }

      $is_visible_cluster = $current_category->is_visible_cluster;
      if ($current_category->is_visible_cluster == 1)
      {
        $is_visible_cluster_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_cluster_val = pgettext('global', 'No');
      }

      $is_visible_contract = $current_category->is_visible_contract;
      if ($current_category->is_visible_contract == 1)
      {
        $is_visible_contract_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_contract_val = pgettext('global', 'No');
      }

      $is_visible_appliance = $current_category->is_visible_appliance;
      if ($current_category->is_visible_appliance == 1)
      {
        $is_visible_appliance_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_appliance_val = pgettext('global', 'No');
      }

      $is_visible_pdu = $current_category->is_visible_pdu;
      if ($current_category->is_visible_pdu == 1)
      {
        $is_visible_pdu_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_pdu_val = pgettext('global', 'No');
      }

      $is_visible_softwarelicense = $current_category->is_visible_softwarelicense;    // Licences
      if ($current_category->is_visible_softwarelicense == 1)
      {
        $is_visible_softwarelicense_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_softwarelicense_val = pgettext('global', 'No');
      }

      $is_visible_softwareversion = $current_category->is_visible_softwareversion;    // Versions
      if ($current_category->is_visible_softwareversion == 1)
      {
        $is_visible_softwareversion_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_softwareversion_val = pgettext('global', 'No');
      }

      $is_visible_line = $current_category->is_visible_line;    // Lignes
      if ($current_category->is_visible_line == 1)
      {
        $is_visible_line_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_line_val = pgettext('global', 'No');
      }

      $is_visible_enclosure = $current_category->is_visible_enclosure;    // Châssis
      if ($current_category->is_visible_enclosure == 1)
      {
        $is_visible_enclosure_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_enclosure_val = pgettext('global', 'No');
      }

      $is_visible_rack = $current_category->is_visible_rack;    // Baies
      if ($current_category->is_visible_rack == 1)
      {
        $is_visible_rack_val = pgettext('global', 'Yes');
      }
      else
      {
        $is_visible_rack_val = pgettext('global', 'No');
      }

      $comment = $current_category->comment;

      $myStates[$current_category->id] = [
        'name'                                => $name,
        'url'                                 => $url,
        'entity'                              => $entity,
        'entity_url'                          => $entity_url,
        'is_visible_computer'                 => $is_visible_computer,
        'is_visible_computer_val'             => $is_visible_computer_val,
        'is_visible_monitor'                  => $is_visible_monitor,
        'is_visible_monitor_val'              => $is_visible_monitor_val,
        'is_visible_networkequipment'         => $is_visible_networkequipment,
        'is_visible_networkequipment_val'     => $is_visible_networkequipment_val,
        'is_visible_peripheral'               => $is_visible_peripheral,
        'is_visible_peripheral_val'           => $is_visible_peripheral_val,
        'is_visible_phone'                    => $is_visible_phone,
        'is_visible_phone_val'                => $is_visible_phone_val,
        'is_visible_printer'                  => $is_visible_printer,
        'is_visible_printer_val'              => $is_visible_printer_val,
        'is_visible_certificate'              => $is_visible_certificate,
        'is_visible_certificate_val'          => $is_visible_certificate_val,
        'is_visible_cluster'                  => $is_visible_cluster,
        'is_visible_cluster_val'              => $is_visible_cluster_val,
        'is_visible_contract'                 => $is_visible_contract,
        'is_visible_contract_val'             => $is_visible_contract_val,
        'is_visible_appliance'                => $is_visible_appliance,
        'is_visible_appliance_val'            => $is_visible_appliance_val,
        'is_visible_pdu'                      => $is_visible_pdu,
        'is_visible_pdu_val'                  => $is_visible_pdu_val,
        'is_visible_softwarelicense'          => $is_visible_softwarelicense,
        'is_visible_softwarelicense_val'      => $is_visible_softwarelicense_val,
        'is_visible_softwareversion'          => $is_visible_softwareversion,
        'is_visible_softwareversion_val'      => $is_visible_softwareversion_val,
        'is_visible_line'                     => $is_visible_line,
        'is_visible_line_val'                 => $is_visible_line_val,
        'is_visible_enclosure'                => $is_visible_enclosure,
        'is_visible_enclosure_val'            => $is_visible_enclosure_val,
        'is_visible_rack'                     => $is_visible_rack,
        'is_visible_rack_val'                 => $is_visible_rack_val,
        'comment'                             => $comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('states', $myStates);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('is_visible_computer', npgettext('global', 'Computer', 'Computers', 2));
    $viewData->addTranslation('is_visible_monitor', npgettext('inventory device', 'Monitor', 'Monitors', 2));
    $viewData->addTranslation(
      'is_visible_networkequipment',
      npgettext('global', 'Network device', 'Network devices', 2)
    );
    $viewData->addTranslation('is_visible_peripheral', npgettext('global', 'Peripheral', 'Peripherals', 2));
    $viewData->addTranslation('is_visible_phone', npgettext('global', 'Phone', 'Phones', 2));
    $viewData->addTranslation('is_visible_printer', npgettext('global', 'Printer', 'Printers', 2));
    $viewData->addTranslation('is_visible_softwarelicense', npgettext('global', 'License', 'Licenses', 2));
    $viewData->addTranslation('is_visible_certificate', npgettext('global', 'Certificate', 'Certificates', 2));
    $viewData->addTranslation('is_visible_enclosure', npgettext('global', 'Enclosure', 'Enclosures', 2));
    $viewData->addTranslation('is_visible_pdu', npgettext('global', 'PDU', 'PDUs', 1));
    $viewData->addTranslation('is_visible_line', npgettext('global', 'Line', 'Lines', 2));
    $viewData->addTranslation('is_visible_rack', npgettext('global', 'Rack', 'Racks', 2));
    $viewData->addTranslation('is_visible_softwareversion', npgettext('global', 'Version', 'Versions', 2));
    $viewData->addTranslation('is_visible_cluster', npgettext('global', 'Cluster', 'Clusters', 2));
    $viewData->addTranslation('is_visible_contract', npgettext('global', 'Contract', 'Contracts', 2));
    $viewData->addTranslation('is_visible_appliance', npgettext('global', 'Appliance', 'Appliances', 2));
    $viewData->addTranslation('comment', npgettext('global', 'Comment', 'Comments', 2));

    return $view->render($response, 'subitem/states.html.twig', (array)$viewData);
  }
}
