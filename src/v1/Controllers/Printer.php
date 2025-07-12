<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPrinter;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Appliance;
use App\Traits\Subs\Certificate;
use App\Traits\Subs\Component;
use App\Traits\Subs\Connection;
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

final class Printer extends Common implements \App\Interfaces\Crud
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
  use Connection;
  use Infocom;

  protected $model = \App\Models\Printer::class;
  protected $rootUrl2 = '/printers/';
  protected $choose = 'printers';

  protected function instanciateModel(): \App\Models\Printer
  {
    return new \App\Models\Printer();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPrinter((object) $request->getParsedBody());

    $printer = new \App\Models\Printer();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($printer))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $printer = \App\Models\Printer::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($printer, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/printers/' . $printer->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/printers')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPrinter((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $printer = \App\Models\Printer::where('id', $id)->first();
    if (is_null($printer))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($printer))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $printer->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($printer, 'update');

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
    $printer = \App\Models\Printer::withTrashed()->where('id', $id)->first();
    if (is_null($printer))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($printer->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $printer->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/printers')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $printer->delete();
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
    $printer = \App\Models\Printer::withTrashed()->where('id', $id)->first();
    if (is_null($printer))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($printer->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $printer->restore();
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
    $item = new \App\Models\Printer();
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
    $pages = $myItem->init_pages_counter;

    foreach ($myItem->cartridges as $cartridge)
    {
      $model = '';
      $model_url = '';
      $type = '';
      $type_url = '';
      if ($cartridge->cartridgeitems !== null)
      {
        $model = $cartridge->cartridgeitems->name . '' . $cartridge->cartridgeitems->ref;
        $model_url = $this->genereRootUrl2Link($rootUrl2, '/cartridgeitems/', $cartridge->cartridgeitems->id);

        if ($cartridge->cartridgeitems->type !== null)
        {
          $type = $cartridge->cartridgeitems->type->name;
          $type_url = $this->genereRootUrl2Link(
            $rootUrl2,
            '/dropdowns/cartridgeitemtypes/',
            $cartridge->cartridgeitems->type->id
          );
        }
      }

      $date_add = $cartridge->date_in;

      $date_use = $cartridge->date_use;

      $date_end = $cartridge->date_out;

      $printer_counter = $cartridge->pages;

      $printed_pages = 0;
      if ($pages < $cartridge->pages)
      {
        $printed_pages = $cartridge->pages - $pages;
        $pages = $cartridge->pages;
      }

      if ($cartridge->date_out == null)
      {
        $myCartridges_use[] = [
          'model'        => $model,
          'model_url'    => $model_url,
          'type'         => $type,
          'type_url'     => $type_url,
          'date_add'     => $date_add,
          'date_use'     => $date_use,
        ];
      }
      else
      {
        $myCartridges_out[] = [
          'model'               => $model,
          'model_url'           => $model_url,
          'type'                => $type,
          'type_url'            => $type_url,
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

    $viewData->addTranslation('model', npgettext('global', 'Cartridge model', 'Cartridge models', 1));
    $viewData->addTranslation('type', npgettext('global', 'Cartridge type', 'Cartridge types', 1));
    $viewData->addTranslation('date_add', pgettext('global', 'Add date'));
    $viewData->addTranslation('date_use', pgettext('inventory device', 'Use date'));
    $viewData->addTranslation('date_end', pgettext('global', 'End date'));
    $viewData->addTranslation('printer_counter', pgettext('printer', 'Printer counter'));
    $viewData->addTranslation('printed_pages', pgettext('printer', 'Printed pages'));
    $viewData->addTranslation('cartridges_use', pgettext('cartridge', 'Used cartridges'));
    $viewData->addTranslation('cartridges_out', pgettext('cartridge', 'Worn cartridges'));

    return $view->render($response, 'subitem/cartridgesprinters.html.twig', (array)$viewData);
  }

  /**
   * @param \App\Models\Printer $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    $tabInfos = [];

    $fusioninventoried_at = $item->getAttribute('fusioninventoried_at');
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
    return $tabInfos;
  }
}
