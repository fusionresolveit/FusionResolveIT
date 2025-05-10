<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostProfile;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Profile extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Profile::class;
  protected $rootUrl2 = '/profiles/';
  protected $choose = 'profiles';

  protected function instanciateModel(): \App\Models\Profile
  {
    return new \App\Models\Profile();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostProfile((object) $request->getParsedBody());

    $profile = new \App\Models\Profile();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($profile))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $profile = \App\Models\Profile::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The profile has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($profile, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/profiles/' . $profile->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/profiles')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostProfile((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $profile = \App\Models\Profile::where('id', $id)->first();
    if (is_null($profile))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($profile))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $profile->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The profile has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($profile, 'update');

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
    $profile = \App\Models\Profile::withTrashed()->where('id', $id)->first();
    if (is_null($profile))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($profile->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $profile->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The profile has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/profiles')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $profile->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The profile has been soft deleted successfully');
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
    $profile = \App\Models\Profile::withTrashed()->where('id', $id)->first();
    if (is_null($profile))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($profile->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $profile->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The profile has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /** @var array<mixed> */
  protected $rigthCategories = [
    'assets' => [
      \App\Models\Computer::class,
      \App\Models\Monitor::class,
      \App\Models\Software::class,
      \App\Models\Networkequipment::class,
      \App\Models\Peripheral::class,
      \App\Models\Printer::class,
      \App\Models\Phone::class,
      \App\Models\Cartridgeitem::class,
      \App\Models\Consumableitem::class,
      \App\Models\Rack::class,
      \App\Models\Enclosure::class,
      \App\Models\Pdu::class,
      \App\Models\Passivedcequipment::class,
      \App\Models\ItemDevicesimcard::class,
    ],
    'assistance' => [
      \App\Models\Ticket::class,
      \App\Models\Followup::class,
      \App\Models\Problem::class,
      \App\Models\Change::class,
      \App\Models\Ticketrecurrent::class,
    ],
    'forms' => [
      \App\Models\Forms\Form::class,
      \App\Models\Forms\Section::class,
      \App\Models\Forms\Question::class,
      // '\App\Models\Forms\Answer',
    ],
    'management' => [
      \App\Models\Softwarelicense::class,
      \App\Models\Budget::class,
      \App\Models\Supplier::class,
      \App\Models\Contact::class,
      \App\Models\Contract::class,
      \App\Models\Line::class,
      \App\Models\Certificate::class,
      \App\Models\Document::class,
      \App\Models\Datacenter::class,
      \App\Models\Dcroom::class,
      \App\Models\Cluster::class,
      \App\Models\Domain::class,
      \App\Models\Appliance::class,
      \App\Models\Knowledgebasearticle::class,
    ],
    'tools' => [
      \App\Models\Project::class,
      \App\Models\Reminder::class,
      \App\Models\Rssfeed::class,
      \App\Models\Savedsearch::class,
      \App\Models\Alert::class,
    ],
    'administration' => [
      \App\Models\User::class,
      \App\Models\Group::class,
      \App\Models\Entity::class,
      \App\Models\Rules\Ticket::class,
      \App\Models\Rules\User::class,
      \App\Models\Profile::class,
      \App\Models\Queuednotification::class,
      \App\Models\Audit::class,
      \App\Models\Displaypreference::class,
    ],
    'setup' => [
      \App\Models\Authsso::class,
      \App\Models\Authldap::class,
      \App\Models\Notification::class,
      \App\Models\Notificationtemplate::class,
      \App\Models\Notificationtemplatetranslation::class,
      \App\Models\Slm::class,
      \App\Models\Fieldunicity::class,
      \App\Models\Crontask::class,
      \App\Models\Link::class,
      \App\Models\Mailcollector::class,
      \App\Models\Location::class,
      \App\Models\State::class,
      \App\Models\Manufacturer::class,
      \App\Models\Blacklist::class,
      \App\Models\Blacklistedmailcontent::class,
      \App\Models\Category::class,
      \App\Models\Tickettemplate::class,
      \App\Models\Solutiontype::class,
      \App\Models\Solutiontemplate::class,
      \App\Models\Requesttype::class,
      \App\Models\Followuptemplate::class,
      \App\Models\Projectstate::class,
      \App\Models\Projecttype::class,
      \App\Models\Projecttask::class,
      \App\Models\Projecttasktype::class,
      \App\Models\Projecttasktemplate::class,
      \App\Models\Planningeventcategory::class,
      \App\Models\Planningexternaleventtemplate::class,
      \App\Models\Computertype::class,
      \App\Models\Networkequipmenttype::class,
      \App\Models\Printertype::class,
      \App\Models\Monitortype::class,
      \App\Models\Peripheraltype::class,
      \App\Models\Phonetype::class,
      \App\Models\Softwarelicensetype::class,
      \App\Models\Cartridgeitemtype::class,
      \App\Models\Consumableitemtype::class,
      \App\Models\Contracttype::class,
      \App\Models\Contacttype::class,
      \App\Models\Devicegenerictype::class,
      \App\Models\Devicesensortype::class,
      \App\Models\Devicememorytype::class,
      \App\Models\Suppliertype::class,
      \App\Models\Interfacetype::class,
      \App\Models\Devicecasetype::class,
      \App\Models\Phonepowersupply::class,
      \App\Models\Filesystem::class,
      \App\Models\Certificatetype::class,
      \App\Models\Budgettype::class,
      \App\Models\Devicesimcardtype::class,
      \App\Models\Linetype::class,
      \App\Models\Racktype::class,
      \App\Models\Pdutype::class,
      \App\Models\Passivedcequipmenttype::class,
      \App\Models\Clustertype::class,
      \App\Models\Computermodel::class,
      \App\Models\Networkequipmentmodel::class,
      \App\Models\Printermodel::class,
      \App\Models\Monitormodel::class,
      \App\Models\Peripheralmodel::class,
      \App\Models\Phonemodel::class,
      \App\Models\Devicecasemodel::class,
      \App\Models\Devicecontrolmodel::class,
      \App\Models\Devicedrivemodel::class,
      \App\Models\Devicegenericmodel::class,
      \App\Models\Devicegraphiccardmodel::class,
      \App\Models\Deviceharddrivemodel::class,
      \App\Models\Devicememorymodel::class,
      \App\Models\Devicemotherboardmodel::class,
      \App\Models\Devicenetworkcardmodel::class,
      \App\Models\Devicepcimodel::class,
      \App\Models\Devicepowersupplymodel::class,
      \App\Models\Deviceprocessormodel::class,
      \App\Models\Devicesoundcardmodel::class,
      \App\Models\Devicesensormodel::class,
      \App\Models\Rackmodel::class,
      \App\Models\Enclosuremodel::class,
      \App\Models\Pdumodel::class,
      \App\Models\Passivedcequipmentmodel::class,
      \App\Models\Virtualmachinetype::class,
      \App\Models\Virtualmachinesystem::class,
      \App\Models\Virtualmachinestate::class,
      \App\Models\Documentcategory::class,
      \App\Models\Documenttype::class,
      \App\Models\Businesscriticity::class,
      \App\Models\Calendar::class,
      \App\Models\Holiday::class,
      \App\Models\Operatingsystem::class,
      \App\Models\Operatingsystemversion::class,
      \App\Models\Operatingsystemservicepack::class,
      \App\Models\Operatingsystemarchitecture::class,
      \App\Models\Operatingsystemedition::class,
      \App\Models\Operatingsystemkernel::class,
      \App\Models\Operatingsystemkernelversion::class,
      \App\Models\Autoupdatesystem::class,
      \App\Models\Networkinterface::class,
      \App\Models\Netpoint::class,
      \App\Models\Network::class,
      \App\Models\Vlan::class,
      \App\Models\Lineoperator::class,
      \App\Models\Domaintype::class,
      \App\Models\Domainrelation::class,
      \App\Models\Domainrecordtype::class,
      \App\Models\Ipnetwork::class,
      \App\Models\Fqdn::class,
      \App\Models\Wifinetwork::class,
      \App\Models\Networkname::class,
      \App\Models\Softwarecategory::class,
      \App\Models\Usertitle::class,
      \App\Models\Usercategory::class,
      \App\Models\Rulerightparameter::class,
      \App\Models\Fieldblacklist::class,
      \App\Models\Ssovariable::class,
      \App\Models\Plug::class,
      \App\Models\Appliancetype::class,
      \App\Models\Applianceenvironment::class,
      \App\Models\Devicepowersupply::class,
      \App\Models\Devicebattery::class,
      \App\Models\Devicecase::class,
      \App\Models\Devicesensor::class,
      \App\Models\Devicegraphiccard::class,
      \App\Models\Devicemotherboard::class,
      \App\Models\Devicenetworkcard::class,
      \App\Models\Devicesoundcard::class,
      \App\Models\Devicegeneric::class,
      \App\Models\Devicecontrol::class,
      \App\Models\Deviceharddrive::class,
      \App\Models\Devicefirmware::class,
      \App\Models\Devicedrive::class,
      \App\Models\Devicememory::class,
      \App\Models\Deviceprocessor::class,
      \App\Models\Devicepci::class,
      \App\Models\Devicesimcard::class,
    ],
  ];

  /**
   * @param array<string, string> $args
   */
  public function showSubAssets(Request $request, Response $response, array $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'assets');
  }

  /**
   * @param array<string, string> $args
   */
  public function itemSubAssets(Request $request, Response $response, array $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'assets');
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubAssistance(Request $request, Response $response, array $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'assistance');
  }

  /**
   * @param array<string, string> $args
   */
  public function itemSubAssistance(Request $request, Response $response, array $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'assistance');
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubForms(Request $request, Response $response, array $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'forms');
  }

  /**
   * @param array<string, string> $args
   */
  public function itemSubForms(Request $request, Response $response, array $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'forms');
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubManagement(Request $request, Response $response, array $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'management');
  }

  /**
   * @param array<string, string> $args
   */
  public function itemSubManagement(Request $request, Response $response, array $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'management');
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubTools(Request $request, Response $response, array $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'tools');
  }

  /**
   * @param array<string, string> $args
   */
  public function itemSubTools(Request $request, Response $response, array $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'tools');
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubAdministration(Request $request, Response $response, array $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'administration');
  }

  /**
   * @param array<string, string> $args
   */
  public function itemSubAdministration(Request $request, Response $response, array $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'administration');
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubSetup(Request $request, Response $response, array $args): Response
  {
    return $this->showSubCategory($request, $response, $args, 'setup');
  }

  /**
   * @param array<string, string> $args
   */
  public function itemSubSetup(Request $request, Response $response, array $args): Response
  {
    return $this->itemSubCategory($request, $response, $args, 'setup');
  }

  /**
   * @param array<string, string> $args
   */
  private function showSubCategory(Request $request, Response $response, array $args, string $category): Response
  {
    $view = Twig::fromRequest($request);

    $item = new \App\Models\Profile();
    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

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

  /**
   * @param array<string, string> $args
   */
  private function itemSubCategory(Request $request, Response $response, array $args, string $category): Response
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'profileright_id'))
    {
      // Update custom
      $profileright = \App\Models\Profileright::where('id', $data->profileright_id)->first();
      if (!is_null($profileright))
      {
        $item = new $profileright->model();
        $definitions = $item->getDefinitions();
        foreach ($definitions as $def)
        {
          $read = false;
          $write = false;
          if (property_exists($data, $def->name . '-read'))
          {
            $read = true;
          }
          if (property_exists($data, $def->name . '-write'))
          {
            $write = true;
          }

          \App\Models\Profilerightcustom::updateOrCreate(
            [
              'profileright_id'     => $profileright->id,
              'definitionfield_id'  => $def->id,
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

    return $response
      ->withHeader('Location', (string) $uri);
  }

  /**
   * @return array<mixed>
   */
  private function getRightsCategory(int $profile_id, string $category): array
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
      if (!is_subclass_of($item, \App\Models\Common::class))
      {
        continue;
      }
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

  /**
   * @return array<mixed>
   */
  private function getRightsCategoryCustom(int $profile_id, string $category): array
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
      if (!is_subclass_of($item, \App\Models\Common::class))
      {
        continue;
      }
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
        if (isset($ids[$def->id]))
        {
          $itemData['customs'][] = [
            'title' => $def->title,
            'name'  => $def->name,
            'read'  => boolval($ids[$def->id]['read']),
            'write' => boolval($ids[$def->id]['write']),
          ];
        }
        else
        {
          $itemData['customs'][] = [
            'title' => $def->title,
            'name'  => $def->name,
            'read'  => false,
            'write' => false,
          ];
        }
      }
      $data[] = $itemData;
    }
    return $data;
  }

  /**
   * @return array<mixed>
   */
  public function getRigthCategories(): array
  {
    return $this->rigthCategories;
  }

  public static function canRightReadItem(object $item): bool
  {
    if (!is_subclass_of($item, \App\Models\Common::class))
    {
      return false;
    }
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
      if ($item->getAttribute('user_id_recipient') == $GLOBALS['user_id'])
      {
        return true;
      }
    }
    if ($profileright->readmygroupitems)
    {
    }

    return false;
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubUsers(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Profile();
    $view = Twig::fromRequest($request);

    $profile = \App\Models\Profile::where('id', $args['id'])->with('users')->first();
    if (is_null($profile))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/users');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myUsers = [];
    foreach ($profile->users as $current_item)
    {
      $entity_id = $current_item->getRelationValue('pivot')->entity_id;
      $entity = '';
      $findentity = \App\Models\Entity::where('id', $entity_id)->first();
      if ($findentity !== null)
      {
        $entity = $findentity->completename;
      }

      $user = $this->genereUserName($current_item->name, $current_item->lastname, $current_item->firstname);

      $is_recursive = '';
      if ($current_item->getRelationValue('pivot')->is_recursive == 1)
      {
        $is_recursive_val = $translator->translate('Yes');
      }
      else
      {
        $is_recursive_val = $translator->translate('No');
      }

      $is_dynamic = '';
      if ($current_item->getRelationValue('pivot')->is_dynamic == 1)
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

        $url = $this->genereRootUrl2Link($rootUrl2, '/users/', $current_item->id);

        $myUsers[$entity_id]['users'][$current_item->id] = [
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
    array_multisort(array_column($myUsers, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myUsers);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($profile, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($profile));
    $viewData->addData('users', $myUsers);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('is_dynamic', $translator->translate('Dynamic'));
    $viewData->addTranslation('is_recursive', $translator->translate('Recursive'));

    return $view->render($response, 'subitem/users.html.twig', (array)$viewData);
  }
}
