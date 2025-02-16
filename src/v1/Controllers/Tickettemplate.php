<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Tickettemplate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;

  // Sub
  use History;

  protected $model = \App\Models\Tickettemplate::class;
  protected $rootUrl2 = '/dropdowns/ticketemplates/';

  protected function instanciateModel(): \App\Models\Tickettemplate
  {
    return new \App\Models\Tickettemplate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Tickettemplate::class);

    $tickettemplate = new \App\Models\Tickettemplate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($tickettemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $tickettemplate = \App\Models\Tickettemplate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The ticket template has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($tickettemplate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/tickettemplates/' . $tickettemplate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/tickettemplates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Tickettemplate::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $tickettemplate = \App\Models\Tickettemplate::where('id', $id)->first();
    if (is_null($tickettemplate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($tickettemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $tickettemplate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The ticket template has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($tickettemplate, 'update');

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
    $tickettemplate = \App\Models\Tickettemplate::withTrashed()->where('id', $id)->first();
    if (is_null($tickettemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($tickettemplate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $tickettemplate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The ticket template has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/tickettemplates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $tickettemplate->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The ticket template has been soft deleted successfully');
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
    $tickettemplate = \App\Models\Tickettemplate::withTrashed()->where('id', $id)->first();
    if (is_null($tickettemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($tickettemplate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $tickettemplate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The ticket template has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubMandatoryFields(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Tickettemplate();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('mandatoryfields')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/mandatoryfields');

    $myMandatoryFields = [];
    foreach ($myItem->mandatoryfields as $mandatoryfield)
    {
      $interface = '';
      // if ($mandatoryfield->interface !== null)
      // {
      //   $interface = $mandatoryfield->interface->name;
      // }

      $myMandatoryFields[] = [
        'name'        => $mandatoryfield->num,
        'interface'   => $interface,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('mandatoryfields', $myMandatoryFields);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('interface', $translator->translate('Interface'));

    return $view->render($response, 'subitem/tickettemplatemandatoryfields.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubPredefinedFields(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Tickettemplate();
    $view = Twig::fromRequest($request);

    $myItem = \App\Models\Tickettemplate::with('predefinedfields')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/predefinedfields');

    $myPredefinedFields = [];
    foreach ($myItem->predefinedfields as $predefinedfield)
    {
      $name = '';
      $definitions = $item->getDefinitions();
      foreach ($definitions as $def)
      {
        if ($def->id == $predefinedfield->num)
        {
          $name = $def->name;
        }
      }

      $myPredefinedFields[] = [
        'name'      => $name,
        'value'     => $predefinedfield->value,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('predefinedfields', $myPredefinedFields);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('value', 'Valeur');

    return $view->render($response, 'subitem/tickettemplatepredefinedfields.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubHiddenFields(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Tickettemplate();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('hiddenfields')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/hiddenfields');

    $myHiddenFields = [];
    foreach ($myItem->hiddenfields as $hiddenfield)
    {
      $name = $hiddenfield->num;

      $myHiddenFields[] = [
        'name'    => $name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('hiddenfields', $myHiddenFields);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/tickettemplatehiddenfields.html.twig', (array)$viewData);
  }
}
