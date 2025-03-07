<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostCartridgeitem;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Note;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Cartridgeitem extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Note;
  use Externallink;
  use Document;
  use History;
  use Infocom;

  protected $model = \App\Models\Cartridgeitem::class;
  protected $rootUrl2 = '/cartridgeitems/';

  protected function instanciateModel(): \App\Models\Cartridgeitem
  {
    return new \App\Models\Cartridgeitem();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostCartridgeitem((object) $request->getParsedBody());

    $cartridgeitem = new \App\Models\Cartridgeitem();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($cartridgeitem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cartridgeitem = \App\Models\Cartridgeitem::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($cartridgeitem, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/cartridgeitems/' . $cartridgeitem->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/cartridgeitems')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostCartridgeitem((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cartridgeitem = \App\Models\Cartridgeitem::where('id', $id)->first();
    if (is_null($cartridgeitem))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($cartridgeitem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $cartridgeitem->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($cartridgeitem, 'update');

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
    $cartridgeitem = \App\Models\Cartridgeitem::withTrashed()->where('id', $id)->first();
    if (is_null($cartridgeitem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($cartridgeitem->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cartridgeitem->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/cartridgeitems')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cartridgeitem->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item has been soft deleted successfully');
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
    $cartridgeitem = \App\Models\Cartridgeitem::withTrashed()->where('id', $id)->first();
    if (is_null($cartridgeitem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($cartridgeitem->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cartridgeitem->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The cartridge item has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubCartridges(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Cartridgeitem();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('cartridges')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/cartridges');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myCartridges_use = [];
    $myCartridges_out = [];
    $total = 0;
    $total_new = 0;
    $total_use = 0;
    $total_out = 0;
    $pages = [];
    foreach ($myItem->cartridges as $cartridge)
    {
      $status = '';
      $date_add = $cartridge->date_in;
      $date_use = $cartridge->date_use;
      $date_end = $cartridge->date_out;
      $use_on = '';
      $url = '';

      if ($date_end !== null)
      {
        $status = $translator->translatePlural('cartridge' . "\004" . 'Worn', 'cartridge' . "\004" . 'Worn', 1);
        $total_out = $total_out + 1;
      }
      elseif ($date_use !== null)
      {
        $status = $translator->translatePlural('cartridge' . "\004" . 'Used', 'cartridge' . "\004" . 'Used', 1);
        $total_use = $total_use + 1;
      }
      else
      {
        $status = $translator->translatePlural('cartridge' . "\004" . 'New', 'cartridge' . "\004" . 'New', 1);
        $total_new = $total_new + 1;
      }
      $total = $total + 1;

      $use_on = '';
      $printer_counter = '';
      if ($cartridge->printer !== null)
      {
        $use_on = $cartridge->printer->name;

        $url = $this->genereRootUrl2Link($rootUrl2, '/printers/', $cartridge->printer->id);

        if (array_key_exists($cartridge->printer->id, $pages) !== true)
        {
          $pages[$cartridge->printer->id] = $cartridge->printer->init_pages_counter;
        }

        if ($pages[$cartridge->printer->id] < $cartridge->pages)
        {
          $pp = $cartridge->pages - $pages[$cartridge->printer->id];
          $printer_counter = sprintf($translator->translatePlural('%d printed page', '%d printed pages', $pp), $pp);
          $pages[$cartridge->printer->id] = $cartridge->pages;
        }
      }

      if ($cartridge->date_out == null)
      {
        $myCartridges_use[] = [
          'status'       => $status,
          'url'          => $url,
          'date_add'     => $date_add,
          'date_use'     => $date_use,
          'use_on'       => $use_on,
        ];
      }
      else
      {
        $myCartridges_out[] = [
          'status'            => $status,
          'url'               => $url,
          'date_add'          => $date_add,
          'date_use'          => $date_use,
          'date_end'          => $date_end,
          'use_on'            => $use_on,
          'printer_counter'   => $printer_counter,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('cartridges_use', $myCartridges_use);
    $viewData->addData('cartridges_out', $myCartridges_out);
    $viewData->addData('total', $total);
    $viewData->addData('total_new', $total_new);
    $viewData->addData('total_use', $total_use);
    $viewData->addData('total_out', $total_out);

    $viewData->addTranslation('status', $translator->translate('item' . "\004" . 'State'));
    $viewData->addTranslation('date_add', $translator->translate('Add date'));
    $viewData->addTranslation('date_use', $translator->translate('Use date'));
    $viewData->addTranslation('date_end', $translator->translate('End date'));
    $viewData->addTranslation('use_on', $translator->translate('Used on'));
    $viewData->addTranslation('printer_counter', $translator->translate('Printer counter'));
    $viewData->addTranslation('cartridges_use', $translator->translate('Used cartridges'));
    $viewData->addTranslation('cartridges_out', $translator->translate('Worn cartridges'));
    $viewData->addTranslation('total', $translator->translate('Total'));
    $viewData->addTranslation(
      'total_new',
      $translator->translatePlural('cartridge' . "\004" . 'New', 'cartridge' . "\004" . 'New', $total_new)
    );
    $viewData->addTranslation(
      'total_use',
      $translator->translatePlural('cartridge' . "\004" . 'Used', 'cartridge' . "\004" . 'Used', $total_use)
    );
    $viewData->addTranslation(
      'total_out',
      $translator->translatePlural('cartridge' . "\004" . 'Worn', 'cartridge' . "\004" . 'Worn', $total_out)
    );

    return $view->render($response, 'subitem/cartridges.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubPrintermodels(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Cartridgeitem();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('printermodels')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/printermodels');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myPrintermodels = [];
    foreach ($myItem->printermodels as $printermodel)
    {
      $name = $printermodel->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/printermodels/', $printermodel->id);

      $myPrintermodels[$printermodel->id] = [
        'name'      => $name,
        'url'       => $url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('printermodels', $myPrintermodels);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/printermodels.html.twig', (array)$viewData);
  }
}
