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

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
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

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
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
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/cartridgeitems')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $cartridgeitem->delete();
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
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
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
        $status = npgettext('cartridge', 'Worn', 'Worn', 1);
        $total_out = $total_out + 1;
      }
      elseif ($date_use !== null)
      {
        $status = npgettext('cartridge', 'Used', 'Used', 1);
        $total_use = $total_use + 1;
      }
      else
      {
        $status = npgettext('cartridge', 'New', 'New', 1);
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
          $printer_counter = sprintf(npgettext('cartridge', '%d printed page', '%d printed pages', $pp), $pp);
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

    $viewData->addTranslation('status', pgettext('inventory device', 'State'));
    $viewData->addTranslation('date_add', pgettext('global', 'Add date'));
    $viewData->addTranslation('date_use', pgettext('inventory device', 'Use date'));
    $viewData->addTranslation('date_end', pgettext('global', 'End date'));
    $viewData->addTranslation('use_on', pgettext('cartridge', 'Used on'));
    $viewData->addTranslation('printer_counter', pgettext('printer', 'Printer counter'));
    $viewData->addTranslation('cartridges_use', pgettext('cartridge', 'Used cartridges'));
    $viewData->addTranslation('cartridges_out', pgettext('cartridge', 'Worn cartridges'));
    $viewData->addTranslation('total', pgettext('global', 'Total'));
    $viewData->addTranslation(
      'total_new',
      npgettext('cartridge', 'New', 'New', $total_new)
    );
    $viewData->addTranslation(
      'total_use',
      npgettext('cartridge', 'Used', 'Used', $total_use)
    );
    $viewData->addTranslation(
      'total_out',
      npgettext('cartridge', 'Worn', 'Worn', $total_out)
    );

    return $view->render($response, 'subitem/cartridges.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubPrintermodels(Request $request, Response $response, array $args): Response
  {
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

    $viewData->addTranslation('name', pgettext('global', 'Name'));

    return $view->render($response, 'subitem/printermodels.html.twig', (array)$viewData);
  }
}
