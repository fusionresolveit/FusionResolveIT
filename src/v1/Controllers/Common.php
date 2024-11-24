<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Common
{
  protected $model = '';
  protected $rootUrl2 = '';
  protected $itilchoose = '';
  protected $associateditems_model = '';
  protected $associateditems_model_id = '';
  protected $costchoose = '';

  protected $MINUTE_TIMESTAMP = '60';
  protected $HOUR_TIMESTAMP = '3600';
  protected $DAY_TIMESTAMP = '86400';
  protected $WEEK_TIMESTAMP = '604800';
  protected $MONTH_TIMESTAMP = '2592000';

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
    if (!$this->canRightRead())
    {
      throw new \Exception('Unauthorized access', 401);
    }

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

    $myItem = $item->find($args['id']);

    if (!\App\v1\Controllers\Profile::canRightReadItem($myItem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

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
    \App\v1\Controllers\Toolbox::addSessionMessage('The item has been updated successfully');

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
  }

  protected function commonShowITILItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    // Load the item
    // $item->loadId($args['id']);
    $myItem = $item->find($args['id']);

    if (!\App\v1\Controllers\Profile::canRightReadItem($myItem))
    {
      throw new \Exception('Unauthorized access', 401);
    }
    $title = '';

    $fields = $item->getFormData($myItem);
    foreach ($fields as $field)
    {
      if ($field['name'] == 'name')
      {
        $title = $field['value'];
        break;
      }
    }

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($this->getUrlWithoutQuery($request)));
    $viewData->addHeaderColor($myItem->getColor());

    $viewData->addData('fields', $fields);
    $viewData->addData('feeds', $item->getFeeds($args['id']));
    $viewData->addData('title', $title);

    if (is_null($myItem->content))
    {
      $viewData->addData('content', null);
    } else {
      $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    }
    $ctrlFollowup = new \App\v1\Controllers\Followup();
    $viewData->addData('fullFollowup', $ctrlFollowup->canRightReadPrivateItem());

    $canAddFollowup = true;
    $canAddSolution = true;
    if ($myItem->canOnlyReadItem())
    {
      $canAddFollowup = false;
      $canAddSolution = false;
    }
    $viewData->addData('canAddFollowup', $canAddFollowup);
    $viewData->addData('canAddSolution', $canAddSolution);

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

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  public function showNewItem(Request $request, Response $response, $args): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $item = new $this->model();

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addData('fields', $item->getFormData($item));
    $viewData->addData('content', '');

    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }

  public function commonShowITILNewItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

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

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  public function showSubHistory(Request $request, Response $response, $args)
  {
    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->find($args['id']);

    $logs = [];
    if ($myItem != null)
    {
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
      if (!$this->canRightCreate($this->model))
      {
        throw new \Exception('Unauthorized access', 401);
      }
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
      $aData = $this->filterFieldsAllowedToWrite($this->model, (array) $data);

      $item = $this->model::create($aData);
    } else {
      // update
      if (!$this->canRightCreate($this->model))
      {
        throw new \Exception('Unauthorized access', 401);
      }

      $item = $this->model::find($id);
      if (!\App\v1\Controllers\Profile::canRightReadItem($item))
      {
        throw new \Exception('Unauthorized access', 401);
      }

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
      $aData = $this->filterFieldsAllowedToWrite($this->model, $aData);

      $item->update($aData);
    }

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
        if (!property_exists($data, $key))
        {
          continue;
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
          // $item->$key()->attach($groupId, $pivot);
        }
      }
    }
    if (is_null($id))
    {
      \App\v1\Controllers\Toolbox::addSessionMessage('The item has been created successfully');
    } else {
      \App\v1\Controllers\Toolbox::addSessionMessage('The item has been updated successfully');
    }

    // notification
    if (is_null($id))
    {
      \App\v1\Controllers\Notification::prepareNotification($item, 'new');
    } else {
      \App\v1\Controllers\Notification::prepareNotification($item, 'update');
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

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('dns_name', $translator->translate('DNS name'));
    $viewData->addTranslation('dns_suffix', $translator->translate('DNS suffix'));
    $viewData->addTranslation('created_at', $translator->translate('Creation date'));
    $viewData->addTranslation('date_expiration', $translator->translate('Expiration date'));
    $viewData->addTranslation('status', $translator->translate('Status'));

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
    $domainitems = $item3->where(['item_id' => $args['id'], 'item_type' => $computermodelclass])->get();

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
        foreach ($domainitems as $domainitem)
        {
          if ($domainitem->domain != null)
          {
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

  private function generateLinkContents($link, $item, $replaceByBr = false)
  {
    $new_link = $link;
    if ($replaceByBr === true)
    {
      $new_link = str_ireplace("\n", "<br>", $new_link);
    }
    $matches = [];
    if (preg_match_all('/\[FIELD:(\w+)\]/', $new_link, $matches))
    {
      foreach ($matches[1] as $key => $field)
      {
        $new_link = self::checkAndReplaceProperty($item, $field, $matches[0][$key], $new_link, $replaceByBr);
      }
    }

    if (strstr($new_link, "[ID]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'id', "[ID]", $new_link, $replaceByBr);
    }
    if (strstr($link, "[NAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'name', "[NAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[SERIAL]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'serial', "[SERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[OTHERSERIAL]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'otherserial', "[OTHERSERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATIONID]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'location_id', "[LOCATIONID]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATION]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'location', "[LOCATION]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[DOMAIN]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'domains', "[DOMAIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[NETWORK]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'network', "[NETWORK]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[REALNAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'realname', "[REALNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[FIRSTNAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'firstname', "[FIRSTNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOGIN]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'login', "[LOGIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[USER]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'users', "[USER]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[GROUP]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'groups', "[GROUP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[IP]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'ips', "[IP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[MAC]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'macs', "[MAC]", $new_link, $replaceByBr);
    }

    return $new_link;
  }

  private function checkAndReplaceProperty($item, $field, $strToReplace, $new_link, $replaceByBr = false)
  {
    $ret = $new_link;

    if (array_key_exists($field, $item))
    {
      if (is_array($item[$field]))
      {
        $tmp = '';
        foreach ($item[$field] as $val)
        {
          if ($tmp != '')
          {
            $tmp = $tmp  . "\n";
          }
          $tmp = $tmp . $val;
        }
        $ret = str_replace($strToReplace, $tmp, $ret);
      } else {
        $ret = str_replace($strToReplace, $item[$field], $ret);
      }
      if ($replaceByBr === true)
      {
        $ret = str_ireplace("\n", "<br>", $ret);
      }
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
    if ($this->rootUrl2 != '')
    {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myKnowbaseitems = [];
    foreach ($myItem->knowbaseitems as $knowbaseitem)
    {
      $url = '';
      if ($rootUrl2 != '')
      {
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
      if ($duration == 0)
      {
        $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', 1), $duration);
      }
      if ($duration != 0)
      {
        $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', $duration), $duration);
      }

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
    $viewData->addTranslation('number', $translator->translate('phone' . "\004" . 'Number'));
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

    $tickets = [];
    foreach ($myItem->tickets as $ticket)
    {
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
          $requesters[] = ['name' => $requestergroup->completename];
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
          $technicians[] = ['name' => $techniciangroup->completename];
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
          $requesters[] = ['name' => $requestergroup->completename];
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
          $technicians[] = ['name' => $techniciangroup->completename];
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
          $requesters[] = ['name' => $requestergroup->completename];
        }
      }
      $technicians = [];
      if ($change->technician != null) {
        foreach ($change->technician as $technician) {
          $technicians[] = ['name' => $technician->name];
        }
      }
      if ($change->techniciangroup != null) {
        foreach ($change->techniciangroup as $techniciangroup) {
          $technicians[] = ['name' => $techniciangroup->completename];
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
    $viewData->addTranslation(
      'tickets_link_elements',
      $translator->translatePlural('Ticket on linked items', 'Tickets on linked items', 1)
    );
    $viewData->addTranslation('problems_link_elements', $translator->translate('Problems on linked items'));
    $viewData->addTranslation('changes_link_elements', $translator->translate('Changes on linked items'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
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

  public function showSubComponents(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with(
      'memories',
      'firmwares',
      'processors',
      'harddrives',
      'batteries',
      'soundcards',
      'controllers',
      'powersupplies',
      'sensors',
      'devicepcis',
      'devicegenerics',
      'devicenetworkcards',
      'devicesimcards',
      'devicemotherboards',
      'devicecases',
      'devicegraphiccards',
      'devicedrives'
    )->find($args['id']);


    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/components');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '')
    {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myMemories = [];
    foreach ($myItem->memories as $memory)
    {
      $loc = \App\Models\Location::find($memory->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($memory->manufacturer !== null)
      {
        $manufacturer = $memory->manufacturer->name;
      }

      $myMemories[] = [
        'name'          => $memory->name,
        'manufacturer'  => $manufacturer,
        'type'          => $memory->type->name,
        'frequence'     => $memory->frequence,
        'size'          => $memory->pivot->size,
        'serial'        => $memory->pivot->serial,
        'busID'         => $memory->pivot->busID,
        'location'      => $location,
        'color'         => 'red',
      ];
    }

    $myFirmwares = [];
    foreach ($myItem->firmwares as $firmware)
    {
      $loc = \App\Models\Location::find($firmware->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($firmware->manufacturer !== null)
      {
        $manufacturer = $firmware->manufacturer->name;
      }

      $type = '';
      if ($firmware->type !== null)
      {
        $type = $firmware->type->name;
      }

      $myFirmwares[] = [
        'name'          => $firmware->name,
        'manufacturer'  => $manufacturer,
        'type'          => $type,
        'version'       => $firmware->version,
        'date'          => $firmware->date,
        'location'      => $location,
        'color'         => 'orange',
      ];
    }

    $myProcessors = [];
    foreach ($myItem->processors as $processor)
    {
      $loc = \App\Models\Location::find($processor->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($processor->manufacturer !== null)
      {
        $manufacturer = $processor->manufacturer->name;
      }

      $myProcessors[] = [
        'name'          => $processor->name,
        'manufacturer'  => $manufacturer,
        'frequency'     => $processor->pivot->frequency,
        'nbcores'       => $processor->pivot->nbcores,
        'nbthreads'     => $processor->pivot->nbthreads,
        'location'      => $location,
        'color'         => 'olive',
      ];
    }

    $myHarddrives = [];
    foreach ($myItem->harddrives as $harddrive)
    {
      $loc = \App\Models\Location::find($harddrive->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($harddrive->manufacturer !== null)
      {
        $manufacturer = $harddrive->manufacturer->name;
      }

      $interface = '';
      if ($harddrive->interface !== null)
      {
        $interface = $harddrive->interface->name;
      }

      $myHarddrives[] = [
        'name'            => $harddrive->name,
        'manufacturer'    => $manufacturer,
        'rpm'             => $harddrive->rpm,
        'cache'           => $harddrive->cache,
        'interface'       => $interface,
        'capacity'        => $harddrive->pivot->capacity,
        'serial'          => $harddrive->pivot->serial,
        'location'        => $location,
        'color'           => 'teal',
      ];
    }

    $myBatteries = [];
    foreach ($myItem->batteries as $battery)
    {
      $loc = \App\Models\Location::find($battery->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($battery->manufacturer !== null)
      {
        $manufacturer = $battery->manufacturer->name;
      }

      $type = '';
      if ($battery->type !== null)
      {
        $type = $battery->type->name;
      }

      $myBatteries[] = [
        'name'                => $battery->name,
        'manufacturer'        => $manufacturer,
        'type'                => $type,
        'voltage'             => $battery->voltage,
        'capacity'            => $battery->capacity,
        'serial'              => $battery->pivot->serial,
        'manufacturing_date'  => $battery->pivot->manufacturing_date,
        'location'            => $location,
        'color'               => 'blue',
      ];
    }

    $mySoundcards = [];
    foreach ($myItem->soundcards as $soundcard)
    {
      $loc = \App\Models\Location::find($soundcard->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($soundcard->manufacturer !== null)
      {
        $manufacturer = $soundcard->manufacturer->name;
      }

      $mySoundcards[] = [
        'name'            => $soundcard->name,
        'manufacturer'    => $manufacturer,
        'type'            => $soundcard->type,
        'location'        => $location,
        'color'           => 'purple',
      ];
    }

    $myControllers = [];
    foreach ($myItem->controllers as $controller)
    {
      $loc = \App\Models\Location::find($controller->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($controller->manufacturer !== null)
      {
        $manufacturer = $controller->manufacturer->name;
      }

      $interface = '';
      if ($controller->interface !== null)
      {
        $interface = $controller->interface->name;
      }

      $myControllers[] = [
        'name'            => $controller->name,
        'manufacturer'    => $manufacturer,
        'interface'       => $interface,
        'location'        => $location,
        'color'           => 'brown',
      ];
    }

    $myPowerSupplies = [];
    foreach ($myItem->powersupplies as $powersupply)
    {
      $loc = \App\Models\Location::find($powersupply->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $documents = [];
      if ($powersupply->documents !== null)
      {
        foreach ($powersupply->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $myPowerSupplies[] = [
        'name'          => $powersupply->name,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'purple',
      ];
    }

    $mySensors = [];
    foreach ($myItem->sensors as $sensor)
    {
      $loc = \App\Models\Location::find($sensor->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($sensor->manufacturer !== null)
      {
        $manufacturer = $sensor->manufacturer->name;
      }

      $documents = [];
      if ($sensor->documents !== null)
      {
        foreach ($sensor->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $mySensors[] = [
        'name'          => $sensor->name,
        'manufacturer'  => $manufacturer,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'purple',
      ];
    }

    $myDevicepcis = [];
    foreach ($myItem->devicepcis as $devicepci)
    {
      $loc = \App\Models\Location::find($devicepci->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $documents = [];
      if ($devicepci->documents !== null)
      {
        foreach ($devicepci->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $myDevicepcis[] = [
        'name'          => $devicepci->name,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'blue',
      ];
    }

    $myDevicegenerics = [];
    foreach ($myItem->devicegenerics as $devicegeneric)
    {
      $loc = \App\Models\Location::find($devicegeneric->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $documents = [];
      if ($devicegeneric->documents !== null)
      {
        foreach ($devicegeneric->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $myDevicegenerics[] = [
        'name'          => $devicegeneric->name,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'teal',
      ];
    }

    $myDevicenetworkcards = [];
    foreach ($myItem->devicenetworkcards as $devicenetworkcard)
    {
      $loc = \App\Models\Location::find($devicenetworkcard->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $documents = [];
      if ($devicenetworkcard->documents !== null)
      {
        foreach ($devicenetworkcard->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $myDevicenetworkcards[] = [
        'name'          => $devicenetworkcard->name,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'red',
      ];
    }

    $myDevicesimcards = [];
    foreach ($myItem->devicesimcards as $devicesimcard)
    {
      $loc = \App\Models\Location::find($devicesimcard->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $documents = [];
      if ($devicesimcard->documents !== null)
      {
        foreach ($devicesimcard->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $mac_address = ''; # TODO

      $myDevicesimcards[] = [
        'name'          => $devicesimcard->name,
        'location'      => $location,
        'documents'     => $documents,
        'mac_address'   => $mac_address,
        'color'         => 'orange',
      ];
    }

    $myDevicemotherboards = [];
    foreach ($myItem->devicemotherboards as $devicemotherboard)
    {
      $loc = \App\Models\Location::find($devicemotherboard->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $documents = [];
      if ($devicemotherboard->documents !== null)
      {
        foreach ($devicemotherboard->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $myDevicemotherboards[] = [
        'name'          => $devicemotherboard->name,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'olive',
      ];
    }

    $myDevicecases = [];
    foreach ($myItem->devicecases as $devicecase)
    {
      $loc = \App\Models\Location::find($devicecase->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($devicecase->manufacturer !== null)
      {
        $manufacturer = $devicecase->manufacturer->name;
      }

      $documents = [];
      if ($devicecase->documents !== null)
      {
        foreach ($devicecase->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $myDevicecases[] = [
        'name'          => $devicecase->name,
        'manufacturer'  => $manufacturer,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'olive',
      ];
    }

    $myDevicegraphiccards = [];
    foreach ($myItem->devicegraphiccards as $devicegraphiccard)
    {
      $loc = \App\Models\Location::find($devicegraphiccard->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($devicegraphiccard->manufacturer !== null)
      {
        $manufacturer = $devicegraphiccard->manufacturer->name;
      }

      $interface = '';
      if ($devicegraphiccard->interface !== null)
      {
        $interface = $devicegraphiccard->interface->name;
      }

      $documents = [];
      if ($devicegraphiccard->documents !== null)
      {
        foreach ($devicegraphiccard->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $myDevicegraphiccards[] = [
        'name'          => $devicegraphiccard->name,
        'manufacturer'  => $manufacturer,
        'interface'     => $interface,
        'chipset'       => $devicegraphiccard->chipset,
        'memory'        => $devicegraphiccard->emory,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'brown',
      ];
    }

    $myDevicedrives = [];
    foreach ($myItem->devicedrives as $devicedrive)
    {
      $loc = \App\Models\Location::find($devicedrive->pivot->location_id);

      $location = '';
      if ($loc !== null)
      {
        $location = $loc->name;
      }

      $manufacturer = '';
      if ($devicedrive->manufacturer !== null)
      {
        $manufacturer = $devicedrive->manufacturer->name;
      }

      $interface = '';
      if ($devicedrive->interface !== null)
      {
        $interface = $devicedrive->interface->name;
      }

      $documents = [];
      if ($devicedrive->documents !== null)
      {
        foreach ($devicedrive->documents as $document)
        {
          $url = '';
          if ($rootUrl2 != '')
          {
            $url = $rootUrl2 . "/documents/" . $document->id;
          }
          $documents[$document->id] = [
            'name' => $document->name,
            'url' => $url,
          ];
        }
      }

      $myDevicedrives[] = [
        'name'          => $devicedrive->name,
        'manufacturer'  => $manufacturer,
        'write'         => $devicedrive->is_writer,
        'speed'         => $devicedrive->speed,
        'interface'     => $interface,
        'location'      => $location,
        'documents'     => $documents,
        'color'         => 'teal',
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('memories', $myMemories);
    $viewData->addData('firmwares', $myFirmwares);
    $viewData->addData('processors', $myProcessors);
    $viewData->addData('harddrives', $myHarddrives);
    $viewData->addData('batteries', $myBatteries);
    $viewData->addData('soundcards', $mySoundcards);
    $viewData->addData('controllers', $myControllers);
    $viewData->addData('powersupplies', $myPowerSupplies);
    $viewData->addData('sensors', $mySensors);
    $viewData->addData('devicepcis', $myDevicepcis);
    $viewData->addData('devicegenerics', $myDevicegenerics);
    $viewData->addData('devicenetworkcards', $myDevicenetworkcards);
    $viewData->addData('devicesimcards', $myDevicesimcards);
    $viewData->addData('devicemotherboards', $myDevicemotherboards);
    $viewData->addData('devicecases', $myDevicecases);
    $viewData->addData('devicegraphiccards', $myDevicegraphiccards);
    $viewData->addData('devicedrives', $myDevicedrives);

    $viewData->addTranslation('memory', $translator->translatePlural('Memory', 'Memories', 1));
    $viewData->addTranslation('manufacturer', $translator->translatePlural('Manufacturer', 'Manufacturers', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('frequence', $translator->translate('Frequency'));
    $viewData->addTranslation(
      'size',
      sprintf('%1$s (%2$s)', $translator->translate('Size'), $translator->translate('Mio'))
    );
    $viewData->addTranslation('serial', $translator->translate('Serial number'));
    $viewData->addTranslation('location', $translator->translatePlural('Location', 'Locations', 1));
    $viewData->addTranslation('busID', $translator->translate('Position of the device on its bus'));
    $viewData->addTranslation('firmware', $translator->translatePlural('Firmware', 'Firmware', 1));
    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));
    $viewData->addTranslation('install_date', $translator->translate('Installation date'));
    $viewData->addTranslation('processor', $translator->translatePlural('Processor', 'Processors', 1));
    $viewData->addTranslation(
      'frequence_mhz',
      sprintf('%1$s (%2$s)', $translator->translate('Frequency'), $translator->translate('MHz'))
    );
    $viewData->addTranslation('nbcores', $translator->translate('Number of cores'));
    $viewData->addTranslation('nbthreads', $translator->translate('Number of threads'));
    $viewData->addTranslation('harddrive', $translator->translatePlural('Hard drive', 'Hard drives', 1));
    $viewData->addTranslation('rpm', $translator->translate('Rpm'));
    $viewData->addTranslation('cache', $translator->translate('Cache'));
    $viewData->addTranslation('interface', $translator->translate('Interface'));
    $viewData->addTranslation(
      'capacity',
      sprintf('%1$s (%2$s)', $translator->translate('Capacity'), $translator->translate('Mio'))
    );
    $viewData->addTranslation('battery', $translator->translatePlural('Battery', 'Batteries', 1));
    $viewData->addTranslation(
      'voltage_mv',
      sprintf('%1$s (%2$s)', $translator->translate('Voltage'), $translator->translate('mV'))
    );
    $viewData->addTranslation(
      'capacity_mwh',
      sprintf('%1$s (%2$s)', $translator->translate('Capacity'), $translator->translate('mWh'))
    );
    $viewData->addTranslation('manufacturing_date', $translator->translate('Manufacturing date'));
    $viewData->addTranslation('soundcard', $translator->translatePlural('Soundcard', 'Soundcards', 1));
    $viewData->addTranslation('controller', $translator->translatePlural('Controller', 'Controllers', 1));
    $viewData->addTranslation('documents', $translator->translatePlural('Document', 'Documents', 2));
    $viewData->addTranslation('mac_address', $translator->translate('MAC address'));
    $viewData->addTranslation('powersupply', $translator->translatePlural('Power supply', 'Power supplies', 1));
    $viewData->addTranslation('sensor', $translator->translatePlural('Sensor', 'Sensors', 1));
    $viewData->addTranslation('devicepci', $translator->translatePlural('PCI device', 'PCI devices', 1));
    $viewData->addTranslation('devicegeneric', $translator->translatePlural('Generic device', 'Generic devices', 1));
    $viewData->addTranslation('devicenetworkcard', $translator->translatePlural('Network card', 'Network cards', 1));
    $viewData->addTranslation('devicesimcard', $translator->translatePlural('Simcard', 'Simcards', 1));
    $viewData->addTranslation('devicemotherboard', $translator->translatePlural('System board', 'System boards', 1));
    $viewData->addTranslation('devicecase', $translator->translatePlural('Case', 'Cases', 1));
    $viewData->addTranslation('devicegraphiccard', $translator->translatePlural('Graphics card', 'Graphics cards', 1));
    $viewData->addTranslation('devicedrive', $translator->translatePlural('Drive', 'Drives', 1));
    $viewData->addTranslation(
      'memory_mio',
      sprintf('%1$s (%2$s)', $translator->translatePlural('Memory', 'Memories', 1), $translator->translate('Mio'))
    );
    $viewData->addTranslation('chipset', $translator->translate('Chipset'));
    $viewData->addTranslation('write', $translator->translate('Write'));
    $viewData->addTranslation('speed', $translator->translate('Speed'));

    return $view->render($response, 'subitem/components.html.twig', (array)$viewData);
  }

  public function showSubVolumes(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('volumes')->find($args['id']);

    $myVolumes = [];
    foreach ($myItem->volumes as $volume)
    {
      if ($volume->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
      }

      $filesystem = '';
      if ($volume->filesystem !== null)
      {
        $filesystem = $volume->filesystem->name;
      }


      $usedpercent = 100;
      if ($volume->totalsize > 0)
      {
        $usedpercent = 100 - round(($volume->freesize / $volume->totalsize) * 100);
      }

      $encryption_status_val = '';
      if ($volume->encryption_status == 0)
      {
        $encryption_status_val = $translator->translate('Non chiffré');
      }
      if ($volume->encryption_status == 1)
      {
        $encryption_status_val = $translator->translate('Chiffré');
      }
      if ($volume->encryption_status == 2)
      {
        $encryption_status_val = $translator->translate('Partiellement chiffré');
      }

      $myVolumes[] = [
        'name'                      => $volume->name,
        'auto'                      => $volume->is_dynamic,
        'auto_val'                  => $auto_val,
        'device'                    => $volume->device,
        'mountpoint'                => $volume->mountpoint,
        'filesystem'                => $filesystem,
        'totalsize'                 => $volume->totalsize,
        'freesize'                  => $volume->freesize,
        'usedpercent'               => $usedpercent,
        'encryption_status'         => $volume->encryption_status,
        'encryption_status_val'     => $encryption_status_val,
        'encryption_tool'           => $volume->encryption_tool,
        'encryption_algorithm'      => $volume->encryption_algorithm,
        'encryption_type'           => $volume->encryption_type,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/volumes');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('volumes', $myVolumes);

    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('device', $translator->translate('Partition'));
    $viewData->addTranslation('mountpoint', $translator->translate('Mount point'));
    $viewData->addTranslation('filesystem', $translator->translatePlural('File system', 'File systems', 1));
    $viewData->addTranslation('totalsize', $translator->translate('Global size'));
    $viewData->addTranslation('freesize', $translator->translate('Free size'));
    $viewData->addTranslation('encryption', $translator->translate('Encryption'));
    $viewData->addTranslation('encryption_algorithm', $translator->translate('Encryption algorithm'));
    $viewData->addTranslation('encryption_tool', $translator->translate('Encryption tool'));
    $viewData->addTranslation('encryption_type', $translator->translate('Encryption type'));
    $viewData->addTranslation('usedpercent', 'Pourcentage utilisé');

    return $view->render($response, 'subitem/volumes.html.twig', (array)$viewData);
  }


  public function showSubItilTicketsCreated(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);


    $item2 = new \App\Models\Ticket();
    $myItem2 = $item2::with('requester', 'requestergroup', 'technician', 'techniciangroup')->get();


    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/tickets');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '')
    {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $tickets = [];
    foreach ($myItem2 as $ticket)
    {
      $add_to_tab = false;

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
          if ($this->itilchoose == 'users') {
            if ($requester->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $requesters[] = ['name' => $requester->name];
        }
      }
      if ($ticket->requestergroup != null) {
        foreach ($ticket->requestergroup as $requestergroup) {
          if ($this->itilchoose == 'groups') {
            if ($requestergroup->id == $args['id']) {
              $add_to_tab = true;
            }
          }
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


      if ($add_to_tab) {
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
    }

    // tri de la + récente à la + ancienne
    usort($tickets, function ($a, $b)
    {
      return $a['last_update'] < $b['last_update'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);

    $viewData->addTranslation('tickets', $translator->translatePlural('Ticket', 'Tickets', 2));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_ticket_found', $translator->translate('No ticket found.'));

    return $view->render($response, 'subitem/itiltickets.html.twig', (array)$viewData);
  }

  public function showSubItilProblems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);


    $item2 = new \App\Models\Problem();
    $myItem2 = $item2::with('requester', 'requestergroup', 'technician', 'techniciangroup')->get();


    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/problems');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $problems = [];
    foreach ($myItem2 as $problem)
    {
      $add_to_tab = false;

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
          if ($this->itilchoose == 'users') {
            if ($requester->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $requesters[] = ['name' => $requester->name];
        }
      }
      if ($problem->requestergroup != null) {
        foreach ($problem->requestergroup as $requestergroup) {
          if ($this->itilchoose == 'groups') {
            if ($requestergroup->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $requesters[] = ['name' => $requestergroup->name];
        }
      }
      $technicians = [];
      if ($problem->technician != null) {
        foreach ($problem->technician as $technician) {
          if ($this->itilchoose == 'users') {
            if ($technician->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $technicians[] = ['name' => $technician->name];
        }
      }
      if ($problem->techniciangroup != null) {
        foreach ($problem->techniciangroup as $techniciangroup) {
          if ($this->itilchoose == 'groups') {
            if ($techniciangroup->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $technicians[] = ['name' => $techniciangroup->name];
        }
      }
      $category = '';
      if ($problem->category != null) {
        $category = $problem->category->name;
      }
      $planification = 0; // TODO


      if ($add_to_tab) {
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
    }

    // tri de la + récente à la + ancienne
    usort($problems, function ($a, $b)
    {
      return $a['last_update'] < $b['last_update'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('problems', $problems);

    $viewData->addTranslation('problems', $translator->translatePlural('Problem', 'Problems', 2));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_problem_found', $translator->translate('No problem found.'));

    return $view->render($response, 'subitem/itilproblems.html.twig', (array)$viewData);
  }

  public function showSubItilChanges(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);


    $item2 = new \App\Models\Change();
    $myItem2 = $item2::with('requester', 'requestergroup', 'technician', 'techniciangroup')->get();


    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/changes');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $changes = [];
    foreach ($myItem2 as $change)
    {
      $add_to_tab = false;

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
          if ($this->itilchoose == 'users') {
            if ($requester->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $requesters[] = ['name' => $requester->name];
        }
      }
      if ($change->requestergroup != null) {
        foreach ($change->requestergroup as $requestergroup) {
          if ($this->itilchoose == 'groups') {
            if ($requestergroup->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $requesters[] = ['name' => $requestergroup->name];
        }
      }
      $technicians = [];
      if ($change->technician != null) {
        foreach ($change->technician as $technician) {
          if ($this->itilchoose == 'users') {
            if ($technician->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $technicians[] = ['name' => $technician->name];
        }
      }
      if ($change->techniciangroup != null) {
        foreach ($change->techniciangroup as $techniciangroup) {
          if ($this->itilchoose == 'groups') {
            if ($techniciangroup->id == $args['id']) {
              $add_to_tab = true;
            }
          }
          $technicians[] = ['name' => $techniciangroup->name];
        }
      }
      $category = '';
      if ($change->itilcategorie != null) {
        $category = $change->itilcategorie->name;
      }
      $planification = 0; // TODO


      if ($add_to_tab) {
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
    }

    // tri de la + récente à la + ancienne
    usort($changes, function ($a, $b)
    {
      return $a['last_update'] < $b['last_update'];
    });


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('changes', $changes);

    $viewData->addTranslation('changes', $translator->translatePlural('Change', 'Changes', 2));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_change_found', $translator->translate('No change found.'));

    return $view->render($response, 'subitem/itilchanges.html.twig', (array)$viewData);
  }

  public function showSubConnections(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('connections')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/connections');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myConnections = [];
    foreach ($myItem->connections as $connection)
    {
      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/computers/" . $connection->id;
      }
      $entity = '';
      if ($connection->entity != null) {
        $entity = $connection->entity->name;
      }

      if ($connection->pivot->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
      }

      $myConnections[] = [
        'name'                 => $connection->name,
        'url'                  => $url,
        'auto'                 => $connection->pivot->is_dynamic,
        'auto_val'             => $auto_val,
        'entity'               => $entity,
        'serial_number'        => $connection->serial,
        'inventaire_number'    => $connection->otherserial,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('connections', $myConnections);
    $viewData->addData('show', 'default');

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation('no_connection_found', $translator->translate('Not connected.'));

    return $view->render($response, 'subitem/connections.html.twig', (array)$viewData);
  }

  public function showSubAssociatedItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->associateditems_model();
    $myItem2 = $item2::where($this->associateditems_model_id, $args['id'])->get();

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/associateditems');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myAssociatedItems = [];
    foreach ($myItem2 as $associateditem)
    {
      $item3 = new $associateditem->item_type();
      $myItem3 = $item3->find($associateditem->item_id);

      if ($myItem3 != null) {
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
        $entity = '';
        if ($myItem3->entity != null) {
          $entity = $myItem3->entity->name;
        }
        $type = $item3->getTitle();

        $serial_number = $myItem3->serial;
        $inventaire_number = $myItem3->otherserial;

        $myAssociatedItems[] = [
          'type'                 => $type,
          'name'                 => $name,
          'url'                  => $url,
          'entity'               => $entity,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
        ];
      }
    }

    // tri ordre alpha
    usort($myAssociatedItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });
    usort($myAssociatedItems, function ($a, $b)
    {
      return strtolower($a['type']) > strtolower($b['type']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('associateditems', $myAssociatedItems);
    $viewData->addData('show', 'computer');

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));

    return $view->render($response, 'subitem/associateditems.html.twig', (array)$viewData);
  }


  protected function canRightRead()
  {
    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->read)
        {
          return true;
        }
      }
    }
    if ($profileright->read || $profileright->readmyitems || $profileright->readmygroupitems)
    {
      return true;
    }
    return false;
  }

  protected function canRightCreate()
  {
    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->write)
        {
          return true;
        }
      }
    }
    if ($profileright->create)
    {
      return true;
    }
    return false;
  }


  protected function canRightUpdate()
  {
    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->write)
        {
          return true;
        }
      }
    }
    if ($profileright->update)
    {
      return true;
    }
    return false;
  }

  public function canRightReadPrivateItem()
  {
    return false;
  }

  private function filterFieldsAllowedToWrite($model, $fields)
  {
    $item = new $model();
    $definitions = $item->getDefinitions();
    $defIds = [];
    foreach ($definitions as $definition)
    {
      $defIds[$definition['id']] = $definition['name'];
    }

    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        if (!$custom->write)
        {
          if (isset($fields[$defIds[$custom->definitionfield_id]]))
          {
            unset($fields[$defIds[$custom->definitionfield_id]]);
          }
        }
      }
    }
    return $fields;
  }

  public function showSubCosts(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('costs')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/costs');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myCosts = [];
    $myTicketCosts = [];
    $total_cost = 0;
    $total_actiontime = 0;
    $total_cost_time = 0;
    $total_cost_fixed = 0;
    $total_cost_material = 0;
    $ticket_costs_total_cost = 0;
    $ticket_costs_total_actiontime = 0;
    $ticket_costs_total_cost_time = 0;
    $ticket_costs_total_cost_fixed = 0;
    $ticket_costs_total_cost_material = 0;
    $total_costs = 0;
    foreach ($myItem->costs as $current_cost)
    {
      $budget = '';
      $budget_url = '';
      if ($current_cost->budget != null)
      {
        $budget = $current_cost->budget->name;

        if ($rootUrl2 != '') {
          $budget_url = $rootUrl2 . "/budgets/" . $current_cost->budget->id;
        }
      }

      $cost = 0;
      $actiontime = 0;
      $cost_time = 0;
      $cost_fixed = 0;
      $cost_material = 0;
      if ($this->costchoose == 'ticket') {
        if (isset($current_cost->actiontime)) {
          $actiontime = $current_cost->actiontime;

          $total_actiontime = $total_actiontime + $actiontime;
        }
        if (isset($current_cost->cost_time)) {
          $cost_time = $current_cost->cost_time;

          $total_cost_time = $total_cost_time + $this->computeCostTime($actiontime, $cost_time);
        }
        if (isset($current_cost->cost_fixed)) {
          $cost_fixed = $current_cost->cost_fixed;

          $total_cost_fixed = $total_cost_fixed + ($cost_fixed);
        }
        if (isset($current_cost->cost_material)) {
          $cost_material = $current_cost->cost_material;

          $total_cost_material = $total_cost_material + ($cost_material);
        }

        $cost = $this->computeTotalCost($actiontime, $cost_time, $cost_fixed, $cost_material);
        $total_cost = $total_cost + ($cost);

      } else {
        if (isset($current_cost->cost)) {
          $cost = $current_cost->cost;

          $total_cost = $total_cost + ($cost);
        }
      }

      $myCosts[$current_cost->id] = [
        'name'               => $current_cost->name,
        'begin_date'         => $current_cost->begin_date,
        'end_date'           => $current_cost->end_date,
        'budget'             => $budget,
        'budget_url'         => $budget_url,
        'cost'               => $this->showCosts($cost),
        'actiontime'         => $this->timestampToString($actiontime, false),
        'cost_time'          => $this->showCosts($cost_time),
        'cost_fixed'         => $this->showCosts($cost_fixed),
        'cost_material'      => $this->showCosts($cost_material),
      ];
    }

    // tri de la + récente à la + ancienne
    usort($myCosts, function ($a, $b)
    {
      return strtolower($a['begin_date']) > strtolower($b['begin_date']);
    });

    if ($this->costchoose == 'project') {
      $item2 = new $this->model();
      $myItem2 = $item2::with('tasks')->find($args['id']);


      foreach ($myItem2->tasks as $current_task) {
        if ($current_task->tickets != null)
        {
          foreach ($current_task->tickets as $current_ticket) {
            $ticket = $current_ticket->name;
            $ticket_url = '';
            if ($rootUrl2 != '') {
              $ticket_url = $rootUrl2 . "/tickets/" . $current_ticket->id;
            }

            if ($current_ticket->costs != null) {
              foreach ($current_ticket->costs as $current_cost) {
                $budget = '';
                $budget_url = '';
                if ($current_cost->budget != null)
                {
                  $budget = $current_cost->budget->name;

                  if ($rootUrl2 != '') {
                    $budget_url = $rootUrl2 . "/budgets/" . $current_cost->budget->id;
                  }
                }

                $cost = 0;
                $actiontime = 0;
                $cost_time = 0;
                $cost_fixed = 0;
                $cost_material = 0;

                if (isset($current_cost->actiontime)) {
                  $actiontime = $current_cost->actiontime;

                  $ticket_costs_total_actiontime = $ticket_costs_total_actiontime + $actiontime;
                }
                if (isset($current_cost->cost_time)) {
                  $cost_time = $current_cost->cost_time;

                  $ticket_costs_total_cost_time = $ticket_costs_total_cost_time + $this->computeCostTime($actiontime, $cost_time);
                }
                if (isset($current_cost->cost_fixed)) {
                  $cost_fixed = $current_cost->cost_fixed;

                  $ticket_costs_total_cost_fixed = $ticket_costs_total_cost_fixed + ($cost_fixed);
                }
                if (isset($current_cost->cost_material)) {
                  $cost_material = $current_cost->cost_material;

                  $ticket_costs_total_cost_material = $ticket_costs_total_cost_material + ($cost_material);
                }

                $cost = $this->computeTotalCost($actiontime, $cost_time, $cost_fixed, $cost_material);
                $ticket_costs_total_cost = $ticket_costs_total_cost + ($cost);


                $myTicketCosts[$current_cost->id] = [
                  'ticket'             => $ticket,
                  'ticket_url'         => $ticket_url,
                  'name'               => $current_cost->name,
                  'begin_date'         => $current_cost->begin_date,
                  'end_date'           => $current_cost->end_date,
                  'budget'             => $budget,
                  'budget_url'         => $budget_url,
                  'cost'               => $this->showCosts($cost),
                  'actiontime'         => $this->timestampToString($actiontime, false),
                  'cost_time'          => $this->showCosts($cost_time),
                  'cost_fixed'         => $this->showCosts($cost_fixed),
                  'cost_material'      => $this->showCosts($cost_material),
                ];
              }
            }
          }
        }
      }

      // tri de la + récente à la + ancienne
      usort($myTicketCosts, function ($a, $b)
      {
        return strtolower($a['begin_date']) > strtolower($b['begin_date']);
      });

      $total_costs = $total_cost + $ticket_costs_total_cost;
    }


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('costs', $myCosts);
    $viewData->addData('ticket_costs', $myTicketCosts);
    $viewData->addData('total_cost', $this->showCosts($total_cost));
    $viewData->addData('total_actiontime', $this->timestampToString($total_actiontime, false));
    $viewData->addData('total_cost_time', $this->showCosts($total_cost_time));
    $viewData->addData('total_cost_fixed', $this->showCosts($total_cost_fixed));
    $viewData->addData('total_cost_material', $this->showCosts($total_cost_material));
    $viewData->addData('ticket_costs_total_cost', $this->showCosts($ticket_costs_total_cost));
    $viewData->addData('ticket_costs_total_actiontime', $this->timestampToString($ticket_costs_total_actiontime, false));
    $viewData->addData('ticket_costs_total_cost_time', $this->showCosts($ticket_costs_total_cost_time));
    $viewData->addData('ticket_costs_total_cost_fixed', $this->showCosts($ticket_costs_total_cost_fixed));
    $viewData->addData('ticket_costs_total_cost_material', $this->showCosts($ticket_costs_total_cost_material));
    $viewData->addData('total_costs', $this->showCosts($total_costs));
    $viewData->addData('show', $this->costchoose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('begin_date', $translator->translate('Start date'));
    $viewData->addTranslation('end_date', $translator->translate('End date'));
    $viewData->addTranslation('budget', $translator->translatePlural('Budget', 'Budgets', 1));
    $viewData->addTranslation('cost', $translator->translatePlural('Cost', 'Costs', 1));
    $viewData->addTranslation('costs', $translator->translatePlural('Cost', 'Costs', 2));
    $viewData->addTranslation('total_cost', $translator->translate('Total cost'));
    $viewData->addTranslation('total', $translator->translate('Total'));
    $viewData->addTranslation('actiontime', $translator->translate('Duration'));
    $viewData->addTranslation('cost_time', $translator->translate('Time cost'));
    $viewData->addTranslation('cost_fixed', $translator->translate('Fixed cost'));
    $viewData->addTranslation('cost_material', $translator->translate('Material cost'));
    $viewData->addTranslation('ticket_costs', $translator->translatePlural('Ticket cost', 'Ticket costs', 2));
    $viewData->addTranslation('ticket', $translator->translatePlural('Ticket', 'Tickets', 1));

    return $view->render($response, 'subitem/costs.html.twig', (array)$viewData);
  }

  public function timestampToString($time, $display_sec = true, $use_days = true)
  {
    global $translator;

    $time = (float)$time;

    $sign = '';
    if ($time < 0) {
      $sign = '- ';
      $time = abs($time);
    }
    $time = floor($time);

    // Force display seconds if time is null
    if ($time < $this->MINUTE_TIMESTAMP) {
      $display_sec = true;
    }

    $units = $this->getTimestampTimeUnits($time);
    if ($use_days) {
      if ($units['day'] > 0) {
        if ($display_sec) {
          return sprintf($translator->translate('%1$s%2$d days %3$d hours %4$d minutes %5$d seconds'), $sign, $units['day'], $units['hour'], $units['minute'], $units['second']);
        }
        return sprintf($translator->translate('%1$s%2$d days %3$d hours %4$d minutes'), $sign, $units['day'], $units['hour'], $units['minute']);
      }
    } else {
      if ($units['day'] > 0) {
        $units['hour'] += 24*$units['day'];
      }
    }

    if ($units['hour'] > 0) {
      if ($display_sec) {
        return sprintf($translator->translate('%1$s%2$d hours %3$d minutes %4$d seconds'), $sign, $units['hour'], $units['minute'], $units['second']);
      }
      return sprintf($translator->translate('%1$s%2$d hours %3$d minutes'), $sign, $units['hour'], $units['minute']);
    }

    if ($units['minute'] > 0) {
      if ($display_sec) {
        return sprintf($translator->translate('%1$s%2$d minutes %3$d seconds'), $sign, $units['minute'], $units['second']);
      }
      return sprintf($translator->translatePlural('%1$s%2$d minute', '%1$s%2$d minutes', $units['minute']), $sign, $units['minute']);
    }

    if ($display_sec) {
      return sprintf($translator->translatePlural('%1$s%2$s second', '%1$s%2$s seconds', $units['second']), $sign, $units['second']);
    }

    return '';
  }

  public function getTimestampTimeUnits($time)
  {
    $out = [];

    $time          = round(abs($time));
    $out['second'] = 0;
    $out['minute'] = 0;
    $out['hour']   = 0;
    $out['day']    = 0;

    $out['second'] = $time%$this->MINUTE_TIMESTAMP;
    $time         -= $out['second'];

    if ($time > 0) {
      $out['minute'] = ($time%$this->HOUR_TIMESTAMP)/$this->MINUTE_TIMESTAMP;
      $time         -= $out['minute']*$this->MINUTE_TIMESTAMP;

      if ($time > 0) {
        $out['hour'] = ($time%$this->DAY_TIMESTAMP)/$this->HOUR_TIMESTAMP;
        $time       -= $out['hour']*$this->HOUR_TIMESTAMP;

        if ($time > 0) {
          $out['day'] = $time/$this->DAY_TIMESTAMP;
        }
      }
    }
    return $out;
  }

  public function showCosts($cost)
  {
    return sprintf("%.2f", $cost);
  }

  public function computeCostTime($actiontime, $cost_time)
  {
    return $this->showCosts(($actiontime*$cost_time/$this->HOUR_TIMESTAMP));
  }

  public function computeTotalCost($actiontime, $cost_time, $cost_fixed, $cost_material)
  {
    return $this->showCosts(($actiontime*$cost_time/$this->HOUR_TIMESTAMP)+$cost_fixed+$cost_material);
  }
}
