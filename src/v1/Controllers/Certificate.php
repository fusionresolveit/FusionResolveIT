<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostCertificate;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Associateditem;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Domain;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use App\Traits\Subs\Knowledgebasearticle;
use App\Traits\Subs\Note;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Certificate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Note;
  use Domain;
  use Externallink;
  use Knowledgebasearticle;
  use Document;
  use Contract;
  use Itil;
  use History;
  use Associateditem;
  use Infocom;

  protected $model = \App\Models\Certificate::class;
  protected $rootUrl2 = '/certificates/';
  protected $choose = 'certificates';

  protected function instanciateModel(): \App\Models\Certificate
  {
    return new \App\Models\Certificate();
  }

  /**
   * @return array{
   *          'associatedAppliances': \App\Models\Appliance,
   *          'associatedComputers': \App\Models\Computer,
   *          'associatedPeripherals': \App\Models\Peripheral,
   *          'associatedDomains': \App\Models\Domain,
   *          'associatedSoftwarelicenses': \App\Models\Softwarelicense,
   *          'associatedNetworkequipments': \App\Models\Networkequipment,
   *          'associatedPhones': \App\Models\Phone,
   *          'associatedPrinters': \App\Models\Printer,
   *          'associatedUsers': \App\Models\User
   *         }
   */
  protected function modelsForSubAssociateditem()
  {
    return [
      'associatedAppliances'        => new \App\Models\Appliance(),
      'associatedComputers'         => new \App\Models\Computer(),
      'associatedPeripherals'       => new \App\Models\Peripheral(),
      'associatedDomains'           => new \App\Models\Domain(),
      'associatedSoftwarelicenses'  => new \App\Models\Softwarelicense(),
      'associatedNetworkequipments' => new \App\Models\Networkequipment(),
      'associatedPhones'            => new \App\Models\Phone(),
      'associatedPrinters'          => new \App\Models\Printer(),
      'associatedUsers'             => new \App\Models\User(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostCertificate((object) $request->getParsedBody());

    $certificate = new \App\Models\Certificate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($certificate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $certificate = \App\Models\Certificate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($certificate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/certificates/' . $certificate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/certificates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostCertificate((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $certificate = \App\Models\Certificate::where('id', $id)->first();
    if (is_null($certificate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($certificate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $certificate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($certificate, 'update');

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
    $certificate = \App\Models\Certificate::withTrashed()->where('id', $id)->first();
    if (is_null($certificate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($certificate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $certificate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/certificates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $certificate->delete();
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
    $certificate = \App\Models\Certificate::withTrashed()->where('id', $id)->first();
    if (is_null($certificate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($certificate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $certificate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
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
    $item = new \App\Models\Certificate();
    $view = Twig::fromRequest($request);

    $certificate = \App\Models\Certificate::where('id', $args['id'])->with('domains')->first();
    if (is_null($certificate))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/domains');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDomains = [];

    foreach ($certificate->domains as $domain)
    {
      $entity = '';
      $entity_url = '';
      if ($domain->entity !== null)
      {
        $entity = $domain->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $domain->entity->id);
      }

      $grouptech = '';
      $grouptech_url = '';
      if ($domain->grouptech !== null)
      {
        $grouptech = $domain->grouptech->completename;
        $grouptech_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $domain->grouptech->id);
      }

      $usertech = '';
      $usertech_url = '';
      if ($domain->usertech !== null)
      {
        $usertech = $this->genereUserName(
          $domain->usertech->name,
          $domain->usertech->lastname,
          $domain->usertech->firstname
        );
        $usertech_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $domain->usertech->id);
      }

      $type = '';
      $type_url = '';
      if ($domain->type !== null)
      {
        $type = $domain->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domaintypes/', $domain->type->id);
      }

      $relation = '';
      $relation_url = '';

      $domainrelation_id = $domain->getRelationValue('pivot')->domainrelation_id;
      if ($domainrelation_id > 0)
      {
        $domainrelation = \App\Models\Domainrelation::where('id', 1)->first();
        if (!is_null($domainrelation))
        {
          $relation = $domainrelation->name;
          $relation_url = $this->genereRootUrl2Link($rootUrl2, '/domainrelations/', $domainrelation->id);
        }
      }

      $alert_expiration = false;
      $date_expiration = $domain->date_expiration;
      if ($date_expiration == null)
      {
        $date_expiration = pgettext('management', 'Does not expire');
      }
      else
      {
        if ($date_expiration < date('Y-m-d H:i:s'))
        {
          $alert_expiration = true;
        }
      }

      $myDomains[] = [
        'name'              => $domain->name,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'group'             => $grouptech,
        'group_url'         => $grouptech_url,
        'user'              => $usertech,
        'user_url'          => $usertech_url,
        'type'              => $type,
        'type_url'          => $type_url,
        'relation'          => $relation,
        'relation_url'      => $relation_url,
        'date_create'       => $domain->created_at,
        'date_exp'          => $date_expiration,
        'alert_expiration'  => $alert_expiration,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($certificate, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($certificate));
    $viewData->addData('domains', $myDomains);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('group', pgettext('inventory device', 'Group in charge'));
    $viewData->addTranslation('user', pgettext('inventory device', 'Technician in charge'));
    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('relation', npgettext('global', 'Domain relation', 'Domain relations', 1));
    $viewData->addTranslation('date_create', pgettext('global', 'Creation date'));
    $viewData->addTranslation('date_exp', pgettext('global', 'Expiration date'));

    return $view->render($response, 'subitem/domains.html.twig', (array)$viewData);
  }
}
