<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSoftware;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Appliance;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Domain;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use App\Traits\Subs\Knowbaseitem;
use App\Traits\Subs\Note;
use App\Traits\Subs\Reservation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Software extends Common implements \App\Interfaces\Crud
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
  use Externallink;
  use Knowbaseitem;
  use Document;
  use Contract;
  use Itil;
  use History;
  use Infocom;

  protected $model = \App\Models\Software::class;
  protected $rootUrl2 = '/softwares/';
  protected $choose = 'softwares';

  protected function instanciateModel(): \App\Models\Software
  {
    return new \App\Models\Software();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSoftware((object) $request->getParsedBody());

    $software = new \App\Models\Software();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($software))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $software = \App\Models\Software::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The software has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($software, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/softwares/' . $software->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/softwares')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSoftware((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $software = \App\Models\Software::where('id', $id)->first();
    if (is_null($software))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($software))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $software->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The software has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($software, 'update');

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
    $software = \App\Models\Software::withTrashed()->where('id', $id)->first();
    if (is_null($software))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($software->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $software->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/softwares')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $software->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software has been soft deleted successfully');
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
    $software = \App\Models\Software::withTrashed()->where('id', $id)->first();
    if (is_null($software))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($software->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $software->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The software has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubVersions(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Software();
    $view = Twig::fromRequest($request);

    $software = \App\Models\Software::with('versions')->where('id', $args['id'])->first();
    if (is_null($software))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/versions');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myVersions = [];
    $total_install = 0;
    foreach ($software->versions as $version)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/softwareversions/', $version->id);

      $status = '';
      $status_url = '';
      if ($version->state !== null)
      {
        $status = $version->state->name;
        $status_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $version->state->id);
      }

      $os = '';
      $os_url = '';
      if ($version->operatingsystem !== null)
      {
        $os = $version->operatingsystem->name;
        $os_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/operatingsystems/', $version->operatingsystem->id);
      }

      $nb_install = 0;
      $softwareWithDevices = \App\Models\Softwareversion::
          where('id', $version->id)
        ->with('devices')
        ->first();
      if (!is_null($softwareWithDevices))
      {
        $nb_install = count($softwareWithDevices->devices);
      }

      $total_install = $total_install + $nb_install;

      $myVersions[] = [
        'name'          => $version->name,
        'url'           => $url,
        'status'        => $status,
        'status_url'    => $status_url,
        'os'            => $os,
        'os_url'        => $os_url,
        'nb_install'    => $nb_install,
        'comment'       => $version->comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($software, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($software));
    $viewData->addData('versions', $myVersions);
    $viewData->addData('total_install', $total_install);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('os', $translator->translate('Operating System'));
    $viewData->addTranslation('nb_install', $translator->translatePlural('Installation', 'Installations', 2));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));
    $viewData->addTranslation('no_item_found', $translator->translate('No items found.'));
    $viewData->addTranslation('total', $translator->translate('Total'));

    return $view->render($response, 'subitem/versions.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubLicenses(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Software();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $softwarelicense = \App\Models\Softwarelicense::where('software_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/licenses');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myLicenses = [];
    $total_number = 0;
    $total_affected_items = 0;
    foreach ($softwarelicense as $license)
    {
      $name = $license->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/softwarelicenses/', $license->id);

      $serial = $license->serial;

      $number = $license->number;
      $total_number = $total_number + $number;

      $affected_items = 0;
      $licenseComput = \App\Models\Softwarelicense::where('id', $license->id)->with('computers')->first();
      if (!is_null($licenseComput))
      {
        $affected_items = count($licenseComput->computers);
      }

      $total_affected_items = $total_affected_items + $affected_items;

      $type = '';
      $type_url = '';
      if ($license->softwarelicensetype !== null)
      {
        $type = $license->softwarelicensetype->name;
        $type_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/softwarelicensetypes/',
          $license->softwarelicensetype->id
        );
      }

      $version_buy = '';
      if ($license->softwareversionsBuy !== null)
      {
        $version_buy = $license->softwareversionsBuy->name;
      }

      $version_use = '';
      if ($license->softwareversionsUse !== null)
      {
        $version_use = $license->softwareversionsUse->name;
      }

      $exp_date = $license->expire;

      $alert_expiration = false;
      $date_expiration = $license->expire;
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

      $status = '';
      $status_url = '';
      if ($license->state !== null)
      {
        $status = $license->state->name;
        $status_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $license->state->id);
      }

      $myLicenses[] = [
        'name'                     => $name,
        'url'                      => $url,
        'serial'                   => $serial,
        'number'                   => $number,
        'affected_items'           => $affected_items,
        'type'                     => $type,
        'type_url'                 => $type_url,
        'version_buy'              => $version_buy,
        'version_use'              => $version_use,
        'date_expiration'          => $date_expiration,
        'alert_expiration'         => $alert_expiration,
        'status'                   => $status,
        'status_url'               => $status_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('licenses', $myLicenses);
    $viewData->addData('total_number', $total_number);
    $viewData->addData('total_affected_items', $total_affected_items);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('serial', $translator->translate('Serial number'));
    $viewData->addTranslation('number', $translator->translate('Number'));
    $viewData->addTranslation('affected_items', $translator->translate('Affected items'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('version_buy', $translator->translate('Purchase version'));
    $viewData->addTranslation('version_use', $translator->translate('Version in use'));
    $viewData->addTranslation('expiration', $translator->translate('Expiration'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('total', $translator->translate('Total'));

    return $view->render($response, 'subitem/licenses.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubSoftwareInstall(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Software();
    $view = Twig::fromRequest($request);

    $software = \App\Models\Software::with('versions')->where('id', $args['id'])->first();
    if (is_null($software))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/softwareinstall');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    // get software version
    $mySoftwareInstall = [];
    foreach ($software->versions as $version)
    {
      $softwareWithDevices = \App\Models\Softwareversion::
          where('id', $version->id)
        ->with('devices')
        ->first();
      if (is_null($softwareWithDevices))
      {
        continue;
      }

      $computerLicences = [];
      $items_licences = \App\Models\Softwarelicense::
          where('id', $version->id)
        ->with('computers')
        ->get();
      foreach ($items_licences as $license)
      {
        foreach ($license->computers as $computer)
        {
          $computerLicences[$computer->id] = [
            'id'   => $license->id,
            'name' => $license->name,
            'type' => $license->softwarelicensetype,
          ];
        }
      }

      foreach ($softwareWithDevices->devices as $computer)
      {
        $type_fr = $computer->getTitle();
        $type = $computer->getTable();

        $versionName = $version->name;

        $version_url = $this->genereRootUrl2Link($rootUrl2, '/softwareversion/', $version->id);

        $nom = $computer->name;

        $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $computer->id);

        $serial = $computer->serial;

        $otherserial = $computer->otherserial;

        $location = '';
        $location_url = '';
        if ($computer->location !== null)
        {
          $location = $computer->location->name;
          $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $computer->location->id);
        }

        $status = '';
        $status_url = '';
        if ($computer->state !== null)
        {
          $status = $computer->state->name;
          $status_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $computer->state->id);
        }

        $group = '';
        $group_url = '';
        if ($computer->group !== null)
        {
          $group = $computer->group->completename;
          $group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $computer->group->id);
        }

        $user = '';
        $user_url = '';
        if ($computer->user !== null)
        {
          $user = $computer->user->completename;
          $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $computer->user->id);
        }

        $licences = '';
        $licences_url = '';
        $licences_serial_type = '';
        if (isset($computerLicences[$computer->id]))
        {
          $licences = $computerLicences[$computer->id]['name'];
          $licences_url = $this->genereRootUrl2Link(
            $rootUrl2,
            '/softwarelicenses/',
            $computerLicences[$computer->id]['id']
          );
          if (!is_null($computerLicences[$computer->id]['type']))
          {
            $licences_serial_type = $computerLicences[$computer->id]['type']->name;
          }
        }

        $date_install = $computer->getRelationValue('pivot')->date_install;

        $mySoftwareInstall[] = [
          'version'                 => $versionName,
          'version_url'             => $version_url,
          'type'                    => $type_fr,
          'nom'                     => $nom,
          'nom_url'                 => $nom_url,
          'serial'                  => $serial,
          'otherserial'             => $otherserial,
          'location'                => $location,
          'location_url'            => $location_url,
          'status'                  => $status,
          'status_url'              => $status_url,
          'group'                   => $group,
          'group_url'               => $group_url,
          'user'                    => $user,
          'user_url'                => $user_url,
          'licences'                => $licences,
          'licences_url'            => $licences_url,
          'licences_serial_type'    => $licences_serial_type,
          'date_install'            => $date_install,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($software, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($software));
    $viewData->addData('softwareinstall', $mySoftwareInstall);

    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));
    $viewData->addTranslation('type', $translator->translate('Item type'));
    $viewData->addTranslation('nom', $translator->translate('Name'));
    $viewData->addTranslation('serial', $translator->translate('Serial number'));
    $viewData->addTranslation('otherserial', $translator->translate('Inventory number'));
    $viewData->addTranslation('location', $translator->translatePlural('Location', 'Locations', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('licences', $translator->translatePlural('License', 'Licenses', 1));
    $viewData->addTranslation('date_install', $translator->translate('Installation date'));

    return $view->render($response, 'subitem/softwareinstall.html.twig', (array)$viewData);
  }
}
