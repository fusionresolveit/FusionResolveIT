<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Appliance extends Common
{
  protected $model = '\App\Models\Appliance';
  protected $rootUrl2 = '/appliances/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Appliance();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Appliance();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Appliance();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Applianceitem();
    $myItem2 = $item2::where('appliance_id', $args['id'])->get();

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/items');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myItems = [];
    foreach ($myItem2 as $appliance_item)
    {
      $item3 = new $appliance_item->item_type();
      $myItem3 = $item3->find($appliance_item->item_id);

      if ($myItem3 != null) {
        $type = '';
        $name = '';
        $url = '';
        $serial_number = '';
        $inventaire_number = '';
        $relations = [];


        $type = $item3->getTitle();

        $name = $myItem3->name;
        if ($name == '') {
          $name = '(' . $myItem3->id . ')';
        }

        $url = '';
        if ($rootUrl2 != '') {
          $table = $item3->getTable();
          if ($table != '') {
            $url = $rootUrl2 . "/" . $table . "/" . $myItem3->id;
          }
        }

        $serial_number = $myItem3->serial;
        $inventaire_number = $myItem3->otherserial;


        $item4 = new \App\Models\Applianceitemrelation();
        $myItem4 = $item4::where('appliance_item_id', $appliance_item->id)->get();

        foreach ($myItem4 as $appliance_item_relation) {
          $item5 = new $appliance_item_relation->item_type();
          $myItem5 = $item5->find($appliance_item_relation->item_id);

          if ($myItem5 != null) {
            $relation_type = $item5->getTitle();
            $relation_name = $myItem5->name;
            if ($relation_name == '') {
              $relation_name = '(' . $myItem5->id . ')';
            }
            $relation_url = '';
            if ($rootUrl2 != '') {
              $table = $item5->getTable();
              if ($table != '') {
                $relation_url = $rootUrl2 . "/" . $table . "/" . $myItem5->id;
              }
            }

            $relations[] = [
              'type'        => $relation_type,
              'name'        => $relation_name,
              'url'         => $relation_url,
            ];
          }
        }

        // tri ordre alpha
        usort($relations, function ($a, $b)
        {
          return strtolower($a['name']) > strtolower($b['name']);
        });
        usort($relations, function ($a, $b)
        {
          return strtolower($a['type']) > strtolower($b['type']);
        });

        $myItems[] = [
          'type'                 => $type,
          'name'                 => $name,
          'url'                  => $url,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
          'relations'            => $relations,
        ];
      }
    }

    // tri ordre alpha
    usort($myItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });
    usort($myItems, function ($a, $b)
    {
      return strtolower($a['type']) > strtolower($b['type']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myItems);
    $viewData->addData('show', 'appliance');

    $viewData->addTranslation('type', $translator->translate('Item type'));
    $viewData->addTranslation('name', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation('relations', $translator->translatePlural('appliance' . "\004" . 'Relation', 'appliance' . "\004" . 'Relations', 2));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }
}
