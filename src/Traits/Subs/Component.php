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
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with(
      'memoryslots',
      'firmware',
      'processors',
      'storages',
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
    $colorTab['firmware'] = 'orange';
    $colorTab['processors'] = 'olive';
    $colorTab['storages'] = 'teal';
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
    foreach ($myItem->memoryslots as $slot)
    {
      $location = '';
      $location_url = '';

      $manufacturer = '';
      $manufacturer_url = '';
      $type = '';
      $type_url = '';
      $serial = '';
      $otherserial = '';
      $state = '';
      $state_url = '';

      if (!is_null($slot->memorymodule))
      {
        if ($slot->memorymodule->manufacturer !== null)
        {
          $manufacturer = $slot->memorymodule->manufacturer->name;
          $manufacturer_url = $this->genereRootUrl2Link(
            $rootUrl2,
            '/dropdowns/manufacturers/',
            $slot->memorymodule->manufacturer->id
          );
        }
        if ($slot->memorymodule->type !== null)
        {
          $type = $slot->memorymodule->type->name;
          $type_url = $this->genereRootUrl2Link(
            $rootUrl2,
            '/dropdowns/memorytype/',
            $slot->memorymodule->type->id
          );
        }
        $serial = $slot->memorymodule->serial;
        $otherserial = $slot->memorymodule->otherserial;

        $status = \App\Models\State::where('id', $slot->memorymodule->state_id)->first();
        if (!is_null($status))
        {
          $state = $status->name;
          $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
        }

        $documents = [];
        if ($slot->memorymodule->documents !== null)
        {
          foreach ($slot->memorymodule->documents as $document)
          {
            $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

            $documents[$document->id] = [
              'name'  => $document->name,
              'url'   => $url,
            ];
          }
        }
        $myMemories[] = [
          'name'                => pgettext('memory device', 'Slot') . ' ' . $slot->slotnumber,
          'is_empty'            => false,
          'manufacturer'        => $manufacturer,
          'manufacturer_url'    => $manufacturer_url,
          'type'                => $type,
          'type_url'            => $type_url,
          'frequence'           => $slot->memorymodule->frequence,
          'size'                => $slot->memorymodule->size,
          'slotnumber'          => $slot->slotnumber,
          'location'            => $location,
          'location_url'        => $location_url,
          'serial'              => $serial,
          'otherserial'         => $otherserial,
          'state'               => $state,
          'state_url'           => $state_url,
          'documents'           => $documents,
          'color'               => $colorTab['memories'],
        ];
      } else {
        $myMemories[] = [
          'name'                => pgettext('memory device', 'Slot') . ' ' . $slot->slotnumber,
          'is_empty'            => true,
          'manufacturer'        => '',
          'manufacturer_url'    => '',
          'type'                => '',
          'type_url'            => '',
          'frequence'           => '',
          'size'                => 0,
          'slotnumber'          => $slot->slotnumber,
          'location'            => '',
          'location_url'        => '',
          'serial'              => '',
          'otherserial'         => '',
          'state'               => '',
          'state_url'           => '',
          'documents'           => [],
          'color'               => $colorTab['memories'],
        ];
      }
    }

    usort($myMemories, function ($item1, $item2)
    {
      return $item1['slotnumber'] <=> $item2['slotnumber'];
    });

    $myFirmware = [];
    // foreach ($myItem->firmware as $firmware)
    // {
    //   $location = '';
    //   $location_url = '';

    //   $loc = \App\Models\Location::where('id', $firmware->getRelationValue('pivot')->location_id)->first();
    //   if (!is_null($loc))
    //   {
    //     $location = $loc->name;
    //     $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
    //   }

    //   $manufacturer = '';
    //   $manufacturer_url = '';
    //   if ($firmware->manufacturer !== null)
    //   {
    //     $manufacturer = $firmware->manufacturer->name;
    //     $manufacturer_url = $this->genereRootUrl2Link(
    //       $rootUrl2,
    //       '/dropdowns/manufacturers/',
    //       $firmware->manufacturer->id
    //     );
    //   }

    //   $serial = $firmware->getRelationValue('pivot')->serial;

    //   $otherserial = $firmware->getRelationValue('pivot')->otherserial;

    //   $state = '';
    //   $state_url = '';
    //   $status = \App\Models\State::where('id', $firmware->getRelationValue('pivot')->state_id)->first();
    //   if ($status !== null)
    //   {
    //     $state = $status->name;
    //     $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
    //   }

    //   $documents = [];
    //   if ($firmware->documents !== null)
    //   {
    //     foreach ($firmware->documents as $document)
    //     {
    //       $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

    //       $documents[$document->id] = [
    //         'name'  => $document->name,
    //         'url'   => $url,
    //       ];
    //     }
    //   }

      // $myFirmware[] = [
      //   'name'                => $firmware->name,
      //   'manufacturer'        => $manufacturer,
      //   'manufacturer_url'    => $manufacturer_url,
      //   'type'                => $type,
      //   'type_url'            => $type_url,
      //   'version'             => $firmware->version,
      //   'date'                => $firmware->date,
      //   'location'            => $location,
      //   'location_url'        => $location_url,
      //   'serial'              => $serial,
      //   'otherserial'         => $otherserial,
      //   'state'               => $state,
      //   'state_url'           => $state_url,
      //   'documents'           => $documents,
      //   'color'               => $colorTab['firmware'],
      // ];
    // }

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

      // $busID = $processor->getRelationValue('pivot')->busID;
      $busID = 0;

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

    $myStorages = [];
    foreach ($myItem->storages as $storage)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::where('id', $storage->location_id)->first();
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($storage->manufacturer !== null)
      {
        $manufacturer = $storage->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $storage->manufacturer->id
        );
      }

      $interface = '';
      $interface_url = '';
      if ($storage->interface !== null)
      {
        $interface = $storage->interface->name;
        $interface_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/interfacetypes/', $storage->interface->id);
      }

      $serial = $storage->serial;

      $otherserial = $storage->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::where('id', $storage->state_id)->first();
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($storage->documents !== null)
      {
        foreach ($storage->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }
      }

      $myStorages[] = [
        'name'                => $storage->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'rpm'                 => $storage->rpm,
        'cache'               => $storage->cache,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'capacity'            => $storage->size,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['storages'],
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
        $write_val = pgettext('global', 'Yes');
      } else {
        $write_val = pgettext('global', 'No');
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
    $viewData->addData('firmware', $myFirmware);
    $viewData->addData('processors', $myProcessors);
    $viewData->addData('storages', $myStorages);
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

    $viewData->addTranslation('memory', pgettext('inventory device', 'Memory size'));
    $viewData->addTranslation('manufacturer', npgettext('global', 'Manufacturer', 'Manufacturers', 1));
    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('frequence', pgettext('global', 'MHz'));
    $viewData->addTranslation(
      'size',
      sprintf('%1$s (%2$s)', pgettext('global', 'Size'), pgettext('global', 'Mio'))
    );
    $viewData->addTranslation('serial', pgettext('inventory device', 'Serial number'));
    $viewData->addTranslation('location', npgettext('global', 'Location', 'Locations', 1));
    $viewData->addTranslation('busID', pgettext('inventory device', 'Position of the device on its bus'));
    $viewData->addTranslation('firmware', npgettext('global', 'Firmware', 'Firmware', 1));
    $viewData->addTranslation('version', npgettext('global', 'Version', 'Versions', 1));
    $viewData->addTranslation('install_date', pgettext('global', 'Installation date'));
    $viewData->addTranslation('processor', npgettext('global', 'Processor', 'Processors', 1));
    $viewData->addTranslation(
      'frequence_mhz',
      sprintf('%1$s (%2$s)', pgettext('global', 'MHz'), pgettext('global', 'MHz'))
    );
    $viewData->addTranslation('nbcores', pgettext('inventory device', 'Number of cores'));
    $viewData->addTranslation('nbthreads', pgettext('inventory device', 'Number of threads'));
    $viewData->addTranslation('storage', npgettext('global', 'Storage', 'Storages', 1));
    $viewData->addTranslation('rpm', pgettext('global', 'RPM'));
    $viewData->addTranslation('cache', pgettext('global', 'Cache'));
    $viewData->addTranslation('interface', pgettext('inventory device', 'Interface'));
    $viewData->addTranslation(
      'capacity',
      sprintf('%1$s (%2$s)', pgettext('battery', 'Capacity'), pgettext('global', 'Mio'))
    );
    $viewData->addTranslation('battery', npgettext('global', 'Battery', 'Batteries', 1));
    $viewData->addTranslation(
      'voltage_mv',
      sprintf('%1$s (%2$s)', pgettext('battery', 'Voltage'), pgettext('battery', 'mV'))
    );
    $viewData->addTranslation(
      'capacity_mwh',
      sprintf('%1$s (%2$s)', pgettext('battery', 'Capacity'), pgettext('battery', 'mWh'))
    );
    $viewData->addTranslation('manufacturing_date', pgettext('inventory device', 'Manufacturing date'));
    $viewData->addTranslation('soundcard', npgettext('global', 'Sound card', 'Sound cards', 1));
    $viewData->addTranslation('controller', npgettext('global', 'Controller', 'Controllers', 1));
    $viewData->addTranslation('documents', npgettext('global', 'Document', 'Documents', 2));
    $viewData->addTranslation('mac_address', pgettext('network', 'MAC address'));
    $viewData->addTranslation('powersupply', npgettext('global', 'Power supply', 'Power supplies', 1));
    $viewData->addTranslation('sensor', npgettext('global', 'Sensor', 'Sensors', 1));
    $viewData->addTranslation('devicepci', npgettext('global', 'PCI device', 'PCI devices', 1));
    $viewData->addTranslation('devicegeneric', npgettext('global', 'Generic device', 'Generic devices', 1));
    $viewData->addTranslation('devicenetworkcard', npgettext('global', 'Network card', 'Network cards', 1));
    $viewData->addTranslation('devicesimcard', npgettext('global', 'SIM card', 'SIM cards', 1));
    $viewData->addTranslation('devicemotherboard', npgettext('global', 'System board', 'System boards', 1));
    $viewData->addTranslation('devicecase', npgettext('global', 'Case', 'Cases', 1));
    $viewData->addTranslation('devicegraphiccard', npgettext('global', 'Graphics card', 'Graphics cards', 1));
    $viewData->addTranslation('devicedrive', npgettext('global', 'Drive', 'Drives', 1));
    $viewData->addTranslation(
      'memory_mio',
      sprintf('%1$s (%2$s)', pgettext('inventory device', 'Memory size'), pgettext('global', 'Mio'))
    );
    $viewData->addTranslation('chipset', pgettext('inventory device', 'Chipset'));
    $viewData->addTranslation('write', pgettext('inventory device', 'Write'));
    $viewData->addTranslation('speed', pgettext('inventory device', 'Speed'));
    $viewData->addTranslation('inventaire_number', pgettext('inventory device', 'Inventory number'));
    $viewData->addTranslation('status', pgettext('inventory device', 'Status'));
    $viewData->addTranslation('msin', pgettext('sim', 'Mobile Subscriber Identification Number'));
    $viewData->addTranslation('user', npgettext('global', 'User', 'Users', 1));
    $viewData->addTranslation('group', npgettext('global', 'Group', 'Groups', 1));
    $viewData->addTranslation('flow', pgettext('inventory device', 'Flow'));
    $viewData->addTranslation('line', npgettext('global', 'Line', 'Lines', 1));

    return $view->render($response, 'subitem/components.html.twig', (array)$viewData);
  }
}
