<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Component
{
  /**
   * @param array<string, string> $args
   */
  public function showSubComponents(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
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
    )->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/components');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);


    $colorTab = [];
    $colorTab['memories'] = 'red';
    $colorTab['firmwares'] = 'orange';
    $colorTab['processors'] = 'olive';
    $colorTab['harddrives'] = 'teal';
    $colorTab['batteries'] = 'blue';
    $colorTab['soundcards'] = 'purple';
    $colorTab['controllers'] = 'red';
    $colorTab['powersupplies'] = 'orange';
    $colorTab['sensors'] = 'olive';
    $colorTab['devicepcis'] = 'teal';
    $colorTab['devicegenerics'] = 'blue';
    $colorTab['devicenetworkcards'] = 'purple';
    $colorTab['devicesimcards'] = 'brown';
    $colorTab['devicemotherboards'] = 'red';
    $colorTab['devicecases'] = 'orange';
    $colorTab['devicegraphiccards'] = 'olive';
    $colorTab['devicedrives'] = 'teal';

    $myMemories = [];
    foreach ($myItem->memories as $memory)
    {
      $location = '';
      $location_url = '';

      $loc = \App\Models\Location::where('id', $memory->getRelationValue('pivot')->location_id)->first();
      if (!is_null($loc))
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($memory->manufacturer !== null)
      {
        $manufacturer = $memory->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $memory->manufacturer->id
        );
      }

      $type = '';
      $type_url = '';
      if ($memory->type !== null)
      {
        $type = $memory->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/devicememorytype/', $memory->type->id);
      }

      $serial = $memory->getRelationValue('pivot')->serial;

      $otherserial = $memory->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';

      $status = \App\Models\State::where('id', $memory->getRelationValue('pivot')->state_id)->first();
      if (!is_null($status))
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($memory->documents !== null)
      {
        foreach ($memory->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myMemories[] = [
        'name'                => $memory->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'type'                => $type,
        'type_url'            => $type_url,
        'frequence'           => $memory->frequence,
        'size'                => $memory->getRelationValue('pivot')->size,
        'busID'               => $memory->getRelationValue('pivot')->busID,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['memories'],
      ];
    }

    $myFirmwares = [];
    foreach ($myItem->firmwares as $firmware)
    {
      $location = '';
      $location_url = '';

      $loc = \App\Models\Location::where('id', $firmware->getRelationValue('pivot')->location_id)->first();
      if (!is_null($loc))
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($firmware->manufacturer !== null)
      {
        $manufacturer = $firmware->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $firmware->manufacturer->id
        );
      }

      $type = '';
      $type_url = '';
      if ($firmware->type !== null)
      {
        $type = $firmware->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/devices/devicefirmwaretypes/', $firmware->type->id);
      }

      $serial = $firmware->getRelationValue('pivot')->serial;

      $otherserial = $firmware->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $firmware->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($firmware->documents !== null)
      {
        foreach ($firmware->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myFirmwares[] = [
        'name'                => $firmware->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'type'                => $type,
        'type_url'            => $type_url,
        'version'             => $firmware->version,
        'date'                => $firmware->date,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['firmwares'],
      ];
    }

    $myProcessors = [];
    foreach ($myItem->processors as $processor)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $processor->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($processor->manufacturer !== null)
      {
        $manufacturer = $processor->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $processor->manufacturer->id
        );
      }

      $serial = $processor->getRelationValue('pivot')->serial;

      $otherserial = $processor->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $processor->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $processor->getRelationValue('pivot')->busID;

      $documents = [];
      if ($processor->documents !== null)
      {
        foreach ($processor->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myProcessors[] = [
        'name'                => $processor->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'frequency'           => $processor->getRelationValue('pivot')->frequency,
        'nbcores'             => $processor->getRelationValue('pivot')->nbcores,
        'nbthreads'           => $processor->getRelationValue('pivot')->nbthreads,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['processors'],
      ];
    }

    $myHarddrives = [];
    foreach ($myItem->harddrives as $harddrive)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $harddrive->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($harddrive->manufacturer !== null)
      {
        $manufacturer = $harddrive->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $harddrive->manufacturer->id
        );
      }

      $interface = '';
      $interface_url = '';
      if ($harddrive->interface !== null)
      {
        $interface = $harddrive->interface->name;
        $interface_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/interfacetypes/', $harddrive->interface->id);
      }

      $serial = $harddrive->getRelationValue('pivot')->serial;

      $otherserial = $harddrive->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $harddrive->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $harddrive->getRelationValue('pivot')->busID;

      $documents = [];
      if ($harddrive->documents !== null)
      {
        foreach ($harddrive->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myHarddrives[] = [
        'name'                => $harddrive->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'rpm'                 => $harddrive->rpm,
        'cache'               => $harddrive->cache,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'capacity'            => $harddrive->getRelationValue('pivot')->capacity,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['harddrives'],
      ];
    }

    $myBatteries = [];
    foreach ($myItem->batteries as $battery)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $battery->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($battery->manufacturer !== null)
      {
        $manufacturer = $battery->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $battery->manufacturer->id
        );
      }

      $type = '';
      $type_url = '';
      if ($battery->type !== null)
      {
        $type = $battery->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/devices/devicebatterytypes/', $battery->type->id);
      }

      $serial = $battery->getRelationValue('pivot')->serial;

      $otherserial = $battery->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $battery->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($battery->documents !== null)
      {
        foreach ($battery->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myBatteries[] = [
        'name'                => $battery->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'type'                => $type,
        'type_url'            => $type_url,
        'voltage'             => $battery->voltage,
        'capacity'            => $battery->capacity,
        'manufacturing_date'  => $battery->getRelationValue('pivot')->manufacturing_date,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['batteries'],
      ];
    }

    $mySoundcards = [];
    foreach ($myItem->soundcards as $soundcard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $soundcard->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($soundcard->manufacturer !== null)
      {
        $manufacturer = $soundcard->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $soundcard->manufacturer->id
        );
      }

      $serial = $soundcard->getRelationValue('pivot')->serial;

      $otherserial = $soundcard->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $soundcard->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $soundcard->getRelationValue('pivot')->busID;

      $documents = [];
      if ($soundcard->documents !== null)
      {
        foreach ($soundcard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $mySoundcards[] = [
        'name'                => $soundcard->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'type'                => $soundcard->type,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['soundcards'],
      ];
    }

    $myControllers = [];
    foreach ($myItem->controllers as $controller)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $controller->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($controller->manufacturer !== null)
      {
        $manufacturer = $controller->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $controller->manufacturer->id
        );
      }

      $interface = '';
      $interface_url = '';
      if ($controller->interface !== null)
      {
        $interface = $controller->interface->name;
        $interface_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/interfacetypes/', $controller->interface->id);
      }

      $serial = $controller->getRelationValue('pivot')->serial;

      $otherserial = $controller->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $controller->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $controller->getRelationValue('pivot')->busID;

      $documents = [];
      if ($controller->documents !== null)
      {
        foreach ($controller->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myControllers[] = [
        'name'                => $controller->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['controllers'],
      ];
    }

    $myPowerSupplies = [];
    foreach ($myItem->powersupplies as $powersupply)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $powersupply->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($powersupply->manufacturer !== null)
      {
        $manufacturer = $powersupply->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $powersupply->manufacturer->id
        );
      }

      $serial = $powersupply->getRelationValue('pivot')->serial;

      $otherserial = $powersupply->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $powersupply->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($powersupply->documents !== null)
      {
        foreach ($powersupply->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myPowerSupplies[] = [
        'name'                => $powersupply->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['powersupplies'],
      ];
    }

    $mySensors = [];
    foreach ($myItem->sensors as $sensor)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $sensor->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($sensor->manufacturer !== null)
      {
        $manufacturer = $sensor->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $sensor->manufacturer->id
        );
      }

      $serial = $sensor->getRelationValue('pivot')->serial;

      $otherserial = $sensor->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $sensor->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($sensor->documents !== null)
      {
        foreach ($sensor->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $mySensors[] = [
        'name'                => $sensor->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['sensors'],
      ];
    }

    $myDevicepcis = [];
    foreach ($myItem->devicepcis as $devicepci)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $devicepci->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $serial = $devicepci->getRelationValue('pivot')->serial;

      $otherserial = $devicepci->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $devicepci->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $devicepci->getRelationValue('pivot')->busID;

      $documents = [];
      if ($devicepci->documents !== null)
      {
        foreach ($devicepci->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myDevicepcis[] = [
        'name'            => $devicepci->name,
        'location'        => $location,
        'location_url'    => $location_url,
        'serial'          => $serial,
        'otherserial'     => $otherserial,
        'state'           => $state,
        'state_url'       => $state_url,
        'busID'           => $busID,
        'documents'       => $documents,
        'color'           => $colorTab['devicepcis'],
      ];
    }

    $myDevicegenerics = [];
    foreach ($myItem->devicegenerics as $devicegeneric)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $devicegeneric->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicegeneric->manufacturer !== null)
      {
        $manufacturer = $devicegeneric->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicegeneric->manufacturer->id
        );
      }

      $serial = $devicegeneric->getRelationValue('pivot')->serial;

      $otherserial = $devicegeneric->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $devicegeneric->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($devicegeneric->documents !== null)
      {
        foreach ($devicegeneric->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myDevicegenerics[] = [
        'name'                => $devicegeneric->name,
        'location'            => $location,
        'location_url'        => $location_url,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['devicegenerics'],
      ];
    }

    $myDevicenetworkcards = [];
    foreach ($myItem->devicenetworkcards as $devicenetworkcard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $devicenetworkcard->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicenetworkcard->manufacturer !== null)
      {
        $manufacturer = $devicenetworkcard->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicenetworkcard->manufacturer->id
        );
      }

      $serial = $devicenetworkcard->getRelationValue('pivot')->serial;

      $otherserial = $devicenetworkcard->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $devicenetworkcard->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $devicenetworkcard->getRelationValue('pivot')->busID;

      $speed = $devicenetworkcard->bandwidth;

      $documents = [];
      if ($devicenetworkcard->documents !== null)
      {
        foreach ($devicenetworkcard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $mac_address = $devicenetworkcard->getRelationValue('pivot')->mac;

      $myDevicenetworkcards[] = [
        'name'                => $devicenetworkcard->name,
        'location'            => $location,
        'location_url'        => $location_url,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'speed'               => $speed,
        'documents'           => $documents,
        'mac_address'         => $mac_address,
        'color'               => $colorTab['devicenetworkcards'],
      ];
    }

    $myDevicesimcards = [];
    foreach ($myItem->devicesimcards as $devicesimcard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $devicesimcard->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $serial = $devicesimcard->getRelationValue('pivot')->serial;

      $otherserial = $devicesimcard->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $devicesimcard->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $line = '';
      $line_url = '';
      $find_line = \App\Models\Line::where('id', $devicesimcard->getRelationValue('pivot')->line_id)->first();
      if ($find_line !== null)
      {
        $line = $find_line->name;
        $line_url = $this->genereRootUrl2Link($rootUrl2, '/lines/', $find_line->id);
      }

      $msin = $devicesimcard->getRelationValue('pivot')->msin;

      $user = '';
      $user_url = '';
      $find_user = \App\Models\User::where('id', $devicesimcard->getRelationValue('pivot')->user_id)->first();
      if ($find_user !== null)
      {
        $user = $find_user->name;
        $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $find_user->id);
      }

      $group = '';
      $group_url = '';
      $find_group = \App\Models\Group::where('id', $devicesimcard->getRelationValue('pivot')->group_id)->first();
      if ($find_group !== null)
      {
        $group = $find_group->name;
        $group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $find_group->id);
      }

      $documents = [];
      if ($devicesimcard->documents !== null)
      {
        foreach ($devicesimcard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myDevicesimcards[] = [
        'name'                => $devicesimcard->name,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'line'                => $line,
        'line_url'            => $line_url,
        'msin'                => $msin,
        'user'                => $user,
        'user_url'            => $user_url,
        'group'               => $group,
        'group_url'           => $group_url,
        'documents'           => $documents,
        'color'               => $colorTab['devicesimcards'],
      ];
    }

    $myDevicemotherboards = [];
    foreach ($myItem->devicemotherboards as $devicemotherboard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $devicemotherboard->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicemotherboard->manufacturer !== null)
      {
        $manufacturer = $devicemotherboard->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicemotherboard->manufacturer->id
        );
      }

      $serial = $devicemotherboard->getRelationValue('pivot')->serial;

      $otherserial = $devicemotherboard->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $devicemotherboard->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($devicemotherboard->documents !== null)
      {
        foreach ($devicemotherboard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myDevicemotherboards[] = [
        'name'                => $devicemotherboard->name,
        'location'            => $location,
        'location_url'        => $location_url,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['devicemotherboards'],
      ];
    }

    $myDevicecases = [];
    foreach ($myItem->devicecases as $devicecase)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $devicecase->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicecase->manufacturer !== null)
      {
        $manufacturer = $devicecase->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicecase->manufacturer->id
        );
      }

      $serial = $devicecase->getRelationValue('pivot')->serial;

      $otherserial = $devicecase->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $devicecase->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($devicecase->documents !== null)
      {
        foreach ($devicecase->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myDevicecases[] = [
        'name'                => $devicecase->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['devicecases'],
      ];
    }

    $myDevicegraphiccards = [];
    foreach ($myItem->devicegraphiccards as $devicegraphiccard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $devicegraphiccard->getRelationValue('pivot')->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicegraphiccard->manufacturer !== null)
      {
        $manufacturer = $devicegraphiccard->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicegraphiccard->manufacturer->id
        );
      }

      $interface = '';
      $interface_url = '';
      if ($devicegraphiccard->interface !== null)
      {
        $interface = $devicegraphiccard->interface->name;
        $interface_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/interfacetypes/',
          $devicegraphiccard->interface->id
        );
      }

      $serial = $devicegraphiccard->getRelationValue('pivot')->serial;

      $otherserial = $devicegraphiccard->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $devicegraphiccard->getRelationValue('pivot')->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $devicegraphiccard->getRelationValue('pivot')->busID;

      $documents = [];
      if ($devicegraphiccard->documents !== null)
      {
        foreach ($devicegraphiccard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myDevicegraphiccards[] = [
        'name'                => $devicegraphiccard->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'chipset'             => $devicegraphiccard->chipset,
        'memory'              => $devicegraphiccard->getRelationValue('pivot')->memory,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['devicegraphiccards'],
      ];
    }

    $myDevicedrives = [];
    foreach ($myItem->devicedrives as $devicedrive)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $devicedrive->getRelationValue('pivot')->location_id)->first();
      if (!is_null($loc))
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicedrive->manufacturer !== null)
      {
        $manufacturer = $devicedrive->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicedrive->manufacturer->id
        );
      }

      $write = $devicedrive->is_writer;
      if ($write == 1)
      {
        $write_val = $translator->translate('Yes');
      } else {
        $write_val = $translator->translate('No');
      }

      $speed = $devicedrive->speed;

      $interface = '';
      $interface_url = '';
      if ($devicedrive->interface !== null)
      {
        $interface = $devicedrive->interface->name;
        $interface_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/interfacetypes/',
          $devicedrive->interface->id
        );
      }

      $serial = $devicedrive->getRelationValue('pivot')->serial;

      $otherserial = $devicedrive->getRelationValue('pivot')->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $devicedrive->getRelationValue('pivot')->state_id)->first();
      if (!is_null($status))
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $devicedrive->getRelationValue('pivot')->busID;

      $documents = [];
      if ($devicedrive->documents !== null)
      {
        foreach ($devicedrive->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myDevicedrives[] = [
        'name'                => $devicedrive->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'write'               => $write,
        'write_val'           => $write_val,
        'speed'               => $speed,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['devicedrives'],
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
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('msin', $translator->translate('Mobile Subscriber Identification Number'));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('flow', $translator->translate('Flow'));
    $viewData->addTranslation('line', $translator->translatePlural('Line', 'Lines', 1));

    return $view->render($response, 'subitem/components.html.twig', (array)$viewData);
  }
}
