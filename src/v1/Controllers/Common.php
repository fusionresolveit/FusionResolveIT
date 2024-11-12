<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Common
{
  protected $model = '';
  protected $rootUrl2 = '';

  protected function getUrlWithoutQuery(Request $request)
  {
    $uri = $request->getUri();
    $query = $uri->getQuery();
    $url = (string) $uri;
    if (!empty($query))
    {
      $url = str_replace('?' . $query, '', $url);
    }
    return $url;
  }

  protected function commonGetAll(Request $request, Response $response, $args, $item): Response
  {
    $params = $request->getQueryParams();
    $page = 1;
    $view = Twig::fromRequest($request);

    $search = new \App\v1\Controllers\Search();
    $url = $this->getUrlWithoutQuery($request);
    if (isset($params['page']) && is_numeric($params['page']))
    {
      $page = (int) $params['page'];
    }

    $fields = $search->getData($item, $url, $page, $params);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);
    $viewData->addHeaderTitle('GSIT - ' . $item->getTitle(2));

    $viewData->addData('fields', $fields);

    $viewData->addData('definition', $item->getDefinitions());

    return $view->render($response, 'search.html.twig', (array)$viewData);
  }

  protected function commonShowItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);
    $session = new \SlimSession\Helper();

    $myItem = $item->find($args['id']);

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($this->getUrlWithoutQuery($request)));

    $viewData->addData('fields', $item->getFormData($myItem));

    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

    // Information TOP
    $informations = $this->getInformationTop($myItem, $request);
    foreach ($informations as $info)
    {
      $viewData->addInformation('top', $info['key'], $info['value'], $info['link']);
    }

    // Information BOTTOM
    $informations = $this->getInformationBottom($myItem, $request);
    foreach ($informations as $info)
    {
      $viewData->addInformation('bottom', $info['key'], $info['value'], $info['link']);
    }


    if ($session->exists('message'))
    {
      $viewData->addMessage($session->message);
      $session->delete('message');
    }

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }

  public function commonUpdateItem(Request $request, Response $response, $args, $item): Response
  {
    $data = (object) $request->getParsedBody();
    // $myItem = $item->find($args['id']);

    // // rewrite data with right database name (for dropdown mainly)
    // $definitions = $item->getDefinitions();
    // foreach ($definitions as $def)
    // {
    //   echo "<br>";
    //   if (property_exists($data, $def['name']))
    //   {
    //     if (in_array($def['type'], ['input', 'textarea', 'dropdown']))
    //     {
    //       if ($myItem->{$def['name']} != $data->{$def['name']})
    //       {
    //         $myItem->{$def['name']} = $data->{$def['name']};
    //       }
    //     }
    //     elseif ($def['type'] == 'dropdown_remote')
    //     {
    //       if (isset($def['multiple']))
    //       {
    //         $values = $data->{$def['name']};
    //         if (!is_array($values))
    //         {
    //           if (empty($values))
    //           {
    //             $values = [];
    //           } else {
    //             $values = explode(',', $values);
    //           }
    //         }
    //         // save
    //         $myItem->{$def['name']}()->syncWithPivotValues($values, $def['pivot']);
    //       }
    //       elseif ($myItem->{$def['dbname']} != $data->{$def['name']})
    //       {
    //         $myItem->{$def['dbname']} = $data->{$def['name']};
    //       }
    //     }
    //   }
    // }

    // // update
    // $myItem->save();
    $this->saveItem($data, $args['id']);

    // manage logs => manage it into model

    // post update

    // add message to session
    $session = new \SlimSession\Helper();
    $session->message = "The item has been updated correctly";

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
  }

  protected function commonShowITILItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $session = new \SlimSession\Helper();

    // Load the item
    // $item->loadId($args['id']);
    $myItem = $item->find($args['id']);

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($this->getUrlWithoutQuery($request)));
    $viewData->addHeaderColor($myItem->getColor());

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('feeds', $item->getFeeds($args['id']));
    if (is_null($myItem->content))
    {
      $viewData->addData('content', null);
    } else {
      $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    }

    $viewData->addTranslation('description', $translator->translate('Description'));
    $viewData->addTranslation('feeds', $translator->translate('Feeds'));
    $viewData->addTranslation('followup', $translator->translatePlural('Followup', 'Followups', 1));
    $viewData->addTranslation('solution', $translator->translatePlural('Solution', 'Solutions', 1));
    $viewData->addTranslation('template', $translator->translatePlural('Template', 'Templates', 1));
    $viewData->addTranslation('private', $translator->translate('Private'));
    $viewData->addTranslation('sourcefollow', $translator->translate('Source of followup'));
    $viewData->addTranslation('category', $translator->translatePlural('Category', 'Categories', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('duration', $translator->translate('Duration'));
    $viewData->addTranslation('seconds', $translator->translatePlural('Second', 'Seconds', 2));
    $viewData->addTranslation('minutes', $translator->translatePlural('Minute', 'Minutes', 2));
    $viewData->addTranslation('hours', $translator->translatePlural('Hour', 'Hours', 2));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('addfollowup', $translator->translate('Add followup'));
    $viewData->addTranslation('timespent', $translator->translate('Time spent'));
    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));
    $viewData->addTranslation('yes', $translator->translate('Yes'));
    $viewData->addTranslation('no', $translator->translate('No'));

    if ($session->exists('message'))
    {
      $viewData->addMessage($session->message);
      $session->delete('message');
    }

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  public function showNewItem(Request $request, Response $response, $args): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $item = new $this->model();

    $session = new \SlimSession\Helper();

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addData('fields', $item->getFormData($item));
    $viewData->addData('content', '');

    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

    if ($session->exists('message'))
    {
      $viewData->addMessage($session->message);
      $session->delete('message');
    }

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }

  public function commonShowITILNewItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $session = new \SlimSession\Helper();

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addData('fields', $item->getFormData($item));
    $viewData->addData('feeds', []);
    $viewData->addData('content', '');

    $viewData->addTranslation('description', $translator->translate('Description'));
    $viewData->addTranslation('feeds', $translator->translate('Feeds'));
    $viewData->addTranslation('followup', $translator->translatePlural('Followup', 'Followups', 1));
    $viewData->addTranslation('solution', $translator->translatePlural('Solution', 'Solutions', 1));
    $viewData->addTranslation('template', $translator->translatePlural('Template', 'Templates', 1));
    $viewData->addTranslation('private', $translator->translate('Private'));
    $viewData->addTranslation('sourcefollow', $translator->translate('Source of followup'));
    $viewData->addTranslation('category', $translator->translatePlural('Category', 'Categories', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('duration', $translator->translate('Duration'));
    $viewData->addTranslation('seconds', $translator->translatePlural('Second', 'Seconds', 2));
    $viewData->addTranslation('minutes', $translator->translatePlural('Minute', 'Minutes', 2));
    $viewData->addTranslation('hours', $translator->translatePlural('Hour', 'Hours', 2));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('addfollowup', $translator->translate('Add followup'));
    $viewData->addTranslation('timespent', $translator->translate('Time spent'));
    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));
    $viewData->addTranslation('yes', $translator->translate('Yes'));
    $viewData->addTranslation('no', $translator->translate('No'));

    if ($session->exists('message'))
    {
      $viewData->addMessage($session->message);
      // $session->delete('message');
    }

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  public function showSubHistory(Request $request, Response $response, $args)
  {
    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $session = new \SlimSession\Helper();

    // Load the item
    $myItem = $item->find($args['id']);

    $logs = [];
    if ($myItem != null) {
      $logs = \App\Models\Log::
          where('item_type', ltrim($this->model, '\\'))
        ->where('item_id', $myItem->id)
        ->orderBy('id', 'desc')
        ->get();
    }


    $fieldsTitle = [];
    foreach ($definitions as $def)
    {
      $fieldsTitle[$def['id']] = $def['title'];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/history');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    // $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    $viewData->addData('history', $logs);
    $viewData->addData('titles', $fieldsTitle);

    if ($session->exists('message'))
    {
      $viewData['message'] = $session->message;
      $session->delete('message');
    }

    return $view->render($response, 'subitem/history.html.twig', (array)$viewData);
  }

  public function newItem(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();
    $id = $this->saveItem($data);

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', str_replace('/new', '/' . $id, (string) $uri))
        ->withStatus(302);
      exit;
    }

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  public function saveItem($data, $id = null)
  {
    // Manage fields like dropdown where name not same in database
    $fieldsDef = [];
    $booleans = [];
    $item = new $this->model();
    $definitions = $item->getDefinitions();
    foreach ($definitions as $definition)
    {
      if (isset($definition['fillable']) && $definition['fillable'] && isset($definition['dbname']))
      {
        $fieldsDef[$definition['name']] = $definition['dbname'];
      }
      if ($definition['type'] == 'boolean')
      {
        $booleans[$definition['name']] = true;
      }
    }


    if (is_null($id))
    {
      foreach ((array) $data as $key => $value)
      {
        if (isset($booleans[$key]))
        {
          if ($value == 'on')
          {
            $data->{$key} = true;
          } else {
            $data->{$key} = false;
          }
        }
      }

      $item = $this->model::create((array) $data);
      return $item->id;
    }

    // update
    $item = $this->model::find($id);
    if (is_null($item))
    {
      // Error
      return $id;
    }

    $aData = (array) $data;
    foreach ($aData as $key => $value)
    {
      if (isset($fieldsDef[$key]))
      {
        $aData[$fieldsDef[$key]] = $value;
      }
      if (isset($booleans[$key]))
      {
        if ($value == 'on')
        {
          $aData[$key] = true;
        } else {
          $aData[$key] = false;
        }
      }
    }

    $item->update($aData);

    // manage multiple
    foreach ($definitions as $def)
    {
      if (isset($def['multiple']))
      {
        $key = $def['name'];
        $pivot = [];
        if (isset($def['pivot']))
        {
          $pivot = $def['pivot'];
        }
        if (!is_array($data->{$key}))
        {
          if (empty($data->{$key}))
          {
            $data->{$key} = [];
          } else {
            $data->{$key} = explode(',', $data->{$key});
          }
        }

        $dbItems = [];
        foreach ($item->$key as $relationItem)
        {
          $dbItems[] = $relationItem->id;
        }
        // To delete
        $toDelete = array_diff($dbItems, $data->{$key});
        foreach ($toDelete as $groupId)
        {
          $item->$key()->detach($groupId, $pivot);
        }

        // To add
        $toAdd = array_diff($data->{$key}, $dbItems);
        foreach ($toAdd as $groupId)
        {
          $item->$key()->attach($groupId, $pivot);
        }
      }
    }
    return $item->id;
  }

  protected function getInformationTop($item, $request)
  {
    return [];
  }

  protected function getInformationBottom($item, $request)
  {
    return [];
  }



  public function showSubNotes(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('notes')->find($args['id']);

    $myNotes = [];
    foreach ($myItem->notes as $note)
    {
      $user = '';
      if ($note->user !== null)
      {
        $user = $note->user->name;
      }
      $user_lastupdater = '';
      if ($note->userlastupdater !== null)
      {
        $user_lastupdater = $note->userlastupdater->name;
      }

      $create = sprintf($translator->translate('Create by %1$s on %2$s'), $user, $note->created_at);
      $update = sprintf($translator->translate('Last update by %1$s on %2$s'), $user_lastupdater, $note->updated_at);

      $myNotes[] = [
        'content' => str_ireplace("\n", "<br/>", $note->content),
        'create' => $create,
        'update' => $update,
        'updated_at' => $note->updated_at,
      ];
    }

    // tri de la + récente à la + ancienne
    usort($myNotes, function ($a, $b)
    {
      return $a['updated_at'] < $b['updated_at'];
    });

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/notes');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('notes', $myNotes);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/notes.html.twig', (array)$viewData);
  }

  public function showSubDomains(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('domains')->find($args['id']);

    $myDomains = [];
    foreach ($myItem->domains as $domain)
    {
      $entity = '';
      if ($domain->entity !== null)
      {
        $entity = $domain->entity->name;
      }
      $groupstech = '';
      if ($domain->groupstech !== null)
      {
        $groupstech = $domain->groupstech->name;
      }
      $userstech = '';
      if ($domain->userstech !== null)
      {
        $userstech = $domain->userstech->name;
      }
      $type = '';
      if ($domain->type !== null)
      {
        $type = $domain->type->name;
      }
      $domainrelation = \App\Models\Domainrelation::find($domain->pivot->domainrelation_id);
      $relation = '';
      if ($domainrelation !== null)
      {
        $relation = $domainrelation->name;
      }

      $alert_expiration = false;
      $date_expiration = $domain->date_expiration;
      if ($date_expiration == null) {
        $date_expiration = $translator->translate("N'expire pas");
      } else {
        if ($date_expiration < date('Y-m-d H:i:s')) {
          $alert_expiration = true;
        }
      }

      $myDomains[] = [
        'name'              => $domain->name,
        'entity'            => $entity,
        'group'             => $groupstech,
        'user'              => $userstech,
        'type'              => $type,
        'relation'          => $relation,
        'date_create'       => $domain->created_at,
        'date_exp'          => $date_expiration,
        'alert_expiration'  => $alert_expiration,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/domains');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('domains', $myDomains);

    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('group', $translator->translate('Group in charge'));
    $viewData->addTranslation('user', $translator->translate('Technician in charge'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('relation', $translator->translatePlural('Domain relation', 'Domains relations', 1));
    $viewData->addTranslation('date_create', $translator->translate('Creation date'));
    $viewData->addTranslation('date_exp', $translator->translate('Expiration date'));

    return $view->render($response, 'subitem/domains.html.twig', (array)$viewData);
  }

  public function showSubAppliances(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('appliances')->find($args['id']);

    $myAppliances = [];
    foreach ($myItem->appliances as $appliance)
    {
      $myAppliances[] = [
        'name' => $appliance->name,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/appliances');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('appliances', $myAppliances);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/appliances.html.twig', (array)$viewData);
  }

  public function showSubCertificates(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('certificates')->find($args['id']);

    $myCertificates = [];
    foreach ($myItem->certificates as $certificate)
    {
      $type = '';
      if ($certificate->type !== null)
      {
        $type = $certificate->type->name;
      }
      $entity = '';
      if ($certificate->entity !== null)
      {
        $entity = $certificate->entity->name;
      }

      $alert_expiration = false;
      $date_expiration = $certificate->date_expiration;
      if ($date_expiration == null) {
        $date_expiration = $translator->translate("N'expire pas");
      } else {
        if ($date_expiration < date('Y-m-d H:i:s')) {
          $alert_expiration = true;
        }
      }
      $state = '';
      if ($certificate->state !== null)
      {
        $state = $certificate->state->name;
      }


      $myCertificates[] = [
        'name'              => $certificate->name,
        'entity'            => $entity,
        'type'              => $type,
        'dns_name'          => $certificate->dns_name,
        'dns_suffix'        => $certificate->dns_suffix,
        'created_at'        => $certificate->created_at,
        'date_expiration'   => $date_expiration,
        'alert_expiration'  => $alert_expiration,
        'state'             => $state,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/certificates');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('certificates', $myCertificates);

    $viewData->addTranslation('name', 'Nom');
    $viewData->addTranslation('entity', 'Entité');
    $viewData->addTranslation('type', 'Type');
    $viewData->addTranslation('dns_name', 'Nom DNS');
    $viewData->addTranslation('dns_suffix', 'Suffixe DNS');
    $viewData->addTranslation('created_at', 'Date de création');
    $viewData->addTranslation('date_expiration', "Date d'expiration");
    $viewData->addTranslation('state', 'Statut');

    return $view->render($response, 'subitem/certificates.html.twig', (array)$viewData);
  }

  public function showSubExternalLinks(Request $request, Response $response, $args): Response
  {
    global $translator;

    $computermodelclass = str_ireplace('\\v1\\Controllers\\', '\\Models\\', get_class($this));

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::find($args['id']);

    $item2 = new \App\Models\LinkItemtype();
    $externallinks = $item2::with('links')->where('item_type', $computermodelclass)->get();

    $item3 = new \App\Models\DomainItem();
    $domainitems = $item3->where(['item_id'=>$args['id'],'item_type'=>$computermodelclass])->get();

    $myExternalLinks = [];
    foreach ($externallinks as $externallink)
    {
      $name = '';
      $open_window = 0;
      $link = '';
      $data = '';
      $generate = '';
      if ($externallink->links !== null)
      {
        $name = $externallink->links->name;
        $open_window = $externallink->links->open_window;
        $link = $externallink->links->link;
        $data = $externallink->links->data;


        $location_id = '';
        $location_name = '';
        if ($myItem->location != null)
        {
          $location_id = $myItem->location->id;
          $location_name = $myItem->location->name;
        }

        $domains = [];
        foreach ($domainitems as $domainitem) {
          if ($domainitem->domain != null) {
            $domains[] = $domainitem->domain->name;
          }
        }

        $network_name = '';
        if ($myItem->network != null)
        {
          $network_name = $myItem->network->name;
        }

        $users = '';
        if ($myItem->user != null)
        {
          if (isset($myItem->user->name)) {
            $users[] = $myItem->user->name;
          } else {
            foreach ($myItem->user as $user) {
              $users[] = $user->name;
            }
          }
        }
        $groups = [];
        if ($myItem->group != null)
        {
          if (isset($myItem->group->name)) {
            $groups[] = $myItem->group->name;
          } else {
            foreach ($myItem->group as $group) {
              $groups[] = $group->name;
            }
          }
        }

        $ips = [];
        $macs = [];

        $itemsLink = [
          'id' => $externallink->links->id,
          'name' => $myItem->name,
          'serial' => $myItem->serial,
          'otherserial' => $myItem->otherserial,
          'location_id' => $location_id,
          'location' => $location_name,
          'domains' => $domains,
          'network' => $network_name,
          'comment' => $myItem->comment,
          'users' => $users,
          'groups' => $groups,
          // 'realname' => $realname,
          // 'firstname' => $firstname,
          // 'login' => $login,
          // 'ips' => $ips,
          // 'macs' => $macs,
        ];

        $generate = $name . ' : ' . self::generateLinkContents($data, $itemsLink, true);
      }


      $myExternalLinks[] = [
        'name'          => $name,
        'open_window'   => $open_window,
        'link'          => $link,
        'generate'      => $generate,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/externallinks');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('externallinks', $myExternalLinks);

    return $view->render($response, 'subitem/externallinks.html.twig', (array)$viewData);
  }

  public function generateLinkContents($link, $item, $replaceByBr = false)
  {
    $new_link = $link;
    if ($replaceByBr === true) $new_link=str_ireplace("\n", "<br>", $new_link);
    $matches = [];
    if (preg_match_all('/\[FIELD:(\w+)\]/', $new_link, $matches)) {
      foreach ($matches[1] as $key => $field) {
        $new_link = self::checkAndReplaceProperty($item, $field, $matches[0][$key], $new_link, $replaceByBr);
      }
    }

    if (strstr($new_link, "[ID]")) {
      $new_link = self::checkAndReplaceProperty($item, 'id', "[ID]", $new_link, $replaceByBr);
    }
    if (strstr($link, "[NAME]")) {
      $new_link = self::checkAndReplaceProperty($item, 'name', "[NAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[SERIAL]")) {
      $new_link = self::checkAndReplaceProperty($item, 'serial', "[SERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[OTHERSERIAL]")) {
      $new_link = self::checkAndReplaceProperty($item, 'otherserial', "[OTHERSERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATIONID]")) {
      $new_link = self::checkAndReplaceProperty($item, 'location_id', "[LOCATIONID]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATION]")) {
      $new_link = self::checkAndReplaceProperty($item, 'location', "[LOCATION]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[DOMAIN]")) {
      $new_link = self::checkAndReplaceProperty($item, 'domains', "[DOMAIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[NETWORK]")) {
      $new_link = self::checkAndReplaceProperty($item, 'network', "[NETWORK]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[REALNAME]")) {
      $new_link = self::checkAndReplaceProperty($item, 'realname', "[REALNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[FIRSTNAME]")) {
      $new_link = self::checkAndReplaceProperty($item, 'firstname', "[FIRSTNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOGIN]")) {
      $new_link = self::checkAndReplaceProperty($item, 'login', "[LOGIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[USER]")) {
      $new_link = self::checkAndReplaceProperty($item, 'users', "[USER]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[GROUP]")) {
      $new_link = self::checkAndReplaceProperty($item, 'groups', "[GROUP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[IP]")) {
      $new_link = self::checkAndReplaceProperty($item, 'ips', "[IP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[MAC]")) {
      $new_link = self::checkAndReplaceProperty($item, 'macs', "[MAC]", $new_link, $replaceByBr);
    }

    return $new_link;
  }

  public function checkAndReplaceProperty($item, $field, $strToReplace, $new_link, $replaceByBr = false)
  {
    $ret = $new_link;

    if (array_key_exists($field, $item)) {
      if (is_array($item[$field])) {
        $tmp = '';
        foreach ($item[$field] as $val) {
          if ($tmp != '') $tmp = $tmp  . "\n";
          $tmp = $tmp . $val;
        }
        $ret = str_replace($strToReplace, $tmp, $ret);
      } else {
        $ret = str_replace($strToReplace, $item[$field], $ret);
      }
      if ($replaceByBr === true) $ret = str_ireplace("\n", "<br>", $ret);
    }

    return $ret;
  }

  public function showSubKnowbaseitems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('knowbaseitems')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/knowbaseitems');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myKnowbaseitems = [];
    foreach ($myItem->knowbaseitems as $knowbaseitem)
    {
      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/knowbaseitems/" . $knowbaseitem->id;
      }

      $myKnowbaseitems[$knowbaseitem->id] = [
        'name'           => $knowbaseitem->name,
        'created_at'     => $knowbaseitem->date,
        'updated_at'     => $knowbaseitem->updated_at,
        'url'            => $url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('knowbaseitems', $myKnowbaseitems);

    $viewData->addTranslation('name', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('created_at', $translator->translate('Creation date'));
    $viewData->addTranslation('updated_at', $translator->translate('Update date'));

    return $view->render($response, 'subitem/knowbaseitems.html.twig', (array)$viewData);
  }

  public function showSubDocuments(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('documents')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/documents');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myDocuments = [];
    foreach ($myItem->documents as $document)
    {
      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/documents/" . $document->id;
      }

      $entity = '';
      if ($document->entity != null)
      {
        $entity = $document->entity->name;
      }

      $rubrique = '';
      if ($document->categorie != null)
      {
        $rubrique = $document->categorie->name;
      }


      $myDocuments[$document->id] = [
        'name'          => $document->name,
        'date'          => $document->pivot->updated_at,
        'url'           => $url,
        'entity'        => $entity,
        'file'          => $document->filename,
        'weblink'       => $document->link,
        'rubrique'      => $rubrique,
        'mimetype'      => $document->mime,
        'balise'        => $document->tag,
      ];
    }

    // tri de la + récente à la + ancienne
    usort($myDocuments, function ($a, $b)
    {
      return $a['date'] < $b['date'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('documents', $myDocuments);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('file', $translator->translate('File'));
    $viewData->addTranslation('weblink', $translator->translate('Web link'));
    $viewData->addTranslation('rubrique', $translator->translate('Heading'));
    $viewData->addTranslation('mimetype', $translator->translate('MIME type'));
    $viewData->addTranslation('balise', $translator->translate('Tag'));
    $viewData->addTranslation('date', $translator->translatePlural('Dates', 'Dates', 1));

    return $view->render($response, 'subitem/documents.html.twig', (array)$viewData);
  }

  public function showSubContracts(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('contracts')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/contracts');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myContracts = [];
    foreach ($myItem->contracts as $contract)
    {

      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/contracts/" . $contract->id;
      }

      $entity = '';
      if ($contract->entity != null)
      {
        $entity = $contract->entity->name;
      }

      $type = '';
      if ($contract->type != null)
      {
        $type = $contract->type->name;
      }

      $suppliers = [];
      if ($contract->suppliers != null)
      {
        foreach ($contract->suppliers as $supplier) {
          $suppliers[$supplier->id] = [
            'name' => $supplier->name,
          ];
        }
      }

      $duration = $contract->duration;
      if ($duration == 0) $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', 1), $duration);
      if ($duration != 0) $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', $duration), $duration);

      if ($contract->begin_date != null) {
        $ladate = $contract->begin_date;
        if ($duration != 0)
        {
          $end_date = date('Y-m-d', strtotime('+' . $duration . ' month', strtotime($ladate)));
          if ($end_date < date('Y-m-d')) {
            $end_date = "<span style=\"color: red;\">" . $end_date . "</span>";
          }
          $initial_contract_period = $initial_contract_period . ' => ' . $end_date;
        }
      }

      $myContracts[$contract->id] = [
        'name'                      => $contract->name,
        'url'                       => $url,
        'entity'                    => $entity,
        'number'                    => $contract->num,
        'type'                      => $type,
        'suppliers'                 => $suppliers,
        'start_date'                => $contract->begin_date,
        'initial_contract_period'   => $initial_contract_period,
      ];
    }

    // tri de la + récente à la + ancienne
    usort($myContracts, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('contracts', $myContracts);
    $viewData->addData('show_suppliers', true);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('number', 'Numéro');
    $viewData->addTranslation('type', $translator->translatePlural('Contract type', 'Contract types', 1));
    $viewData->addTranslation('supplier', $translator->translatePlural('Supplier', 'Suppliers', 1));
    $viewData->addTranslation('start_date', $translator->translate('Start date'));
    $viewData->addTranslation('initial_contract_period', $translator->translate('Initial contract period'));

    return $view->render($response, 'subitem/contracts.html.twig', (array)$viewData);
  }

  public function showSubSuppliers(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('suppliers')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/suppliers');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $mySuppliers = [];
    foreach ($myItem->suppliers as $supplier)
    {
      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/suppliers/" . $supplier->id;
      }

      $entity = '';
      if ($supplier->entity != null)
      {
        $entity = $supplier->entity->name;
      }

      $type = '';
      if ($supplier->type != null)
      {
        $type = $supplier->type->name;
      }

      $mySuppliers[$supplier->id] = [
        'name'           => $supplier->name,
        'url'            => $url,
        'entity'         => $entity,
        'type'           => $type,
        'phone'          => $supplier->phonenumber,
        'fax'            => $supplier->fax,
        'website'        => $supplier->website,
      ];
    }

    // tri de la + récente à la + ancienne
    usort($mySuppliers, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('suppliers', $mySuppliers);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Third party type', 'Third party types', 1));
    $viewData->addTranslation('phone', $translator->translatePlural('Phone', 'Phones', 1));
    $viewData->addTranslation('fax', $translator->translate('Fax'));
    $viewData->addTranslation('website', $translator->translate('Website'));

    return $view->render($response, 'subitem/suppliers.html.twig', (array)$viewData);
  }

  public function showSubSoftwares(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('softwareversions')->find($args['id']);

    $myAntiviruses = [];

    $softwares = [];
    foreach ($myItem->softwareversions as $softwareversion)
    {
      $softwares[] = [
        'id' => $softwareversion->id,
        'name' => $softwareversion->name,
        'software' => [
          'id' => $softwareversion->software->id,
          'name' => $softwareversion->software->name,
        ]
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/softwares');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwares', $softwares);
    $viewData->addData('show', 'default');

    $viewData->addTranslation('software', $translator->translatePlural('Software', 'Software', 1));
    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));

    return $view->render($response, 'subitem/softwares.html.twig', (array)$viewData);
  }

  public function showSubOperatingSystem(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('operatingsystems')->find($args['id']);

    $operatingsystem = [];
    foreach ($myItem->operatingsystems as $os)
    {
      $osa = \App\Models\Operatingsystemarchitecture::find($os->pivot->operatingsystemarchitecture_id);
      $osv = \App\Models\Operatingsystemversion::find($os->pivot->operatingsystemversion_id);
      $ossp = \App\Models\Operatingsystemservicepack::find($os->pivot->operatingsystemservicepack_id);
      $oskv = \App\Models\Operatingsystemkernelversion::find($os->pivot->operatingsystemkernelversion_id);
      $ose = \App\Models\Operatingsystemedition::find($os->pivot->operatingsystemedition_id);
      $osln = $os->pivot->license_number;
      $oslid = $os->pivot->licenseid;
      $osid = $os->pivot->installationdate;
      $oswo = $os->pivot->winowner;
      $oswc = $os->pivot->wincompany;
      $osoc = $os->pivot->oscomment;
      $oshid = $os->pivot->hostid;

      $architecture = '';
      if ($osa !== null)
      {
        $architecture = $osa->name;
      }
      $version = '';
      if ($osv !== null)
      {
        $version = $osv->name;
      }
      $servicepack = '';
      if ($ossp !== null)
      {
        $servicepack = $ossp->name;
      }
      $kernelversion = '';
      if ($oskv !== null)
      {
        $kernelversion = $oskv->name;
      }
      $edition = '';
      if ($ose !== null)
      {
        $edition = $ose->name;
      }
      $license_number = '';
      if ($osln !== null)
      {
        $license_number = $osln;
      }
      $licenseid = '';
      if ($oslid !== null)
      {
        $licenseid = $oslid;
      }
      $installationdate = '';
      if ($osid !== null)
      {
        $installationdate = $osid;
      }
      $winowner = '';
      if ($oswo !== null)
      {
        $winowner = $oswo;
      }
      $wincompany = '';
      if ($oswc !== null)
      {
        $wincompany = $oswc;
      }
      $oscomment = '';
      if ($osoc !== null)
      {
        $oscomment = $osoc;
      }
      $hostid = '';
      if ($oshid !== null)
      {
        $hostid = $oshid;
      }

      $operatingsystem = [
        'id' => $os->id,
        'name' => $os->name,
        'architecture' => $architecture,
        'architecture_id' => $os->pivot->operatingsystemarchitecture_id,
        'version' => $version,
        'version_id' => $os->pivot->operatingsystemversion_id,
        'servicepack' => $servicepack,
        'servicepack_id' => $os->pivot->operatingsystemservicepack_id,
        'kernelversion' => $kernelversion,
        'kernelversion_id' => $os->pivot->operatingsystemkernelversion_id,
        'edition' => $edition,
        'edition_id' => $os->pivot->operatingsystemedition_id,
        'licensenumber' => $license_number,
        'licenseid' => $licenseid,
        'installationdate' => $installationdate,
        'winowner' => $winowner,
        'wincompany' => $wincompany,
        'oscomment' => $oscomment,
        'hostid' => $hostid,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/operatingsystem');

    $show = '';
    if ($this->rootUrl2 != '') {
      $show = str_ireplace('/', '', $this->rootUrl2);
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDef = [];
    $myItemData = [];


    $getDefs = $item->getSpecificFunction('getDefinitionOperatingSystem');

    $myItemData = [
      'name'  => $operatingsystem['name'],
      'architecture'  => [
        'id' => $operatingsystem['architecture_id'],
        'name' => $operatingsystem['architecture'],
      ],
      'kernelversion'  => [
        'id' => $operatingsystem['kernelversion_id'],
        'name' => $operatingsystem['kernelversion'],
      ],
      'version'  => [
        'id' => $operatingsystem['version_id'],
        'name' => $operatingsystem['version'],
      ],
      'servicepack'  => [
        'id' => $operatingsystem['servicepack_id'],
        'name' => $operatingsystem['servicepack'],
      ],
      'edition'  => [
        'id' => $operatingsystem['edition_id'],
        'name' => $operatingsystem['edition'],
      ],
      'licenseid'  => $operatingsystem['licenseid'],
      'licensenumber'  => $operatingsystem['licensenumber'],
    ];
    $myItemDataObject = json_decode(json_encode($myItemData));

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));
    $viewData->addData('show', $show);
    $viewData->addData('operatingsystem', $operatingsystem);

    $viewData->addTranslation('entreprise', 'Entreprise');
    $viewData->addTranslation('oscomment', $translator->translate('Comments'));
    $viewData->addTranslation('hostid', 'HostID');
    $viewData->addTranslation('owner', 'Propriétaire');
    $viewData->addTranslation('install_date', $translator->translate('Installation date'));

    return $view->render($response, 'subitem/operatingsystems.html.twig', (array)$viewData);
  }

  public function showSubItil(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('tickets', 'problems', 'changes')->find($args['id']);


    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/itil');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }
    // echo "<pre>";
    // print_r($myItem->tickets);
    // echo "</pre>";
    // die();

    $tickets = [];
    foreach ($myItem->tickets as $ticket)
    {
      // echo "<pre>";
      // print_r($ticket);
      // echo "</pre>";
      // die();

      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/tickets/" . $ticket->id;
      }

      $status = $this->getStatusArray()[$ticket->status];
      $entity = '';
      if ($ticket->entity != null) {
        $entity = $ticket->entity->name;
      }
      $priority = $this->getPriorityArray()[$ticket->priority];
      $requesters = [];
      if ($ticket->requester != null) {
        foreach ($ticket->requester as $requester) {
          $requesters[] = ['name' => $requester->name];
        }
      }
      if ($ticket->requestergroup != null) {
        foreach ($ticket->requestergroup as $requestergroup) {
          $requesters[] = ['name' => $requestergroup->name];
        }
      }
      $technicians = [];
      if ($ticket->technician != null) {
        foreach ($ticket->technician as $technician) {
          $technicians[] = ['name' => $technician->name];
        }
      }
      if ($ticket->techniciangroup != null) {
        foreach ($ticket->techniciangroup as $techniciangroup) {
          $technicians[] = ['name' => $techniciangroup->name];
        }
      }
      $associated_items = []; // TODO
      $category = '';
      if ($ticket->category != null) {
        $category = $ticket->category->name;
      }
      $planification = 0; // TODO


      $tickets[$ticket->id] = [
        'url' => $url,
        'status' => $status,
        'date' => $ticket->date,
        'last_update' => $ticket->updated_at,
        'entity' => $entity,
        'priority' => $priority,
        'requesters' => $requesters,
        'technicians' => $technicians,
        'associated_items' => $associated_items,
        'title' => $ticket->name,
        'category' => $category,
        'planification' => $planification,
      ];

    }

    $problems = [];
    foreach ($myItem->problems as $problem)
    {
      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/problems/" . $problem->id;
      }

      $status = $this->getStatusArray()[$problem->status];
      $entity = '';
      if ($problem->entity != null) {
        $entity = $problem->entity->name;
      }
      $priority = $this->getPriorityArray()[$problem->priority];
      $requesters = [];
      if ($problem->requester != null) {
        foreach ($problem->requester as $requester) {
          $requesters[] = ['name' => $requester->name];
        }
      }
      if ($problem->requestergroup != null) {
        foreach ($problem->requestergroup as $requestergroup) {
          $requesters[] = ['name' => $requestergroup->name];
        }
      }
      $technicians = [];
      if ($problem->technician != null) {
        foreach ($problem->technician as $technician) {
          $technicians[] = ['name' => $technician->name];
        }
      }
      if ($problem->techniciangroup != null) {
        foreach ($problem->techniciangroup as $techniciangroup) {
          $technicians[] = ['name' => $techniciangroup->name];
        }
      }
      $category = '';
      if ($problem->category != null) {
        $category = $problem->category->name;
      }
      $planification = 0; // TODO


      $problems[$problem->id] = [
        'url' => $url,
        'status' => $status,
        'date' => $problem->date,
        'last_update' => $problem->updated_at,
        'entity' => $entity,
        'priority' => $priority,
        'requesters' => $requesters,
        'technicians' => $technicians,
        'title' => $problem->name,
        'category' => $category,
        'planification' => $planification,
      ];

    }

    $changes = [];
    foreach ($myItem->changes as $change)
    {
      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/changes/" . $change->id;
      }

      $status = $this->getStatusArray()[$change->status];
      $entity = '';
      if ($change->entity != null) {
        $entity = $change->entity->name;
      }
      $priority = $this->getPriorityArray()[$change->priority];
      $requesters = [];
      if ($change->requester != null) {
        foreach ($change->requester as $requester) {
          $requesters[] = ['name' => $requester->name];
        }
      }
      if ($change->requestergroup != null) {
        foreach ($change->requestergroup as $requestergroup) {
          $requesters[] = ['name' => $requestergroup->name];
        }
      }
      $technicians = []; // TODO
      if ($change->technician != null) {
        foreach ($change->technician as $technician) {
          $technicians[] = ['name' => $technician->name];
        }
      }
      if ($change->techniciangroup != null) {
        foreach ($change->techniciangroup as $techniciangroup) {
          $technicians[] = ['name' => $techniciangroup->name];
        }
      }
      $category = '';
      if ($change->itilcategorie != null) {
        $category = $change->itilcategorie->name;
      }
      $planification = 0; // TODO


      $changes[$change->id] = [
        'url' => $url,
        'status' => $status,
        'date' => $change->date,
        'last_update' => $change->updated_at,
        'entity' => $entity,
        'priority' => $priority,
        'requesters' => $requesters,
        'technicians' => $technicians,
        'title' => $change->name,
        'category' => $category,
        'planification' => $planification,
      ];
    }

    $tickets_link_elements = [];
    $problems_link_elements = [];
    $changes_link_elements = [];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);
    $viewData->addData('problems', $problems);
    $viewData->addData('changes', $changes);
    $viewData->addData('tickets_link_elements', $tickets_link_elements);
    $viewData->addData('problems_link_elements', $problems_link_elements);
    $viewData->addData('changes_link_elements', $changes_link_elements);

    $viewData->addTranslation('tickets', $translator->translatePlural('Ticket', 'Tickets', 2));
    $viewData->addTranslation('problems', $translator->translatePlural('Problem', 'Problems', 2));
    $viewData->addTranslation('changes', $translator->translatePlural('Change', 'Changes', 2));
    $viewData->addTranslation('tickets_link_elements', $translator->translatePlural('Ticket on linked items', 'Tickets on linked items', 1));
    $viewData->addTranslation('problems_link_elements', $translator->translate('Problems on linked items'));
    $viewData->addTranslation('changes_link_elements', $translator->translate('Changes on linked items'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation('associated_items', $translator->translatePlural('Associated element', 'Associated elements', 2));
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_ticket_found', $translator->translate('No ticket found.'));
    $viewData->addTranslation('no_problem_found', $translator->translate('No problem found.'));
    $viewData->addTranslation('no_change_found', $translator->translate('No change found.'));

    return $view->render($response, 'subitem/itil.html.twig', (array)$viewData);
  }


  public static function getStatusArray()
  {
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => $translator->translate('Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => $translator->translate('Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => $translator->translate('Closed'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      7 => [
        'title' => $translator->translate('status' . "\004" . 'Accepted'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
     8 => [
        'title' => $translator->translate('Review'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      9 => [
        'title' => $translator->translate('Evaluation'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      10 => [
        'title' => $translator->translatePlural('Approval', 'Approvals', 1),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      11 => [
        'title' => $translator->translate('change' . "\004" . 'Testing'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      12 => [
        'title' => $translator->translate('Qualification'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
    ];
  }

  public static function getPriorityArray()
  {
    global $translator;
    return [
      6 => [
        'title' => $translator->translate('priority' . "\004" . 'Major'),
        'color' => 'gsitmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => $translator->translate('priority' . "\004" . 'Very high'),
        'color' => 'gsitveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => $translator->translate('priority' . "\004" . 'High'),
        'color' => 'gsithigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => $translator->translate('priority' . "\004" . 'Medium'),
        'color' => 'gsitmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => $translator->translate('priority' . "\004" . 'Low'),
        'color' => 'gsitlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => $translator->translate('priority' . "\004" . 'Very low'),
        'color' => 'gsitverylow',
        'icon'  => 'volume off',
      ],
    ];
  }
}
