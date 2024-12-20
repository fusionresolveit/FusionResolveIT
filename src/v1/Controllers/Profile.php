<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Profile extends Common
{
  protected $model = '\App\Models\Profile';
  protected $rootUrl2 = '/profiles/';
  protected $choose = 'profiles';
  protected $rigthCategories = [
    'assets' => [
      '\App\Models\Computer',
      '\App\Models\Monitor',
      '\App\Models\Software',
      '\App\Models\Networkequipment',
      '\App\Models\Peripheral',
      '\App\Models\Printer',
      '\App\Models\Phone',
      '\App\Models\Cartridgeitem',
      '\App\Models\Consumableitem',
      '\App\Models\Rack',
      '\App\Models\Enclosure',
      '\App\Models\Pdu',
      '\App\Models\Passivedcequipment',
      '\App\Models\ItemDevicesimcard',
    ],
    'assistance' => [
      '\App\Models\Ticket',
      '\App\Models\Followup',
      '\App\Models\Problem',
      '\App\Models\Change',
      '\App\Models\Ticketrecurrent',
    ],
    'forms' => [
      '\App\Models\Forms\Form',
      '\App\Models\Forms\Section',
      '\App\Models\Forms\Question',
      // '\App\Models\Forms\Answer',
    ],
    'management' => [
      '\App\Models\Softwarelicense',
      '\App\Models\Budget',
      '\App\Models\Supplier',
      '\App\Models\Contact',
      '\App\Models\Contract',
      '\App\Models\Line',
      '\App\Models\Certificate',
      '\App\Models\Document',
      '\App\Models\Datacenter',
      '\App\Models\Dcroom',
      '\App\Models\Cluster',
      '\App\Models\Domain',
      '\App\Models\Appliance',
    ],
    'tools' => [
      '\App\Models\Project',
      '\App\Models\Reminder',
      '\App\Models\Rssfeed',
      '\App\Models\Savedsearch',
      // '\App\Models\Alert',
    ],
    'administration' => [
      '\App\Models\User',
      '\App\Models\Group',
      '\App\Models\Entity',
      '\App\Models\Rules\Ticket',
      '\App\Models\Profile',
      '\App\Models\Queuednotification',
      '\App\Models\Event',
      '\App\Models\Displaypreference',
    ],
    'setup' => [
      '\App\Models\Authsso',
      '\App\Models\Authldap',
      '\App\Models\Notification',
      '\App\Models\Notificationtemplate',
      '\App\Models\Notificationtemplatetranslation',
      '\App\Models\Slm',
      '\App\Models\Fieldunicity',
      '\App\Models\Crontask',
      '\App\Models\Link',
      '\App\Models\Mailcollector',
      '\App\Models\Location',
      '\App\Models\State',
      '\App\Models\Manufacturer',
      '\App\Models\Blacklist',
      '\App\Models\Blacklistedmailcontent',
      '\App\Models\Category',
      '\App\Models\Tickettemplate',
      // '\App\Models\Taskcategory',
      // '\App\Models\Tasktemplate',
      '\App\Models\Solutiontype',
      '\App\Models\Solutiontemplate',
      '\App\Models\Requesttype',
      '\App\Models\Followuptemplate',
      '\App\Models\Projectstate',
      '\App\Models\Projecttype',
      '\App\Models\Projecttask',
      '\App\Models\Projecttasktype',
      '\App\Models\Projecttasktemplate',
      '\App\Models\Planningeventcategory',
      '\App\Models\Planningexternaleventtemplate',
      '\App\Models\Computertype',
      '\App\Models\Networkequipmenttype',
      '\App\Models\Printertype',
      '\App\Models\Monitortype',
      '\App\Models\Peripheraltype',
      '\App\Models\Phonetype',
      '\App\Models\Softwarelicensetype',
      '\App\Models\Cartridgeitemtype',
      '\App\Models\Consumableitemtype',
      '\App\Models\Contracttype',
      '\App\Models\Contacttype',
      '\App\Models\Devicegenerictype',
      '\App\Models\Devicesensortype',
      '\App\Models\Devicememorytype',
      '\App\Models\Suppliertype',
      '\App\Models\Interfacetype',
      '\App\Models\Devicecasetype',
      '\App\Models\Phonepowersupply',
      '\App\Models\Filesystem',
      '\App\Models\Certificatetype',
      '\App\Models\Budgettype',
      '\App\Models\Devicesimcardtype',
      '\App\Models\Linetype',
      '\App\Models\Racktype',
      '\App\Models\Pdutype',
      '\App\Models\Passivedcequipmenttype',
      '\App\Models\Clustertype',
      '\App\Models\Computermodel',
      '\App\Models\Networkequipmentmodel',
      '\App\Models\Printermodel',
      '\App\Models\Monitormodel',
      '\App\Models\Peripheralmodel',
      '\App\Models\Phonemodel',
      '\App\Models\Devicecasemodel',
      '\App\Models\Devicecontrolmodel',
      '\App\Models\Devicedrivemodel',
      '\App\Models\Devicegenericmodel',
      '\App\Models\Devicegraphiccardmodel',
      '\App\Models\Deviceharddrivemodel',
      '\App\Models\Devicememorymodel',
      '\App\Models\Devicemotherboardmodel',
      '\App\Models\Devicenetworkcardmodel',
      '\App\Models\Devicepcimodel',
      '\App\Models\Devicepowersupplymodel',
      '\App\Models\Deviceprocessormodel',
      '\App\Models\Devicesoundcardmodel',
      '\App\Models\Devicesensormodel',
      '\App\Models\Rackmodel',
      '\App\Models\Enclosuremodel',
      '\App\Models\Pdumodel',
      '\App\Models\Passivedcequipmentmodel',
      '\App\Models\Virtualmachinetype',
      '\App\Models\Virtualmachinesystem',
      '\App\Models\Virtualmachinestate',
      '\App\Models\Documentcategory',
      '\App\Models\Documenttype',
      '\App\Models\Businesscriticity',
      '\App\Models\Knowbaseitemcategory',
      '\App\Models\Calendar',
      '\App\Models\Holiday',
      '\App\Models\Operatingsystem',
      '\App\Models\Operatingsystemversion',
      '\App\Models\Operatingsystemservicepack',
      '\App\Models\Operatingsystemarchitecture',
      '\App\Models\Operatingsystemedition',
      '\App\Models\Operatingsystemkernel',
      '\App\Models\Operatingsystemkernelversion',
      '\App\Models\Autoupdatesystem',
      '\App\Models\Networkinterface',
      '\App\Models\Netpoint',
      '\App\Models\Network',
      '\App\Models\Vlan',
      '\App\Models\Lineoperator',
      '\App\Models\Domaintype',
      '\App\Models\Domainrelation',
      '\App\Models\Domainrecordtype',
      '\App\Models\Ipnetwork',
      '\App\Models\Fqdn',
      '\App\Models\Wifinetwork',
      '\App\Models\Networkname',
      '\App\Models\Softwarecategory',
      '\App\Models\Usertitle',
      '\App\Models\Usercategory',
      '\App\Models\Rulerightparameter',
      '\App\Models\Fieldblacklist',
      '\App\Models\Ssovariable',
      '\App\Models\Plug',
      '\App\Models\Appliancetype',
      '\App\Models\Applianceenvironment',
      '\App\Models\OauthimapApplication',
      '\App\Models\Devicepowersupply',
      '\App\Models\Devicebattery',
      '\App\Models\Devicecase',
      '\App\Models\Devicesensor',
      '\App\Models\Devicegraphiccard',
      '\App\Models\Devicemotherboard',
      '\App\Models\Devicenetworkcard',
      '\App\Models\Devicesoundcard',
      '\App\Models\Devicegeneric',
      '\App\Models\Devicecontrol',
      '\App\Models\Deviceharddrive',
      '\App\Models\Devicefirmware',
      '\App\Models\Devicedrive',
      '\App\Models\Devicememory',
      '\App\Models\Deviceprocessor',
      '\App\Models\Devicepci',
      '\App\Models\Devicesimcard',
    ],
  ];


  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Profile();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Profile();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Profile();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubAssets(Request $request, Response $response, $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'assets');
  }

  public function itemSubAssets(Request $request, Response $response, $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'assets');
  }

  public function showSubAssistance(Request $request, Response $response, $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'assistance');
  }

  public function itemSubAssistance(Request $request, Response $response, $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'assistance');
  }

  public function showSubForms(Request $request, Response $response, $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'forms');
  }

  public function itemSubForms(Request $request, Response $response, $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'forms');
  }

  public function showSubManagement(Request $request, Response $response, $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'management');
  }

  public function itemSubManagement(Request $request, Response $response, $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'management');
  }

  public function showSubTools(Request $request, Response $response, $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'tools');
  }

  public function itemSubTools(Request $request, Response $response, $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'tools');
  }

  public function showSubAdministration(Request $request, Response $response, $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'administration');
  }

  public function itemSubAdministration(Request $request, Response $response, $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'administration');
  }

  public function showSubSetup(Request $request, Response $response, $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'setup');
  }

  public function itemSubSetup(Request $request, Response $response, $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'setup');
  }

  private function showSubCategory(Request $request, Response $response, $args, $category)
  {
    global $translator;

    $view = Twig::fromRequest($request);

    $item = new \App\Models\Profile();
    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/' . $category);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addHeaderTitle('Fusion Resolve IT - ' . $item->getTitle(2));

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('definition', $item->getDefinitions());
    $viewData->addData('rights', $this->getRightsCategory($myItem->id, $category));
    $viewData->addData('custom', $this->getRightsCategoryCustom($myItem->id, $category));
    $viewData->addData('category', $category);

    return $view->render($response, 'subitem/profilecustom.html.twig', (array)$viewData);
  }

  private function itemSubCategory($request, $response, $args, $category)
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'profileright_id'))
    {
      // Update custom
      $profileright = \App\Models\Profileright::find($data->profileright_id);
      if (!is_null($profileright))
      {
        $item = new $profileright->model();
        $definitions = $item->getDefinitions();
        foreach ($definitions as $def)
        {
          $read = false;
          $write = false;
          if (property_exists($data, $def['name'] . '-read'))
          {
            $read = true;
          }
          if (property_exists($data, $def['name'] . '-write'))
          {
            $write = true;
          }

          \App\Models\Profilerightcustom::updateOrCreate(
            [
              'profileright_id'     => $profileright->id,
              'definitionfield_id'  => $def['id'],
            ],
            [
              'read'  => $read,
              'write' => $write,
            ]
          );
        }
      }
    }
    else
    {
      // Update general rights
      $rightLists = ['read', 'create', 'update', 'softdelete', 'delete', 'custom'];
      if ($category == 'assistance')
      {
        $rightLists[] = 'readmyitems';
        $rightLists[] = 'readmygroupitems';
        $rightLists[] = 'readprivateitems';
        $rightLists[] = 'canassign';
      }
      foreach ($this->rigthCategories[$category] as $model)
      {
        $dataRights = [];
        foreach ($rightLists as $right)
        {
          if (property_exists($data, $model . '-' . $right))
          {
            $dataRights[$right] = true;
          }
          else
          {
            $dataRights[$right] = false;
          }
        }
        \App\Models\Profileright::updateOrCreate(
          [
            'profile_id'  => $args['id'],
            'model'       => ltrim($model, '\\'),
          ],
          $dataRights,
        );
        // add message to session
        \App\v1\Controllers\Toolbox::addSessionMessage('The rights have been updated successfully');
      }
    }
    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
  }

  private function getRightsCategory($profile_id, $category)
  {
    $data = [];
    foreach ($this->rigthCategories[$category] as $model)
    {
      $profileright = \App\Models\Profileright::
          where('profile_id', $profile_id)
        ->where('model', ltrim($model, '\\'))
        ->first();
      if (is_null($profileright))
      {
        $profileright = new \App\Models\Profileright();
      }
      $item = new $model();
      if ($category == 'assistance')
      {
        $customData = [
          'model'   => $model,
          'title'   => $item->getTitle(2),
          'rights'  => [
            'read'              => $profileright->read,
            'readmyitems'       => 'disabled',
            'readmygroupitems'  => 'disabled',
            'readprivateitems'  => 'disabled',
            'canassign'         => 'disabled',
            'create'            => $profileright->create,
            'update'            => $profileright->update,
            'softdelete'        => $profileright->softdelete,
            'delete'            => $profileright->delete,
            'custom'            => $profileright->custom,
          ],
        ];
        if ($model == '\App\Models\Ticket')
        {
          $customData['rights']['readmyitems'] = $profileright->readmyitems;
          $customData['rights']['readmygroupitems'] = $profileright->readmygroupitems;
          $customData['rights']['canassign'] = $profileright->canassign;
        }
        if ($model == '\App\Models\Followup')
        {
          $customData['rights']['readprivateitems'] = $profileright->readprivateitems;
        }
        $data[] = $customData;
      }
      else
      {
        $data[] = [
          'model'   => $model,
          'title'   => $item->getTitle(2),
          'rights'  => [
            'read'        => $profileright->read,
            'create'      => $profileright->create,
            'update'      => $profileright->update,
            'softdelete'  => $profileright->softdelete,
            'delete'      => $profileright->delete,
            'custom'      => $profileright->custom,
          ],
        ];
      }
    }
    return $data;
  }

  private function getRightsCategoryCustom($profile_id, $category)
  {
    $data = [];
    foreach ($this->rigthCategories[$category] as $model)
    {
      $profileright = \App\Models\Profileright::
          where('profile_id', $profile_id)
        ->where('model', ltrim($model, '\\'))
        ->first();
      if (is_null($profileright) || !$profileright->custom)
      {
        continue;
      }
      $item = new $model();
      $itemData = [
        'model'           => $model,
        'profileright_id' => $profileright->id,
        'title'           => $item->getTitle(2),
        'customs'         => [],
      ];

      $customs = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($customs as $custom)
      {
        $ids[$custom->definitionfield_id] = [
          'read'  => $custom->read,
          'write' => $custom->write,
        ];
      }
      $definitions = $item->getDefinitions();
      foreach ($definitions as &$def)
      {
        if (isset($ids[$def["id"]]))
        {
          $itemData['customs'][] = [
            'title' => $def['title'],
            'name'  => $def['name'],
            'read'  => boolval($ids[$def["id"]]['read']),
            'write' => boolval($ids[$def["id"]]['write']),
          ];
        }
        else
        {
          $itemData['customs'][] = [
            'title' => $def['title'],
            'name'  => $def['name'],
            'read'  => false,
            'write' => false,
          ];
        }
      }
      $data[] = $itemData;
    }
    return $data;
  }

  public function getRigthCategories()
  {
    return $this->rigthCategories;
  }

  public static function canRightReadItem($item)
  {
    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', get_class($item))
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
    if ($profileright->read)
    {
      return true;
    }
    if ($profileright->readmyitems)
    {
      if ($item->user_id_recipient == $GLOBALS['user_id'])
      {
        return true;
      }
    }
    if ($profileright->readmygroupitems)
    {
    }

    return false;
  }

  public function showSubUsers(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\ProfileUser();
    $myItem2 = $item2::where('profile_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/users');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myUsers = [];
    foreach ($myItem2 as $current_item)
    {
      $entity_id = $current_item->entity_id;
      $entity = '';
      $findentity = \App\Models\Entity::find($entity_id);
      if ($findentity !== null)
      {
        $entity = $findentity->completename;
      }

      $user_id = $current_item->user_id;
      $user = '';
      $finduser = \App\Models\User::find($user_id);
      if ($finduser !== null)
      {
        $user = $this->genereUserName($finduser->name, $finduser->lastname, $finduser->firstname);
      }

      $is_recursive = '';
      if ($current_item->is_recursive == 1)
      {
        $is_recursive_val = $translator->translate('Yes');
      }
      else
      {
        $is_recursive_val = $translator->translate('No');
      }

      $is_dynamic = '';
      if ($current_item->is_dynamic == 1)
      {
        $is_dynamic_val = $translator->translate('Yes');
      }
      else
      {
        $is_dynamic_val = $translator->translate('No');
      }

      if (($entity != '') && ($user != ''))
      {
        if (array_key_exists($entity_id, $myUsers) !== true)
        {
          $myUsers[$entity_id] = [
            'name'  => $entity,
            'users' => [],
          ];
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/users/', $user_id);

        $myUsers[$entity_id]['users'][$user_id] = [
          'name'                  => $user,
          'url'                   => $url,
          'is_recursive'          => $is_recursive,
          'is_recursive_val'      => $is_recursive_val,
          'is_dynamic'            => $is_dynamic,
          'is_dynamic_val'        => $is_dynamic_val,
        ];
      }
    }

    // tri ordre alpha
    uasort($myUsers, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('users', $myUsers);
    $viewData->addData('show', $this->choose);


    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('is_dynamic', $translator->translate('Dynamic'));
    $viewData->addTranslation('is_recursive', $translator->translate('Recursive'));

    return $view->render($response, 'subitem/users.html.twig', (array)$viewData);
  }
}
