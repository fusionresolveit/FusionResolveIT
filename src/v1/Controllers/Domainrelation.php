<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardentity;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Domain;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Domainrelation extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Domain;
  use History;

  protected $model = \App\Models\Domainrelation::class;
  protected $rootUrl2 = '/dropdowns/domainrelations/';
  protected $choose = 'domainrelations';

  protected function instanciateModel(): \App\Models\Domainrelation
  {
    return new \App\Models\Domainrelation();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Domainrelation::class);

    $domainrelation = new \App\Models\Domainrelation();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($domainrelation))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domainrelation = \App\Models\Domainrelation::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The domain relation has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($domainrelation, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/domainrelations/' . $domainrelation->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/domainrelations')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Domainrelation::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domainrelation = \App\Models\Domainrelation::where('id', $id)->first();
    if (is_null($domainrelation))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($domainrelation))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domainrelation->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The domain relation has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($domainrelation, 'update');

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
    $domainrelation = \App\Models\Domainrelation::withTrashed()->where('id', $id)->first();
    if (is_null($domainrelation))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($domainrelation->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domainrelation->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain relation has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/domainrelations')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domainrelation->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain relation has been soft deleted successfully');
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
    $domainrelation = \App\Models\Domainrelation::withTrashed()->where('id', $id)->first();
    if (is_null($domainrelation))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($domainrelation->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domainrelation->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain relation has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubDomains(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Domainrelation();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/domains');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDomains = [];
    foreach ($myItem->domains as $domain)
    {
      $name = $domain->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/domains/', $domain->id);

      $entity = '';
      $entity_url = '';
      if ($domain->entity !== null)
      {
        $entity = $domain->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $domain->entity->id);
      }

      $group = '';
      $group_url = '';
      if ($domain->grouptech !== null)
      {
        $group = $domain->grouptech->completename;
        $group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $domain->grouptech->id);
      }

      $user = '';
      $user_url = '';
      if ($domain->usertech !== null)
      {
        $user = $this->genereUserName(
          $domain->usertech->name,
          $domain->usertech->lastname,
          $domain->usertech->firstname
        );
        $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $domain->usertech->id);
      }

      $type = '';
      $type_url = '';
      if ($domain->type !== null)
      {
        $type = $domain->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domaintypes/', $domain->type->id);
      }

      $create_at = $domain->created_at;

      $alert_expiration = false;
      $date_expiration = $domain->date_expiration;
      if ($date_expiration == null)
      {
        $date_expiration = $translator->translate("N'expire pas");
      }
      else
      {
        if ($date_expiration < date('Y-m-d H:i:s'))
        {
          $alert_expiration = true;
        }
      }

      $myDomains[] = [
        'name'                => $name,
        'url'                 => $url,
        'entity'              => $entity,
        'entity_url'          => $entity_url,
        'group'               => $group,
        'group_url'           => $group_url,
        'user'                => $user,
        'user_url'            => $user_url,
        'type'                => $type,
        'type_url'            => $type_url,
        'date_create'         => $create_at,
        'date_exp'            => $date_expiration,
        'alert_expiration'    => $alert_expiration,
      ];
    }

    // tri ordre alpha
    array_multisort(array_column($myDomains, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myDomains);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('domains', $myDomains);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('group', $translator->translate('Group in charge'));
    $viewData->addTranslation('user', $translator->translate('Technician in charge'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('date_create', $translator->translate('Creation date'));
    $viewData->addTranslation('date_exp', $translator->translate('Expiration date'));

    return $view->render($response, 'subitem/domains.html.twig', (array)$viewData);
  }
}
