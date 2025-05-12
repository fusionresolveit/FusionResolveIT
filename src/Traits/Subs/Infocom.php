<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Infocom
{
  /**
   * @param array<string, string> $args
   */
  public function showSubInfocoms(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('infocom')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/infocom');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myInfocom = [];
    foreach ($myItem->infocom as $infocom)
    {
      $comment = $infocom->comment;

      $entity_id = $infocom->entity_id;
      $entity_name = '';
      if ($infocom->entity !== null)
      {
        $entity_name = $infocom->entity->completename;
      }

      $is_recursive = $infocom->is_recursive;

      $buy_date = $infocom->buy_date;

      $use_date = $infocom->use_date;

      $warranty_duration = $infocom->warranty_duration;

      $warranty_info = $infocom->warranty_info;

      $supplier_id = $infocom->supplier_id;
      $supplier_name = '';
      if ($infocom->supplier !== null)
      {
        $supplier_name = $infocom->supplier->name;
      }

      $order_number = $infocom->order_number;

      $delivery_number = $infocom->delivery_number;

      $immo_number = $infocom->immo_number;

      $value = $this->showCosts($infocom->value);

      $warranty_value = $this->showCosts($infocom->warranty_value);

      $sink_time = $infocom->sink_time;

      $sink_type = $infocom->sink_type;

      $sink_coeff = $infocom->sink_coeff;

      $bill = $infocom->bill;

      $budget_id = $infocom->budget_id;
      $budget_name = '';
      if ($infocom->budget !== null)
      {
        $budget_name = $infocom->budget->name;
      }

      $alert = $infocom->alert;

      $order_date = $infocom->order_date;

      $delivery_date = $infocom->delivery_date;

      $inventory_date = $infocom->inventory_date;

      $warranty_date = $infocom->warranty_date;

      // $decommission_date = '';
      // $decommission_date_tmp = explode(' ', $infocom->decommission_date);
      // if (count($decommission_date_tmp) == 2)
      // {
      //   $decommission_date = $decommission_date_tmp[0];
      // }
      $decommission_date = $infocom->decommission_date;

      $businesscriticity_id = $infocom->businesscriticity_id;
      $businesscriticity_name = '';
      if ($infocom->businesscriticity !== null)
      {
        $businesscriticity_name = $infocom->businesscriticity->name;
      }

      $myInfocom = [
        'comment'                   => $comment,
        'entity_id'                 => $entity_id,
        'entity_name'               => $entity_name,
        'is_recursive'              => $is_recursive,
        'buy_date'                  => $buy_date,
        'use_date'                  => $use_date,
        'warranty_duration'         => $warranty_duration,
        'warranty_info'             => $warranty_info,
        'supplier_id'               => $supplier_id,
        'supplier_name'             => $supplier_name,
        'order_number'              => $order_number,
        'delivery_number'           => $delivery_number,
        'immo_number'               => $immo_number,
        'value'                     => $value,
        'warranty_value'            => $warranty_value,
        'sink_time'                 => $sink_time,
        'sink_type'                 => $sink_type,
        'sink_coeff'                => $sink_coeff,
        'bill'                      => $bill,
        'budget_id'                 => $budget_id,
        'budget_name'               => $budget_name,
        'alert'                     => $alert,
        'order_date'                => $order_date,
        'delivery_date'             => $delivery_date,
        'inventory_date'            => $inventory_date,
        'warranty_date'             => $warranty_date,
        'decommission_date'         => $decommission_date,
        'businesscriticity_id'      => $businesscriticity_id,
        'businesscriticity_name'    => $businesscriticity_name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionInfocom');
    $myItemData = [];
    if (count($myInfocom) > 0)
    {
      $myItemData = [
        'order_date'          => $myInfocom['order_date'],
        'buy_date'            => $myInfocom['buy_date'],
        'delivery_date'       => $myInfocom['delivery_date'],
        'use_date'            => $myInfocom['use_date'],
        'inventory_date'      => $myInfocom['inventory_date'],
        'decommission_date'   => $myInfocom['decommission_date'],
        'supplier'            => [
          'id'    => $myInfocom['supplier_id'],
          'name'  => $myInfocom['supplier_name'],
        ],
        'budget'              => [
          'id'    => $myInfocom['budget_id'],
          'name'  => $myInfocom['budget_name'],
        ],
        'order_number'        => $myInfocom['order_number'],
        'immo_number'         => $myInfocom['immo_number'],
        'bill'                => $myInfocom['bill'],
        'delivery_number'     => $myInfocom['delivery_number'],
        'value'               => $myInfocom['value'],
        'warranty_value'      => $myInfocom['warranty_value'],
        'sink_type'           => $myInfocom['sink_type'],
        'sink_time'           => $myInfocom['sink_time'],
        'sink_coeff'          => $myInfocom['sink_coeff'],
        'businesscriticity'   => [
          'id'    => $myInfocom['businesscriticity_id'],
          'name'  => $myInfocom['businesscriticity_name'],
        ],
        'comment'             => $myInfocom['comment'],
        'warranty_date'       => $myInfocom['warranty_date'],
        'warranty_duration'   => $myInfocom['warranty_duration'],
        'warranty_info'       => $myInfocom['warranty_info'],
      ];
    }
    $jsonStr = json_encode($myItemData);
    if ($jsonStr === false)
    {
      $jsonStr = '{}';
    }
    $myItemDataObject = json_decode($jsonStr);

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));
    $viewData->addData('item_id', $args['id']);
    $viewData->addData('item_type', $this->model);
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    return $view->render($response, 'subitem/infocom.html.twig', (array)$viewData);
  }
}
