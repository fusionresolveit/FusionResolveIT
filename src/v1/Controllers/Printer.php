<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Printer extends Common
{
  protected $model = '\App\Models\Printer';
  protected $rootUrl2 = '/printers/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Printer();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Printer();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Printer();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubCartridges(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('cartridges')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/cartridges');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myCartridges_use = [];
    $myCartridges_out = [];
    foreach ($myItem->cartridges as $cartridge)
    {
      $url = '';

      $model = '';
      $type = '';
      if ($cartridge->cartridgeitems != null) {
        $model = $cartridge->cartridgeitems->name . '' . $cartridge->cartridgeitems->ref;

        if ($cartridge->cartridgeitems->type != null) {
          $type = $cartridge->cartridgeitems->type->name;
        }

        if ($rootUrl2 != '') {
          $url = $rootUrl2 . "/cartridgeitems/" . $cartridge->cartridgeitems->id;
        }
      }

      $date_add = $cartridge->date_in;
      $date_use = $cartridge->date_use;
      $date_end = $cartridge->date_out;
      $printer_counter = $cartridge->pages;
      $printed_pages = 0; // TODO

      if ($cartridge->date_out == null) {
        $myCartridges_use[] = [
          'model'        => $model,
          'url'          => $url,
          'type'         => $type,
          'date_add'     => $date_add,
          'date_use'     => $date_use,
        ];
      } else {
        $myCartridges_out[] = [
          'model'               => $model,
          'url'                 => $url,
          'type'                => $type,
          'date_add'            => $date_add,
          'date_use'            => $date_use,
          'date_end'            => $date_end,
          'printer_counter'     => $printer_counter,
          'printed_pages'       => $printed_pages,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('cartridges_use', $myCartridges_use);
    $viewData->addData('cartridges_out', $myCartridges_out);

    $viewData->addTranslation('model', $translator->translatePlural('Cartridge model', 'Cartridge models', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Cartridge type', 'Cartridge types', 1));
    $viewData->addTranslation('date_add', $translator->translate('Add date'));
    $viewData->addTranslation('date_use', $translator->translate('Use date'));
    $viewData->addTranslation('date_end', $translator->translate('End date'));
    $viewData->addTranslation('printer_counter', $translator->translate('Printer counter'));
    $viewData->addTranslation('printed_pages', $translator->translate('Printed pages'));

    $viewData->addTranslation('cartridges_use', $translator->translate('Used cartridges'));
    $viewData->addTranslation('cartridges_out', $translator->translate('Worn cartridges'));


    return $view->render($response, 'subitem/cartridgesprinters.html.twig', (array)$viewData);
  }
}
