<?php

declare(strict_types=1);

namespace App;

use Slim\Routing\RouteCollectorProxy;
use Psr\Container\ContainerInterface as TContainerInterface;

final class Route
{
  /**
   * @param \Slim\App<TContainerInterface|null> $app
   */
  public static function setRoutes(&$app): void
  {
    global $basePath;

    $app->redirect('', $basePath . '/view/login');
    $app->redirect('/', $basePath . '/view/login');

    // Enable OPTIONS method for all routes
    $app->options('/{routes:.+}', function ($request, $response, $args)
    {
      return $response;
    });

    // The ping - pong ;)
    // $app->get($prefix . '/ping', \App\v1\Controllers\Ping::class . ':getPing');

    $app->group('/api/v1', function (RouteCollectorProxy $v1)
    {
      $v1->group('/fusioninventory', function (RouteCollectorProxy $fusioninventory)
      {
        $fusioninventory->map(['GET'], '', \App\v1\Controllers\Fusioninventory\Communication::class . ':null');
        $fusioninventory->map(['POST'], '', \App\v1\Controllers\Fusioninventory\Communication::class . ':getConfig');
      });
    });

    $app->group('/view', function (RouteCollectorProxy $view)
    {
      $view->map(['GET'], '/dropdown', \App\v1\Controllers\Dropdown::class . ':getAll');
      $view->map(['GET'], '/dropdown/rule/criteria', \App\v1\Controllers\Dropdown::class . ':getRuleCriteria');
      $view->map(
        ['GET'],
        '/dropdown/rule/criteria/condition',
        \App\v1\Controllers\Dropdown::class . ':getRuleCriteriaCondition'
      );
      $view->map(
        ['GET'],
        '/dropdown/rule/criteria/pattern',
        \App\v1\Controllers\Dropdown::class . ':getRuleCriteriaPattern'
      );
      $view->map(
        ['GET'],
        '/dropdown/rule/actions/field',
        \App\v1\Controllers\Dropdown::class . ':getRuleActionsField'
      );
      $view->map(
        ['GET'],
        '/dropdown/rule/actions/actiontype',
        \App\v1\Controllers\Dropdown::class . ':getRuleActionsType'
      );
      $view->map(
        ['GET'],
        '/dropdown/rule/actions/value',
        \App\v1\Controllers\Dropdown::class . ':getRuleActionsValue'
      );

      $view->group('/login', function (RouteCollectorProxy $login)
      {
        $login->map(['GET'], '', \App\v1\Controllers\Login::class . ':getLogin');
        $login->map(['POST'], '', \App\v1\Controllers\Login::class . ':postLogin');

        $login->group('/sso/{callbackid:[a-z0-9]+}', function (RouteCollectorProxy $loginsso)
        {
          $loginsso->map(['GET'], '', \App\v1\Controllers\Login::class . ':doSSO');
          $loginsso->map(['GET'], '/cb', \App\v1\Controllers\Login::class . ':callbackSSO');
        });
      });

      $view->group('/columns', function (RouteCollectorProxy $columns)
      {
        $columns->map(['GET'], '', \App\v1\Controllers\Displaypreference::class . ':manageColumnsOfModel');
        $columns->map(['POST'], '', \App\v1\Controllers\Displaypreference::class . ':postColumnOfModel');
        $columns->map(['GET'], '/createuser', \App\v1\Controllers\Displaypreference::class . ':viewCreateUserColumn');
        $columns->map(['GET'], '/deleteuser', \App\v1\Controllers\Displaypreference::class . ':viewDeleteUserColumn');
        $columns->group("/{id:[0-9]+}", function (RouteCollectorProxy $columnId)
        {
          $columnId->map(['GET'], '/delete', \App\v1\Controllers\Displaypreference::class . ':deleteColumn');
          $columnId->map(['GET'], '/up', \App\v1\Controllers\Displaypreference::class . ':viewUpColumn');
          $columnId->map(['GET'], '/down', \App\v1\Controllers\Displaypreference::class . ':viewDownColumn');
        });
      });

      $view->map(['GET'], '/logout', \App\v1\Controllers\Login::class . ':logout');
      $view->map(['POST'], '/changeprofileentity', \App\v1\Controllers\Login::class . ':changeProfileEntity');

      $view->map(['GET'], '/home', \App\v1\Controllers\Home::class . ':homepage');
      $view->map(['POST'], '/home/switch', \App\v1\Controllers\Home::class . ':switchHomepage');

      $view->group('/computers', function (RouteCollectorProxy $computers)
      {
        $computers->map(['GET'], '', \App\v1\Controllers\Computer::class . ':showAll');
        $computers->group("/new", function (RouteCollectorProxy $computerNew)
        {
          $computerNew->map(['GET'], '', \App\v1\Controllers\Computer::class . ':showNewItem');
          $computerNew->map(['POST'], '', \App\v1\Controllers\Computer::class . ':newItem');
        });

        $computers->group("/{id:[0-9]+}", function (RouteCollectorProxy $computerId)
        {
          $computerId->map(['GET'], '', \App\v1\Controllers\Computer::class . ':showItem');
          $computerId->map(['POST'], '', \App\v1\Controllers\Computer::class . ':updateItem');

          $computerId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Computer::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Computer::class . ':restoreItem');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Computer::class . ':showSubOperatingSystem');
            $sub->map(['POST'], 'operatingsystem', \App\v1\Controllers\Computer::class . ':saveSubOperatingSystem');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Computer::class . ':showSubSoftwares');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Computer::class . ':showSubComponents');
            $sub->map(['GET'], 'volumes', \App\v1\Controllers\Computer::class . ':showSubVolumes');
            $sub->map(['GET'], 'virtualization', \App\v1\Controllers\Computer::class . ':showSubVirtualization');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Computer::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Computer::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Computer::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Computer::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Computer::class . ':showSubNotes');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Computer::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Computer::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Computer::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Computer::class . ':showSubItil');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Computer::class . ':showSubConnections');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Computer::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Computer::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Computer::class . ':showSubHistory');
          });
        });
      });

      $view->group('/computerantivirus', function (RouteCollectorProxy $computerantivirus)
      {
        $computerantivirus->map(['GET'], '', \App\v1\Controllers\Computerantivirus::class . ':showAll');
        $computerantivirus->group("/new", function (RouteCollectorProxy $caNew)
        {
          $caNew->map(['GET'], '', \App\v1\Controllers\Computerantivirus::class . ':showNewItem');
          $caNew->map(['POST'], '', \App\v1\Controllers\Computerantivirus::class . ':newItem');
        });

        $computerantivirus->group("/{id:[0-9]+}", function (RouteCollectorProxy $computerantivirusId)
        {
          $computerantivirusId->map(['GET'], '', \App\v1\Controllers\Computerantivirus::class . ':showItem');
          $computerantivirusId->map(['POST'], '', \App\v1\Controllers\Computerantivirus::class . ':updateItem');
          $computerantivirusId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Computerantivirus::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Computerantivirus::class . ':restoreItem');
          });
        });
      });

      $view->group('/monitors', function (RouteCollectorProxy $monitors)
      {
        $monitors->map(['GET'], '', \App\v1\Controllers\Monitor::class . ':showAll');
        $monitors->group("/new", function (RouteCollectorProxy $monitorNew)
        {
          $monitorNew->map(['GET'], '', \App\v1\Controllers\Monitor::class . ':showNewItem');
          $monitorNew->map(['POST'], '', \App\v1\Controllers\Monitor::class . ':newItem');
        });

        $monitors->group("/{id:[0-9]+}", function (RouteCollectorProxy $monitorId)
        {
          $monitorId->map(['GET'], '', \App\v1\Controllers\Monitor::class . ':showItem');
          $monitorId->map(['POST'], '', \App\v1\Controllers\Monitor::class . ':updateItem');

          $monitorId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Monitor::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Monitor::class . ':restoreItem');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Monitor::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Monitor::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Monitor::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Monitor::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Monitor::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Monitor::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Monitor::class . ':showSubContracts');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Monitor::class . ':showSubSoftwares');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Monitor::class . ':showSubOperatingSystem');
            $sub->map(['POST'], 'operatingsystem', \App\v1\Controllers\Monitor::class . ':saveSubOperatingSystem');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Monitor::class . ':showSubItil');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Monitor::class . ':showSubConnections');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Monitor::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Monitor::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Monitor::class . ':showSubHistory');
          });
        });
      });
      $view->group('/softwares', function (RouteCollectorProxy $softwares)
      {
        $softwares->map(['GET'], '', \App\v1\Controllers\Software::class . ':showAll');
        $softwares->group("/new", function (RouteCollectorProxy $softwareNew)
        {
          $softwareNew->map(['GET'], '', \App\v1\Controllers\Software::class . ':showNewItem');
          $softwareNew->map(['POST'], '', \App\v1\Controllers\Software::class . ':newItem');
        });

        $softwares->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwareId)
        {
          $softwareId->map(['GET'], '', \App\v1\Controllers\Software::class . ':showItem');
          $softwareId->map(['POST'], '', \App\v1\Controllers\Software::class . ':updateItem');

          $softwareId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Software::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Software::class . ':restoreItem');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Software::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Software::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Software::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Software::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Software::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Software::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Software::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Software::class . ':showSubItil');
            $sub->map(['GET'], 'versions', \App\v1\Controllers\Software::class . ':showSubVersions');
            $sub->map(['GET'], 'licenses', \App\v1\Controllers\Software::class . ':showSubLicenses');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Software::class . ':showSubInfocoms');
            $sub->map(['GET'], 'softwareinstall', \App\v1\Controllers\Software::class . ':showSubSoftwareInstall');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Software::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Software::class . ':showSubHistory');
          });
        });
      });

      $view->group('/softwareversions', function (RouteCollectorProxy $softwareversions)
      {
        $softwareversions->map(['GET'], '', \App\v1\Controllers\Softwareversion::class . ':showAll');
        $softwareversions->group("/new", function (RouteCollectorProxy $sversionNew)
        {
          $sversionNew->map(['GET'], '', \App\v1\Controllers\Softwareversion::class . ':showNewItem');
          $sversionNew->map(['POST'], '', \App\v1\Controllers\Softwareversion::class . ':newItem');
        });

        $softwareversions->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwareversionId)
        {
          $softwareversionId->map(['GET'], '', \App\v1\Controllers\Softwareversion::class . ':showItem');
          $softwareversionId->map(['POST'], '', \App\v1\Controllers\Softwareversion::class . ':updateItem');
          $softwareversionId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Softwareversion::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Softwareversion::class . ':restoreItem');
          });
        });
      });

      $view->group('/networkequipments', function (RouteCollectorProxy $networkequipments)
      {
        $networkequipments->map(['GET'], '', \App\v1\Controllers\Networkequipment::class . ':showAll');
        $networkequipments->group("/new", function (RouteCollectorProxy $networkequipmentNew)
        {
          $networkequipmentNew->map(['GET'], '', \App\v1\Controllers\Networkequipment::class . ':showNewItem');
          $networkequipmentNew->map(['POST'], '', \App\v1\Controllers\Networkequipment::class . ':newItem');
        });

        $networkequipments->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkequipmentId)
        {
          $networkequipmentId->map(['GET'], '', \App\v1\Controllers\Networkequipment::class . ':showItem');
          $networkequipmentId->map(['POST'], '', \App\v1\Controllers\Networkequipment::class . ':updateItem');

          $networkequipmentId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Networkequipment::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Networkequipment::class . ':restoreItem');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Networkequipment::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Networkequipment::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Networkequipment::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Networkequipment::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Networkequipment::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Networkequipment::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Networkequipment::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Networkequipment::class . ':showSubContracts');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Networkequipment::class . ':showSubInfocoms');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Networkequipment::class . ':showSubSoftwares');
            $sub->map(
              ['GET'],
              'operatingsystem',
              \App\v1\Controllers\Networkequipment::class . ':showSubOperatingSystem'
            );
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Networkequipment::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Networkequipment::class . ':showSubComponents');
            $sub->map(['GET'], 'volumes', \App\v1\Controllers\Networkequipment::class . ':showSubVolumes');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Networkequipment::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Networkequipment::class . ':showSubHistory');
          });
        });
      });

      $view->group('/peripherals', function (RouteCollectorProxy $peripherals)
      {
        $peripherals->map(['GET'], '', \App\v1\Controllers\Peripheral::class . ':showAll');
        $peripherals->group("/new", function (RouteCollectorProxy $peripheralNew)
        {
          $peripheralNew->map(['GET'], '', \App\v1\Controllers\Peripheral::class . ':showNewItem');
          $peripheralNew->map(['POST'], '', \App\v1\Controllers\Peripheral::class . ':newItem');
        });

        $peripherals->group("/{id:[0-9]+}", function (RouteCollectorProxy $peripheralId)
        {
          $peripheralId->map(['GET'], '', \App\v1\Controllers\Peripheral::class . ':showItem');
          $peripheralId->map(['POST'], '', \App\v1\Controllers\Peripheral::class . ':updateItem');

          $peripheralId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Peripheral::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Peripheral::class . ':restoreItem');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Peripheral::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Peripheral::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Peripheral::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Peripheral::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Peripheral::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Peripheral::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Peripheral::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Peripheral::class . ':showSubContracts');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Peripheral::class . ':showSubSoftwares');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Peripheral::class . ':showSubOperatingSystem');
            $sub->map(['POST'], 'operatingsystem', \App\v1\Controllers\Peripheral::class . ':saveSubOperatingSystem');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Peripheral::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Peripheral::class . ':showSubComponents');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Peripheral::class . ':showSubConnections');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Peripheral::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Peripheral::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Peripheral::class . ':showSubHistory');
          });
        });
      });

      $view->group('/printers', function (RouteCollectorProxy $printers)
      {
        $printers->map(['GET'], '', \App\v1\Controllers\Printer::class . ':showAll');
        $printers->group("/new", function (RouteCollectorProxy $printerNew)
        {
          $printerNew->map(['GET'], '', \App\v1\Controllers\Printer::class . ':showNewItem');
          $printerNew->map(['POST'], '', \App\v1\Controllers\Printer::class . ':newItem');
        });

        $printers->group("/{id:[0-9]+}", function (RouteCollectorProxy $printerId)
        {
          $printerId->map(['GET'], '', \App\v1\Controllers\Printer::class . ':showItem');
          $printerId->map(['POST'], '', \App\v1\Controllers\Printer::class . ':updateItem');

          $printerId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Printer::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Printer::class . ':restoreItem');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Printer::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Printer::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Printer::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Printer::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Printer::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Printer::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Printer::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Printer::class . ':showSubContracts');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Printer::class . ':showSubSoftwares');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Printer::class . ':showSubOperatingSystem');
            $sub->map(['POST'], 'operatingsystem', \App\v1\Controllers\Printer::class . ':saveSubOperatingSystem');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Printer::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Printer::class . ':showSubComponents');
            $sub->map(['GET'], 'volumes', \App\v1\Controllers\Printer::class . ':showSubVolumes');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Printer::class . ':showSubConnections');
            $sub->map(['GET'], 'cartridges', \App\v1\Controllers\Printer::class . ':showSubCartridges');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Printer::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Printer::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Printer::class . ':showSubHistory');
          });
        });
      });
      $view->group('/cartridgeitems', function (RouteCollectorProxy $cartridgeitems)
      {
        $cartridgeitems->map(['GET'], '', \App\v1\Controllers\Cartridgeitem::class . ':showAll');
        $cartridgeitems->group("/new", function (RouteCollectorProxy $cartridgeitemNew)
        {
          $cartridgeitemNew->map(['GET'], '', \App\v1\Controllers\Cartridgeitem::class . ':showNewItem');
          $cartridgeitemNew->map(['POST'], '', \App\v1\Controllers\Cartridgeitem::class . ':newItem');
        });

        $cartridgeitems->group("/{id:[0-9]+}", function (RouteCollectorProxy $cartridgeitemId)
        {
          $cartridgeitemId->map(['GET'], '', \App\v1\Controllers\Cartridgeitem::class . ':showItem');
          $cartridgeitemId->map(['POST'], '', \App\v1\Controllers\Cartridgeitem::class . ':updateItem');

          $cartridgeitemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Cartridgeitem::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Cartridgeitem::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Cartridgeitem::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Cartridgeitem::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Cartridgeitem::class . ':showSubDocuments');
            $sub->map(['GET'], 'cartridges', \App\v1\Controllers\Cartridgeitem::class . ':showSubCartridges');
            $sub->map(['GET'], 'printermodels', \App\v1\Controllers\Cartridgeitem::class . ':showSubPrintermodels');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Cartridgeitem::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Cartridgeitem::class . ':showSubHistory');
          });
        });
      });
      $view->group('/consumableitems', function (RouteCollectorProxy $consumableitems)
      {
        $consumableitems->map(['GET'], '', \App\v1\Controllers\Consumableitem::class . ':showAll');
        $consumableitems->group("/new", function (RouteCollectorProxy $consumableitemNew)
        {
          $consumableitemNew->map(['GET'], '', \App\v1\Controllers\Consumableitem::class . ':showNewItem');
          $consumableitemNew->map(['POST'], '', \App\v1\Controllers\Consumableitem::class . ':newItem');
        });

        $consumableitems->group("/{id:[0-9]+}", function (RouteCollectorProxy $consumableitemId)
        {
          $consumableitemId->map(['GET'], '', \App\v1\Controllers\Consumableitem::class . ':showItem');
          $consumableitemId->map(['POST'], '', \App\v1\Controllers\Consumableitem::class . ':updateItem');
          $consumableitemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Consumableitem::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Consumableitem::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Consumableitem::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Consumableitem::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Consumableitem::class . ':showSubDocuments');
            $sub->map(['GET'], 'consumables', \App\v1\Controllers\Consumableitem::class . ':showSubConsumables');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Consumableitem::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Consumableitem::class . ':showSubHistory');
          });
        });
      });

      $view->group('/phones', function (RouteCollectorProxy $phones)
      {
        $phones->map(['GET'], '', \App\v1\Controllers\Phone::class . ':showAll');
        $phones->group("/new", function (RouteCollectorProxy $phoneNew)
        {
          $phoneNew->map(['GET'], '', \App\v1\Controllers\Phone::class . ':showNewItem');
          $phoneNew->map(['POST'], '', \App\v1\Controllers\Phone::class . ':newItem');
        });

        $phones->group("/{id:[0-9]+}", function (RouteCollectorProxy $phoneId)
        {
          $phoneId->map(['GET'], '', \App\v1\Controllers\Phone::class . ':showItem');
          $phoneId->map(['POST'], '', \App\v1\Controllers\Phone::class . ':updateItem');

          $phoneId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Phone::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Phone::class . ':restoreItem');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Phone::class . ':showSubDomains');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Phone::class . ':showSubAppliances');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Phone::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Phone::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Phone::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Phone::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Phone::class . ':showSubContracts');
            $sub->map(['GET'], 'softwares', \App\v1\Controllers\Phone::class . ':showSubSoftwares');
            $sub->map(['GET'], 'operatingsystem', \App\v1\Controllers\Phone::class . ':showSubOperatingSystem');
            $sub->map(['POST'], 'operatingsystem', \App\v1\Controllers\Phone::class . ':saveSubOperatingSystem');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Phone::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Phone::class . ':showSubComponents');
            $sub->map(['GET'], 'volumes', \App\v1\Controllers\Phone::class . ':showSubVolumes');
            $sub->map(['GET'], 'connections', \App\v1\Controllers\Phone::class . ':showSubConnections');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Phone::class . ':showSubInfocoms');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\Phone::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Phone::class . ':showSubHistory');
          });
        });
      });

      $view->group('/racks', function (RouteCollectorProxy $racks)
      {
        $racks->map(['GET'], '', \App\v1\Controllers\Rack::class . ':showAll');
        $racks->group("/new", function (RouteCollectorProxy $rackNew)
        {
          $rackNew->map(['GET'], '', \App\v1\Controllers\Rack::class . ':showNewItem');
          $rackNew->map(['POST'], '', \App\v1\Controllers\Rack::class . ':newItem');
        });

        $racks->group("/{id:[0-9]+}", function (RouteCollectorProxy $rackId)
        {
          $rackId->map(['GET'], '', \App\v1\Controllers\Rack::class . ':showItem');
          $rackId->map(['POST'], '', \App\v1\Controllers\Rack::class . ':updateItem');
          $rackId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Rack::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Rack::class . ':restoreItem');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Rack::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Rack::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Rack::class . ':showSubItil');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Rack::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Rack::class . ':showSubHistory');
          });
        });
      });

      $view->group('/enclosures', function (RouteCollectorProxy $enclosures)
      {
        $enclosures->map(['GET'], '', \App\v1\Controllers\Enclosure::class . ':showAll');
        $enclosures->group("/new", function (RouteCollectorProxy $encNew)
        {
          $encNew->map(['GET'], '', \App\v1\Controllers\Enclosure::class . ':showNewItem');
          $encNew->map(['POST'], '', \App\v1\Controllers\Enclosure::class . ':newItem');
        });

        $enclosures->group("/{id:[0-9]+}", function (RouteCollectorProxy $enclosureId)
        {
          $enclosureId->map(['GET'], '', \App\v1\Controllers\Enclosure::class . ':showItem');
          $enclosureId->map(['POST'], '', \App\v1\Controllers\Enclosure::class . ':updateItem');
          $enclosureId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Enclosure::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Enclosure::class . ':restoreItem');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Enclosure::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Enclosure::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Enclosure::class . ':showSubItil');
            $sub->map(['GET'], 'components', \App\v1\Controllers\Enclosure::class . ':showSubComponents');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Enclosure::class . ':showSubItems');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Enclosure::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Enclosure::class . ':showSubHistory');
          });
        });
      });

      $view->group('/pdus', function (RouteCollectorProxy $pdus)
      {
        $pdus->map(['GET'], '', \App\v1\Controllers\Pdu::class . ':showAll');
        $pdus->group("/new", function (RouteCollectorProxy $pduNew)
        {
          $pduNew->map(['GET'], '', \App\v1\Controllers\Pdu::class . ':showNewItem');
          $pduNew->map(['POST'], '', \App\v1\Controllers\Pdu::class . ':newItem');
        });

        $pdus->group("/{id:[0-9]+}", function (RouteCollectorProxy $pduId)
        {
          $pduId->map(['GET'], '', \App\v1\Controllers\Pdu::class . ':showItem');
          $pduId->map(['POST'], '', \App\v1\Controllers\Pdu::class . ':updateItem');
          $pduId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Pdu::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Pdu::class . ':restoreItem');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Pdu::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Pdu::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Pdu::class . ':showSubItil');
            $sub->map(['GET'], 'plugs', \App\v1\Controllers\Pdu::class . ':showSubPlugs');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Pdu::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Pdu::class . ':showSubHistory');
          });
        });
      });

      $view->group('/passivedcequipments', function (RouteCollectorProxy $passivedcequipments)
      {
        $passivedcequipments->map(['GET'], '', \App\v1\Controllers\Passivedcequipment::class . ':showAll');
        $passivedcequipments->group("/new", function (RouteCollectorProxy $passivedNew)
        {
          $passivedNew->map(['GET'], '', \App\v1\Controllers\Passivedcequipment::class . ':showNewItem');
          $passivedNew->map(['POST'], '', \App\v1\Controllers\Passivedcequipment::class . ':newItem');
        });

        $passivedcequipments->group("/{id:[0-9]+}", function (RouteCollectorProxy $passivedcequipmentId)
        {
          $passivedcequipmentId->map(['GET'], '', \App\v1\Controllers\Passivedcequipment::class . ':showItem');
          $passivedcequipmentId->map(['POST'], '', \App\v1\Controllers\Passivedcequipment::class . ':updateItem');
          $passivedcequipmentId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Passivedcequipment::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Passivedcequipment::class . ':restoreItem');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Passivedcequipment::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Passivedcequipment::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Passivedcequipment::class . ':showSubItil');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Passivedcequipment::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Passivedcequipment::class . ':showSubHistory');
          });
        });
      });

      $view->group('/itemdevicesimcards', function (RouteCollectorProxy $item_devicesimcards)
      {
        $item_devicesimcards->map(['GET'], '', \App\v1\Controllers\ItemDevicesimcard::class . ':showAll');
        $item_devicesimcards->map(['POST'], '', \App\v1\Controllers\ItemDevicesimcard::class . ':postItem');
        $item_devicesimcards->group("/{id:[0-9]+}", function (RouteCollectorProxy $item_devicesimcardId)
        {
          $item_devicesimcardId->map(['GET'], '', \App\v1\Controllers\ItemDevicesimcard::class . ':showItem');
          $item_devicesimcardId->map(['POST'], '', \App\v1\Controllers\ItemDevicesimcard::class . ':updateItem');
          $item_devicesimcardId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'documents', \App\v1\Controllers\ItemDevicesimcard::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\ItemDevicesimcard::class . ':showSubContracts');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\ItemDevicesimcard::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\ItemDevicesimcard::class . ':showSubHistory');
          });
        });
      });

      $view->group('/tickets', function (RouteCollectorProxy $tickets)
      {
        $tickets->map(['GET'], '', \App\v1\Controllers\Ticket::class . ':showAll');
        $tickets->group("/new", function (RouteCollectorProxy $ticketNew)
        {
          $ticketNew->map(['GET'], '', \App\v1\Controllers\Ticket::class . ':showNewItem');
          $ticketNew->map(['POST'], '', \App\v1\Controllers\Ticket::class . ':newItem');
        });

        $tickets->group("/{id:[0-9]+}", function (RouteCollectorProxy $ticketId)
        {
          $ticketId->map(['GET'], '', \App\v1\Controllers\Ticket::class . ':showItem');
          $ticketId->map(['POST'], '', \App\v1\Controllers\Ticket::class . ':updateItem');
          $ticketId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Ticket::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Ticket::class . ':restoreItem');
            $sub->map(['GET'], 'criteria', \App\v1\Controllers\Rules\Ticket::class . ':showCriteria');
            $sub->map(['POST'], 'followups', \App\v1\Controllers\Followup::class . ':postItem');
            $sub->map(['POST'], 'solutions', \App\v1\Controllers\Solution::class . ':postItem');
            $sub->map(
              ['GET'],
              'solutions/{solutionid:[0-9]+}/accept',
              \App\v1\Controllers\Solution::class . ':postAccept'
            );
            $sub->map(
              ['GET'],
              'solutions/{solutionid:[0-9]+}/refuse',
              \App\v1\Controllers\Solution::class . ':postRefuse'
            );
            $sub->map(['GET'], 'stats', \App\v1\Controllers\Ticket::class . ':showStats');
            $sub->map(['GET'], 'problem', \App\v1\Controllers\Ticket::class . ':showProblem');
            $sub->map(['POST'], 'problem', \App\v1\Controllers\Ticket::class . ':postProblem');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Ticket::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Ticket::class . ':showSubCosts');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Ticket::class . ':showSubItems');
            $sub->map(['GET'], 'projects', \App\v1\Controllers\Ticket::class . ':showSubProjects');
            $sub->map(['GET'], 'projecttasks', \App\v1\Controllers\Ticket::class . ':showSubProjecttasks');
            $sub->map(['GET'], 'changes', \App\v1\Controllers\Ticket::class . ':showSubChanges');
            $sub->map(['GET'], 'approvals', \App\v1\Controllers\Ticket::class . ':showSubApprovals');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Ticket::class . ':showSubHistory');
          });
        });
      });
      $view->group('/problems', function (RouteCollectorProxy $problems)
      {
        $problems->map(['GET'], '', \App\v1\Controllers\Problem::class . ':showAll');
        $problems->group("/new", function (RouteCollectorProxy $problemNew)
        {
          $problemNew->map(['GET'], '', \App\v1\Controllers\Problem::class . ':showNewItem');
          $problemNew->map(['POST'], '', \App\v1\Controllers\Problem::class . ':newItem');
        });


        $problems->group("/{id:[0-9]+}", function (RouteCollectorProxy $problemId)
        {
          $problemId->map(['GET'], '', \App\v1\Controllers\Problem::class . ':showItem');
          $problemId->map(['POST'], '', \App\v1\Controllers\Problem::class . ':updateItem');
          $problemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Problem::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Problem::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Problem::class . ':showSubNotes');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Problem::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'changes', \App\v1\Controllers\Problem::class . ':showSubChanges');
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Problem::class . ':showSubCosts');
            $sub->map(['GET'], 'projects', \App\v1\Controllers\Problem::class . ':showSubProjects');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\Problem::class . ':showSubTickets');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Problem::class . ':showSubItems');
            $sub->map(['GET'], 'stats', \App\v1\Controllers\Problem::class . ':showStats');
            $sub->map(['GET'], 'analysis', \App\v1\Controllers\Problem::class . ':showAnalysis');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Problem::class . ':showSubHistory');
          });
        });
      });
      $view->group('/changes', function (RouteCollectorProxy $changes)
      {
        $changes->map(['GET'], '', \App\v1\Controllers\Change::class . ':showAll');
        $changes->group("/new", function (RouteCollectorProxy $changeNew)
        {
          $changeNew->map(['GET'], '', \App\v1\Controllers\Change::class . ':showNewItem');
          $changeNew->map(['POST'], '', \App\v1\Controllers\Change::class . ':newItem');
        });

        $changes->group("/{id:[0-9]+}", function (RouteCollectorProxy $changeId)
        {
          $changeId->map(['GET'], '', \App\v1\Controllers\Change::class . ':showItem');
          $changeId->map(['POST'], '', \App\v1\Controllers\Change::class . ':updateItem');
          $changeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Change::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Change::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Change::class . ':showSubNotes');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Change::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Change::class . ':showSubCosts');
            $sub->map(['GET'], 'projects', \App\v1\Controllers\Change::class . ':showSubProjects');
            $sub->map(['GET'], 'problem', \App\v1\Controllers\Change::class . ':showProblem');
            $sub->map(['POST'], 'problem', \App\v1\Controllers\Change::class . ':postProblem');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\Change::class . ':showSubTickets');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Change::class . ':showSubItems');
            $sub->map(['GET'], 'stats', \App\v1\Controllers\Change::class . ':showStats');
            $sub->map(['GET'], 'analysis', \App\v1\Controllers\Change::class . ':showAnalysis');
            $sub->map(['GET'], 'plans', \App\v1\Controllers\Change::class . ':showPlans');
            $sub->map(['GET'], 'approvals', \App\v1\Controllers\Change::class . ':showSubApprovals');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Change::class . ':showSubHistory');
          });
        });
      });

      $view->group('/ticketrecurrents', function (RouteCollectorProxy $ticketrecurrents)
      {
        $ticketrecurrents->map(['GET'], '', \App\v1\Controllers\Ticketrecurrent::class . ':showAll');
        $ticketrecurrents->group("/new", function (RouteCollectorProxy $trecurNew)
        {
          $trecurNew->map(['GET'], '', \App\v1\Controllers\Ticketrecurrent::class . ':showNewItem');
          $trecurNew->map(['POST'], '', \App\v1\Controllers\Ticketrecurrent::class . ':newItem');
        });

        $ticketrecurrents->group("/{id:[0-9]+}", function (RouteCollectorProxy $ticketrecurrentId)
        {
          $ticketrecurrentId->map(['GET'], '', \App\v1\Controllers\Ticketrecurrent::class . ':showItem');
          $ticketrecurrentId->map(['POST'], '', \App\v1\Controllers\Ticketrecurrent::class . ':updateItem');
          $ticketrecurrentId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Ticketrecurrent::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Ticketrecurrent::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Ticketrecurrent::class . ':showSubHistory');
          });
        });
      });

      $view->group('/softwarelicenses', function (RouteCollectorProxy $softwarelicenses)
      {
        $softwarelicenses->map(['GET'], '', \App\v1\Controllers\Softwarelicense::class . ':showAll');
        $softwarelicenses->group("/new", function (RouteCollectorProxy $slicenseNew)
        {
          $slicenseNew->map(['GET'], '', \App\v1\Controllers\Softwarelicense::class . ':showNewItem');
          $slicenseNew->map(['POST'], '', \App\v1\Controllers\Softwarelicense::class . ':newItem');
        });

        $softwarelicenses->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwarelicenseId)
        {
          $softwarelicenseId->map(['GET'], '', \App\v1\Controllers\Softwarelicense::class . ':showItem');
          $softwarelicenseId->map(['POST'], '', \App\v1\Controllers\Softwarelicense::class . ':updateItem');
          $softwarelicenseId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Softwarelicense::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Softwarelicense::class . ':restoreItem');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Softwarelicense::class . ':showSubCertificates');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Softwarelicense::class . ':showSubNotes');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Softwarelicense::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Softwarelicense::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Softwarelicense::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Softwarelicense::class . ':showSubItil');
            $sub->map(['GET'], 'licenses', \App\v1\Controllers\Softwarelicense::class . ':showSubSoftwarelicenses');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Softwarelicense::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Softwarelicense::class . ':showSubHistory');
          });
        });
      });
      $view->group('/budgets', function (RouteCollectorProxy $budgets)
      {
        $budgets->map(['GET'], '', \App\v1\Controllers\Budget::class . ':showAll');
        $budgets->group("/new", function (RouteCollectorProxy $budgetNew)
        {
          $budgetNew->map(['GET'], '', \App\v1\Controllers\Budget::class . ':showNewItem');
          $budgetNew->map(['POST'], '', \App\v1\Controllers\Budget::class . ':newItem');
        });

        $budgets->group("/{id:[0-9]+}", function (RouteCollectorProxy $budgetId)
        {
          $budgetId->map(['GET'], '', \App\v1\Controllers\Budget::class . ':showItem');
          $budgetId->map(['POST'], '', \App\v1\Controllers\Budget::class . ':updateItem');
          $budgetId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Budget::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Budget::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Budget::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Budget::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Budget::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Budget::class . ':showSubDocuments');
            $sub->map(['GET'], 'attacheditems', \App\v1\Controllers\Budget::class . ':showSubAttachedItems');
            $sub->map(['GET'], 'budgetmain', \App\v1\Controllers\Budget::class . ':showSubBudgetMain');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Budget::class . ':showSubHistory');
          });
        });
      });

      $view->group('/suppliers', function (RouteCollectorProxy $suppliers)
      {
        $suppliers->map(['GET'], '', \App\v1\Controllers\Supplier::class . ':showAll');
        $suppliers->group("/new", function (RouteCollectorProxy $supplierNew)
        {
          $supplierNew->map(['GET'], '', \App\v1\Controllers\Supplier::class . ':showNewItem');
          $supplierNew->map(['POST'], '', \App\v1\Controllers\Supplier::class . ':newItem');
        });

        $suppliers->group("/{id:[0-9]+}", function (RouteCollectorProxy $supplierId)
        {
          $supplierId->map(['GET'], '', \App\v1\Controllers\Supplier::class . ':showItem');
          $supplierId->map(['POST'], '', \App\v1\Controllers\Supplier::class . ':updateItem');
          $supplierId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Supplier::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Supplier::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Supplier::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Supplier::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Supplier::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Supplier::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Supplier::class . ':showSubContracts');
            $sub->map(['GET'], 'contacts', \App\v1\Controllers\Supplier::class . ':showSubContacts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Supplier::class . ':showSubItil');
            $sub->map(['GET'], 'attacheditems', \App\v1\Controllers\Supplier::class . ':showSubAttachedItems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Supplier::class . ':showSubHistory');
          });
        });
      });

      $view->group('/contacts', function (RouteCollectorProxy $contacts)
      {
        $contacts->map(['GET'], '', \App\v1\Controllers\Contact::class . ':showAll');
        $contacts->group("/new", function (RouteCollectorProxy $contactNew)
        {
          $contactNew->map(['GET'], '', \App\v1\Controllers\Contact::class . ':showNewItem');
          $contactNew->map(['POST'], '', \App\v1\Controllers\Contact::class . ':newItem');
        });

        $contacts->group("/{id:[0-9]+}", function (RouteCollectorProxy $contactId)
        {
          $contactId->map(['GET'], '', \App\v1\Controllers\Contact::class . ':showItem');
          $contactId->map(['POST'], '', \App\v1\Controllers\Contact::class . ':updateItem');
          $contactId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Contact::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Contact::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Contact::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Contact::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Contact::class . ':showSubDocuments');
            $sub->map(['GET'], 'suppliers', \App\v1\Controllers\Contact::class . ':showSubSuppliers');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Contact::class . ':showSubHistory');
          });
        });
      });

      $view->group('/contracts', function (RouteCollectorProxy $contracts)
      {
        $contracts->map(['GET'], '', \App\v1\Controllers\Contract::class . ':showAll');
        $contracts->group("/new", function (RouteCollectorProxy $contractNew)
        {
          $contractNew->map(['GET'], '', \App\v1\Controllers\Contract::class . ':showNewItem');
          $contractNew->map(['POST'], '', \App\v1\Controllers\Contract::class . ':newItem');
        });

        $contracts->group("/{id:[0-9]+}", function (RouteCollectorProxy $contractId)
        {
          $contractId->map(['GET'], '', \App\v1\Controllers\Contract::class . ':showItem');
          $contractId->map(['POST'], '', \App\v1\Controllers\Contract::class . ':updateItem');
          $contractId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Contract::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Contract::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Contract::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Contract::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Contract::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Contract::class . ':showSubDocuments');
            $sub->map(['GET'], 'suppliers', \App\v1\Controllers\Contract::class . ':showSubSuppliers');
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Contract::class . ':showSubCosts');
            $sub->map(['GET'], 'attacheditems', \App\v1\Controllers\Contract::class . ':showSubAttachedItems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Contract::class . ':showSubHistory');
          });
        });
      });
      $view->group('/documents', function (RouteCollectorProxy $documents)
      {
        $documents->map(['GET'], '', \App\v1\Controllers\Document::class . ':showAll');
        $documents->map(['POST'], '', \App\v1\Controllers\Document::class . ':postItem');
        $documents->group("/{id:[0-9]+}", function (RouteCollectorProxy $documentId)
        {
          $documentId->map(['GET'], '', \App\v1\Controllers\Document::class . ':showItem');
          $documentId->map(['POST'], '', \App\v1\Controllers\Document::class . ':updateItem');
          $documentId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Document::class . ':showSubNotes');
            $sub->map(['GET'], 'associateditems', \App\v1\Controllers\Document::class . ':showSubAssociatedItems');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Document::class . ':showSubDocuments');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Document::class . ':showSubHistory');
          });
        });
      });

      $view->group('/knowledgebasearticles', function (RouteCollectorProxy $articles)
      {
        $articles->map(['GET'], '', \App\v1\Controllers\Knowledgebasearticle::class . ':showAll');
        $articles->group("/new", function (RouteCollectorProxy $articleNew)
        {
          $articleNew->map(['GET'], '', \App\v1\Controllers\Knowledgebasearticle::class . ':showNewItem');
          $articleNew->map(['POST'], '', \App\v1\Controllers\Knowledgebasearticle::class . ':newItem');
        });

        $articles->map(['GET'], '/read/{id:[0-9]+}', \App\v1\Controllers\Knowledgebasearticle::class . ':showReadItem');

        $articles->group("/{id:[0-9]+}", function (RouteCollectorProxy $articleId)
        {
          $articleId->map(['GET'], '', \App\v1\Controllers\Knowledgebasearticle::class . ':showItem');
          $articleId->map(['POST'], '', \App\v1\Controllers\Knowledgebasearticle::class . ':updateItem');

          $articleId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Knowledgebasearticle::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Knowledgebasearticle::class . ':restoreItem');
            $sub->map(['GET'], 'entityview', \App\v1\Controllers\Knowledgebasearticle::class . ':showSubEntityview');
            $sub->map(['POST'], 'entityview', \App\v1\Controllers\Knowledgebasearticle::class . ':newSubEntityview');
            $sub->map(
              ['GET'],
              'entityview/delete/{entityid:[0-9]+}',
              \App\v1\Controllers\Knowledgebasearticle::class . ':deleteSubEntityview'
            );

            $sub->map(['GET'], 'groupview', \App\v1\Controllers\Knowledgebasearticle::class . ':showSubGroupview');
            $sub->map(['POST'], 'groupview', \App\v1\Controllers\Knowledgebasearticle::class . ':newSubGroupview');
            $sub->map(
              ['GET'],
              'groupview/delete/{groupid:[0-9]+}',
              \App\v1\Controllers\Knowledgebasearticle::class . ':deleteSubGroupview'
            );

            $sub->map(['GET'], 'profileview', \App\v1\Controllers\Knowledgebasearticle::class . ':showSubProfileview');
            $sub->map(['POST'], 'profileview', \App\v1\Controllers\Knowledgebasearticle::class . ':newSubProfileview');
            $sub->map(
              ['GET'],
              'profileview/delete/{profileid:[0-9]+}',
              \App\v1\Controllers\Knowledgebasearticle::class . ':deleteSubProfileview'
            );

            $sub->map(['GET'], 'userview', \App\v1\Controllers\Knowledgebasearticle::class . ':showSubUserview');
            $sub->map(['POST'], 'userview', \App\v1\Controllers\Knowledgebasearticle::class . ':newSubUserview');
            $sub->map(
              ['GET'],
              'userview/delete/{userid:[0-9]+}',
              \App\v1\Controllers\Knowledgebasearticle::class . ':deleteSubUserview'
            );

            $sub->map(['GET'], 'revisions', \App\v1\Controllers\Knowledgebasearticle::class . ':showSubRevisions');
            $sub->map(
              ['GET'],
              'revisions/{revisionid:[0-9]+}',
              \App\v1\Controllers\Knowledgebasearticle::class . ':showSubRevisions'
            );
            $sub->map(['GET'], 'history', \App\v1\Controllers\Knowledgebasearticle::class . ':showSubHistory');
          });
        });
      });

      $view->group('/lines', function (RouteCollectorProxy $lines)
      {
        $lines->map(['GET'], '', \App\v1\Controllers\Line::class . ':showAll');
        $lines->group("/new", function (RouteCollectorProxy $lineNew)
        {
          $lineNew->map(['GET'], '', \App\v1\Controllers\Line::class . ':showNewItem');
          $lineNew->map(['POST'], '', \App\v1\Controllers\Line::class . ':newItem');
        });

        $lines->group("/{id:[0-9]+}", function (RouteCollectorProxy $lineId)
        {
          $lineId->map(['GET'], '', \App\v1\Controllers\Line::class . ':showItem');
          $lineId->map(['POST'], '', \App\v1\Controllers\Line::class . ':updateItem');
          $lineId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Line::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Line::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Line::class . ':showSubNotes');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Line::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Line::class . ':showSubContracts');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Line::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Line::class . ':showSubHistory');
          });
        });
      });

      $view->group('/certificates', function (RouteCollectorProxy $certificates)
      {
        $certificates->map(['GET'], '', \App\v1\Controllers\Certificate::class . ':showAll');
        $certificates->group("/new", function (RouteCollectorProxy $certNew)
        {
          $certNew->map(['GET'], '', \App\v1\Controllers\Certificate::class . ':showNewItem');
          $certNew->map(['POST'], '', \App\v1\Controllers\Certificate::class . ':newItem');
        });

        $certificates->group("/{id:[0-9]+}", function (RouteCollectorProxy $certificateId)
        {
          $certificateId->map(['GET'], '', \App\v1\Controllers\Certificate::class . ':showItem');
          $certificateId->map(['POST'], '', \App\v1\Controllers\Certificate::class . ':updateItem');
          $certificateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Certificate::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Certificate::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Certificate::class . ':showSubNotes');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Certificate::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Certificate::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Certificate::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Certificate::class . ':showSubContracts');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Certificate::class . ':showSubDomains');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Certificate::class . ':showSubItil');
            $sub->map(['GET'], 'associateditems', \App\v1\Controllers\Certificate::class . ':showSubAssociatedItems');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Certificate::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Certificate::class . ':showSubHistory');
          });
        });
      });

      $view->group('/datacenters', function (RouteCollectorProxy $datacenters)
      {
        $datacenters->map(['GET'], '', \App\v1\Controllers\Datacenter::class . ':showAll');
        $datacenters->group("/new", function (RouteCollectorProxy $datacenterNew)
        {
          $datacenterNew->map(['GET'], '', \App\v1\Controllers\Datacenter::class . ':showNewItem');
          $datacenterNew->map(['POST'], '', \App\v1\Controllers\Datacenter::class . ':newItem');
        });

        $datacenters->group("/{id:[0-9]+}", function (RouteCollectorProxy $datacenterId)
        {
          $datacenterId->map(['GET'], '', \App\v1\Controllers\Datacenter::class . ':showItem');
          $datacenterId->map(['POST'], '', \App\v1\Controllers\Datacenter::class . ':updateItem');
          $datacenterId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Datacenter::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Datacenter::class . ':restoreItem');
            $sub->map(['GET'], 'dcrooms', \App\v1\Controllers\Datacenter::class . ':showSubDcrooms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Datacenter::class . ':showSubHistory');
          });
        });
      });

      $view->group('/dcrooms', function (RouteCollectorProxy $dcrooms)
      {
        $dcrooms->map(['GET'], '', \App\v1\Controllers\Dcroom::class . ':showAll');
        $dcrooms->group("/new", function (RouteCollectorProxy $dcroomNew)
        {
          $dcroomNew->map(['GET'], '', \App\v1\Controllers\Dcroom::class . ':showNewItem');
          $dcroomNew->map(['POST'], '', \App\v1\Controllers\Dcroom::class . ':newItem');
        });

        $dcrooms->group("/{id:[0-9]+}", function (RouteCollectorProxy $dcroomId)
        {
          $dcroomId->map(['GET'], '', \App\v1\Controllers\Dcroom::class . ':showItem');
          $dcroomId->map(['POST'], '', \App\v1\Controllers\Dcroom::class . ':updateItem');
          $dcroomId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Dcroom::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Dcroom::class . ':restoreItem');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Dcroom::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Dcroom::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Dcroom::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Dcroom::class . ':showSubItil');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Dcroom::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Dcroom::class . ':showSubHistory');
          });
        });
      });

      $view->group('/clusters', function (RouteCollectorProxy $clusters)
      {
        $clusters->map(['GET'], '', \App\v1\Controllers\Cluster::class . ':showAll');
        $clusters->group("/new", function (RouteCollectorProxy $clusterNew)
        {
          $clusterNew->map(['GET'], '', \App\v1\Controllers\Cluster::class . ':showNewItem');
          $clusterNew->map(['POST'], '', \App\v1\Controllers\Cluster::class . ':newItem');
        });

        $clusters->group("/{id:[0-9]+}", function (RouteCollectorProxy $clusterId)
        {
          $clusterId->map(['GET'], '', \App\v1\Controllers\Cluster::class . ':showItem');
          $clusterId->map(['POST'], '', \App\v1\Controllers\Cluster::class . ':updateItem');
          $clusterId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Cluster::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Cluster::class . ':restoreItem');
            $sub->map(['GET'], 'appliances', \App\v1\Controllers\Cluster::class . ':showSubAppliances');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Cluster::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Cluster::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Cluster::class . ':showSubItil');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Cluster::class . ':showSubItems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Cluster::class . ':showSubHistory');
          });
        });
      });

      $view->group('/domains', function (RouteCollectorProxy $domains)
      {
        $domains->map(['GET'], '', \App\v1\Controllers\Domain::class . ':showAll');
        $domains->group("/new", function (RouteCollectorProxy $domainNew)
        {
          $domainNew->map(['GET'], '', \App\v1\Controllers\Domain::class . ':showNewItem');
          $domainNew->map(['POST'], '', \App\v1\Controllers\Domain::class . ':newItem');
        });

        $domains->group("/{id:[0-9]+}", function (RouteCollectorProxy $domainId)
        {
          $domainId->map(['GET'], '', \App\v1\Controllers\Domain::class . ':showItem');
          $domainId->map(['POST'], '', \App\v1\Controllers\Domain::class . ':updateItem');
          $domainId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Domain::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Domain::class . ':restoreItem');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Domain::class . ':showSubCertificates');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Domain::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Domain::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Domain::class . ':showSubContracts');
            $sub->map(['GET'], 'records', \App\v1\Controllers\Domain::class . ':showSubRecords');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Domain::class . ':showSubItil');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Domain::class . ':showSubInfocoms');
            $sub->map(['GET'], 'attacheditems', \App\v1\Controllers\Domain::class . ':showSubAttachedItems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Domain::class . ':showSubHistory');
          });
        });
      });

      $view->group('/appliances', function (RouteCollectorProxy $appliances)
      {
        $appliances->map(['GET'], '', \App\v1\Controllers\Appliance::class . ':showAll');
        $appliances->group("/new", function (RouteCollectorProxy $applianceNew)
        {
          $applianceNew->map(['GET'], '', \App\v1\Controllers\Appliance::class . ':showNewItem');
          $applianceNew->map(['POST'], '', \App\v1\Controllers\Appliance::class . ':newItem');
        });

        $appliances->group("/{id:[0-9]+}", function (RouteCollectorProxy $viewlianceId)
        {
          $viewlianceId->map(['GET'], '', \App\v1\Controllers\Appliance::class . ':showItem');
          $viewlianceId->map(['POST'], '', \App\v1\Controllers\Appliance::class . ':updateItem');
          $viewlianceId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Appliance::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Appliance::class . ':restoreItem');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\Appliance::class . ':showSubCertificates');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Appliance::class . ':showSubDomains');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\Appliance::class . ':showSubExternalLinks');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Appliance::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Appliance::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Appliance::class . ':showSubContracts');
            $sub->map(['GET'], 'itil', \App\v1\Controllers\Appliance::class . ':showSubItil');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Appliance::class . ':showSubItems');
            $sub->map(['GET'], 'infocom', \App\v1\Controllers\Appliance::class . ':showSubInfocoms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Appliance::class . ':showSubHistory');
          });
        });
      });

      $view->group('/projects', function (RouteCollectorProxy $projects)
      {
        $projects->map(['GET'], '', \App\v1\Controllers\Project::class . ':showAll');
        $projects->group("/new", function (RouteCollectorProxy $projectNew)
        {
          $projectNew->map(['GET'], '', \App\v1\Controllers\Project::class . ':showNewItem');
          $projectNew->map(['POST'], '', \App\v1\Controllers\Project::class . ':newItem');
        });

        $projects->group("/{id:[0-9]+}", function (RouteCollectorProxy $projectId)
        {
          $projectId->map(['GET'], '', \App\v1\Controllers\Project::class . ':showItem');
          $projectId->map(['POST'], '', \App\v1\Controllers\Project::class . ':updateItem');
          $projectId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Project::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Project::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Project::class . ':showSubNotes');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Project::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Project::class . ':showSubDocuments');
            $sub->map(['GET'], 'contracts', \App\v1\Controllers\Project::class . ':showSubContracts');
            $sub->map(['GET'], 'projecttasks', \App\v1\Controllers\Project::class . ':showSubProjecttasks');
            $sub->map(['GET'], 'projects', \App\v1\Controllers\Project::class . ':showSubProjects');
            $sub->map(['GET'], 'projectteams', \App\v1\Controllers\Project::class . ':showSubProjectteams');
            $sub->map(['GET'], 'items', \App\v1\Controllers\Project::class . ':showSubItems');
            $sub->map(['GET'], 'costs', \App\v1\Controllers\Project::class . ':showSubCosts');
            $sub->map(['GET'], 'itilitems', \App\v1\Controllers\Project::class . ':showSubItilitems');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Project::class . ':showSubHistory');
          });
        });
      });

      $view->group('/reminders', function (RouteCollectorProxy $reminders)
      {
        $reminders->map(['GET'], '', \App\v1\Controllers\Reminder::class . ':showAll');
        $reminders->group("/new", function (RouteCollectorProxy $reminderNew)
        {
          $reminderNew->map(['GET'], '', \App\v1\Controllers\Reminder::class . ':showNewItem');
          $reminderNew->map(['POST'], '', \App\v1\Controllers\Reminder::class . ':newItem');
        });

        $reminders->group("/{id:[0-9]+}", function (RouteCollectorProxy $reminderId)
        {
          $reminderId->map(['GET'], '', \App\v1\Controllers\Reminder::class . ':showItem');
          $reminderId->map(['POST'], '', \App\v1\Controllers\Reminder::class . ':updateItem');
          $reminderId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Reminder::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Reminder::class . ':restoreItem');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Reminder::class . ':showSubDocuments');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Reminder::class . ':showSubHistory');
          });
        });
      });

      $view->group('/rssfeeds', function (RouteCollectorProxy $rssfeeds)
      {
        $rssfeeds->map(['GET'], '', \App\v1\Controllers\Rssfeed::class . ':showAll');
        $rssfeeds->group("/new", function (RouteCollectorProxy $rssfeedNew)
        {
          $rssfeedNew->map(['GET'], '', \App\v1\Controllers\Rssfeed::class . ':showNewItem');
          $rssfeedNew->map(['POST'], '', \App\v1\Controllers\Rssfeed::class . ':newItem');
        });

        $rssfeeds->group("/{id:[0-9]+}", function (RouteCollectorProxy $rssfeedId)
        {
          $rssfeedId->map(['GET'], '', \App\v1\Controllers\Rssfeed::class . ':showItem');
          $rssfeedId->map(['POST'], '', \App\v1\Controllers\Rssfeed::class . ':updateItem');
          $rssfeedId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Rssfeed::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Rssfeed::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Rssfeed::class . ':showSubHistory');
          });
        });
      });

      $view->group('/savedsearchs', function (RouteCollectorProxy $savedsearchs)
      {
        $savedsearchs->map(['GET'], '', \App\v1\Controllers\Savedsearch::class . ':showAll');
        $savedsearchs->group("/new", function (RouteCollectorProxy $savedsearchNew)
        {
          $savedsearchNew->map(['GET'], '', \App\v1\Controllers\Savedsearch::class . ':showNewItem');
          $savedsearchNew->map(['POST'], '', \App\v1\Controllers\Savedsearch::class . ':newItem');
        });

        $savedsearchs->group("/{id:[0-9]+}", function (RouteCollectorProxy $savedsearchId)
        {
          $savedsearchId->map(['GET'], '', \App\v1\Controllers\Savedsearch::class . ':showItem');
          $savedsearchId->map(['POST'], '', \App\v1\Controllers\Savedsearch::class . ':updateItem');
          $savedsearchId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Savedsearch::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Savedsearch::class . ':restoreItem');
          });
        });
      });

      $view->group('/alerts', function (RouteCollectorProxy $alerts)
      {
        $alerts->map(['GET'], '', \App\v1\Controllers\Alert::class . ':showAll');
        $alerts->group("/new", function (RouteCollectorProxy $alertNew)
        {
          $alertNew->map(['GET'], '', \App\v1\Controllers\Alert::class . ':showNewItem');
          $alertNew->map(['POST'], '', \App\v1\Controllers\Alert::class . ':newItem');
        });

        $alerts->group("/{id:[0-9]+}", function (RouteCollectorProxy $alertId)
        {
          $alertId->map(['GET'], '', \App\v1\Controllers\Alert::class . ':showItem');
          $alertId->map(['POST'], '', \App\v1\Controllers\Alert::class . ':updateItem');
          $alertId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Alert::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Alert::class . ':restoreItem');
          });
        });
      });

      $view->group('/users', function (RouteCollectorProxy $users)
      {
        $users->map(['GET'], '', \App\v1\Controllers\User::class . ':showAll');
        $users->group("/new", function (RouteCollectorProxy $userNew)
        {
          $userNew->map(['GET'], '', \App\v1\Controllers\User::class . ':showNewItem');
          $userNew->map(['POST'], '', \App\v1\Controllers\User::class . ':newItem');
        });

        $users->group("/{id:[0-9]+}", function (RouteCollectorProxy $userId)
        {
          $userId->map(['GET'], '', \App\v1\Controllers\User::class . ':showItem');
          $userId->map(['POST'], '', \App\v1\Controllers\User::class . ':updateItem');
          $userId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\User::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\User::class . ':restoreItem');
            $sub->map(['GET'], 'authorization', \App\v1\Controllers\User::class . ':showSubAuthorization');
            $sub->map(['POST'], 'authorization', \App\v1\Controllers\User::class . ':itemSubAuthorization');
            $sub->map(['GET'], 'certificates', \App\v1\Controllers\User::class . ':showSubCertificates');
            $sub->map(['GET'], 'externallinks', \App\v1\Controllers\User::class . ':showSubExternalLinks');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\User::class . ':showSubDocuments');
            $sub->map(['GET'], 'groups', \App\v1\Controllers\User::class . ':showSubGroups');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\User::class . ':showSubTickets');
            $sub->map(['GET'], 'problems', \App\v1\Controllers\User::class . ':showSubProblems');
            $sub->map(['GET'], 'changes', \App\v1\Controllers\User::class . ':showSubItilChanges');
            $sub->map(['GET'], 'reservations', \App\v1\Controllers\User::class . ':showSubReservations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\User::class . ':showSubHistory');
            $sub->map(['GET'], 'revokeaccesses', \App\v1\Controllers\User::class . ':revokeAccessesItem');
          });
        });
      });
      $view->group('/groups', function (RouteCollectorProxy $groups)
      {
        $groups->map(['GET'], '', \App\v1\Controllers\Group::class . ':showAll');
        $groups->group("/new", function (RouteCollectorProxy $groupNew)
        {
          $groupNew->map(['GET'], '', \App\v1\Controllers\Group::class . ':showNewItem');
          $groupNew->map(['POST'], '', \App\v1\Controllers\Group::class . ':newItem');
        });

        $groups->group("/{id:[0-9]+}", function (RouteCollectorProxy $groupId)
        {
          $groupId->map(['GET'], '', \App\v1\Controllers\Group::class . ':showItem');
          $groupId->map(['POST'], '', \App\v1\Controllers\Group::class . ':updateItem');
          $groupId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Group::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Group::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Group::class . ':showSubNotes');
            $sub->map(['GET'], 'users', \App\v1\Controllers\Group::class . ':showSubUsers');
            $sub->map(['POST'], 'users', \App\v1\Controllers\Group::class . ':newSubUsers');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\Group::class . ':showSubTickets');
            $sub->map(['GET'], 'problems', \App\v1\Controllers\Group::class . ':showSubProblems');
            $sub->map(['GET'], 'changes', \App\v1\Controllers\Group::class . ':showSubItilChanges');
            $sub->map(['GET'], 'groups', \App\v1\Controllers\Group::class . ':showSubGroups');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Group::class . ':showSubHistory');
          });
        });
      });

      $view->group('/entities', function (RouteCollectorProxy $entities)
      {
        $entities->map(['GET'], '', \App\v1\Controllers\Entity::class . ':showAll');
        $entities->group("/new", function (RouteCollectorProxy $entityNew)
        {
          $entityNew->map(['GET'], '', \App\v1\Controllers\Entity::class . ':showNewItem');
          $entityNew->map(['POST'], '', \App\v1\Controllers\Entity::class . ':newItem');
        });

        $entities->group("/{id:[0-9]+}", function (RouteCollectorProxy $entityId)
        {
          $entityId->map(['GET'], '', \App\v1\Controllers\Entity::class . ':showItem');
          $entityId->map(['POST'], '', \App\v1\Controllers\Entity::class . ':updateItem');
          $entityId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Entity::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Entity::class . ':restoreItem');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Entity::class . ':showSubNotes');
            $sub->map(
              ['GET'],
              'knowledgebasearticles',
              \App\v1\Controllers\Entity::class . ':showSubKnowledgebasearticles'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Entity::class . ':showSubDocuments');
            $sub->map(['GET'], 'address', \App\v1\Controllers\Entity::class . ':showSubAddress');
            $sub->map(['GET'], 'entities', \App\v1\Controllers\Entity::class . ':showSubEntities');
            $sub->map(['GET'], 'users', \App\v1\Controllers\Entity::class . ':showSubUsers');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Entity::class . ':showSubHistory');
          });
        });
      });
      $view->group('/rules', function (RouteCollectorProxy $rules)
      {
        $rules->group("/tickets", function (RouteCollectorProxy $tickets)
        {
          $tickets->map(['GET'], '', \App\v1\Controllers\Rules\Ticket::class . ':showAll');
          $tickets->group("/new", function (RouteCollectorProxy $ticketNew)
          {
            $ticketNew->map(['GET'], '', \App\v1\Controllers\Rules\Ticket::class . ':showNewItem');
            $ticketNew->map(['POST'], '', \App\v1\Controllers\Rules\Ticket::class . ':newItem');
          });

          $tickets->group("/{id:[0-9]+}", function (RouteCollectorProxy $ticketId)
          {
            $ticketId->map(['GET'], '', \App\v1\Controllers\Rules\Ticket::class . ':showItem');

            $ticketId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'criteria', \App\v1\Controllers\Rules\Ticket::class . ':showCriteria');
              $sub->group("criteria/new", function (RouteCollectorProxy $criteriaNew)
              {
                $criteriaNew->map(['GET'], '', \App\v1\Controllers\Rules\Ticket::class . ':showNewCriteria');
                $criteriaNew->map(['POST'], '', \App\v1\Controllers\Rules\Ticket::class . ':newCriteria');
              });
              $sub->map(['GET'], 'actions', \App\v1\Controllers\Rules\Ticket::class . ':showActions');
              $sub->group("actions/new", function (RouteCollectorProxy $actionNew)
              {
                $actionNew->map(['GET'], '', \App\v1\Controllers\Rules\Ticket::class . ':showNewAction');
                $actionNew->map(['POST'], '', \App\v1\Controllers\Rules\Ticket::class . ':newAction');
              });
            });
          });
        });
        $rules->group("/users", function (RouteCollectorProxy $users)
        {
          $users->map(['GET'], '', \App\v1\Controllers\Rules\User::class . ':showAll');
          $users->group("/new", function (RouteCollectorProxy $userNew)
          {
            $userNew->map(['GET'], '', \App\v1\Controllers\Rules\User::class . ':showNewItem');
            $userNew->map(['POST'], '', \App\v1\Controllers\Rules\User::class . ':newItem');
          });

          $users->group("/{id:[0-9]+}", function (RouteCollectorProxy $userId)
          {
            $userId->map(['GET'], '', \App\v1\Controllers\Rules\User::class . ':showItem');

            $userId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'criteria', \App\v1\Controllers\Rules\User::class . ':showCriteria');
              $sub->group("criteria/new", function (RouteCollectorProxy $criteriaNew)
              {
                $criteriaNew->map(['GET'], '', \App\v1\Controllers\Rules\User::class . ':showNewCriteria');
                $criteriaNew->map(['POST'], '', \App\v1\Controllers\Rules\User::class . ':newCriteria');
              });
              $sub->map(['GET'], 'actions', \App\v1\Controllers\Rules\User::class . ':showActions');
              $sub->group("actions/new", function (RouteCollectorProxy $actionNew)
              {
                $actionNew->map(['GET'], '', \App\v1\Controllers\Rules\User::class . ':showNewAction');
                $actionNew->map(['POST'], '', \App\v1\Controllers\Rules\User::class . ':newAction');
              });
            });
          });
        });
      });

      $view->group('/profiles', function (RouteCollectorProxy $profiles)
      {
        $profiles->map(['GET'], '', \App\v1\Controllers\Profile::class . ':showAll');
        $profiles->group("/new", function (RouteCollectorProxy $profileNew)
        {
          $profileNew->map(['GET'], '', \App\v1\Controllers\Profile::class . ':showNewItem');
          $profileNew->map(['POST'], '', \App\v1\Controllers\Profile::class . ':newItem');
        });

        $profiles->group("/{id:[0-9]+}", function (RouteCollectorProxy $profileId)
        {
          $profileId->map(['GET'], '', \App\v1\Controllers\Profile::class . ':showItem');
          $profileId->map(['POST'], '', \App\v1\Controllers\Profile::class . ':updateItem');

          $profileId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Profile::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Profile::class . ':restoreItem');
            $sub->map(['GET'], 'assets', \App\v1\Controllers\Profile::class . ':showSubAssets');
            $sub->map(['POST'], 'assets', \App\v1\Controllers\Profile::class . ':itemSubAssets');
            $sub->map(['GET'], 'assistance', \App\v1\Controllers\Profile::class . ':showSubAssistance');
            $sub->map(['POST'], 'assistance', \App\v1\Controllers\Profile::class . ':itemSubAssistance');
            $sub->map(['GET'], 'forms', \App\v1\Controllers\Profile::class . ':showSubForms');
            $sub->map(['POST'], 'forms', \App\v1\Controllers\Profile::class . ':itemSubForms');
            $sub->map(['GET'], 'management', \App\v1\Controllers\Profile::class . ':showSubManagement');
            $sub->map(['POST'], 'management', \App\v1\Controllers\Profile::class . ':itemSubManagement');
            $sub->map(['GET'], 'tools', \App\v1\Controllers\Profile::class . ':showSubTools');
            $sub->map(['POST'], 'tools', \App\v1\Controllers\Profile::class . ':itemSubTools');
            $sub->map(['GET'], 'administration', \App\v1\Controllers\Profile::class . ':showSubAdministration');
            $sub->map(['POST'], 'administration', \App\v1\Controllers\Profile::class . ':itemSubAdministration');
            $sub->map(['GET'], 'setup', \App\v1\Controllers\Profile::class . ':showSubSetup');
            $sub->map(['POST'], 'setup', \App\v1\Controllers\Profile::class . ':itemSubSetup');
            $sub->map(['GET'], 'users', \App\v1\Controllers\Profile::class . ':showSubUsers');

            $sub->map(['GET'], 'history', \App\v1\Controllers\Profile::class . ':showSubHistory');
          });
        });
      });

      $view->group('/queuednotifications', function (RouteCollectorProxy $queuednotifications)
      {
        $queuednotifications->map(['GET'], '', \App\v1\Controllers\Queuednotification::class . ':showAll');
        $queuednotifications->map(['POST'], '', \App\v1\Controllers\Queuednotification::class . ':postItem');
        $queuednotifications->group("/{id:[0-9]+}", function (RouteCollectorProxy $queuednotificationId)
        {
          $queuednotificationId->map(['GET'], '', \App\v1\Controllers\Queuednotification::class . ':showItem');
          $queuednotificationId->map(['POST'], '', \App\v1\Controllers\Queuednotification::class . ':updateItem');
        });
      });
      $view->group('/audits', function (RouteCollectorProxy $audits)
      {
        $audits->map(['GET'], '', \App\v1\Controllers\Audit::class . ':showAll');
        $audits->map(['POST'], '', \App\v1\Controllers\Audit::class . ':postItem');
        $audits->group("/{id:[0-9]+}", function (RouteCollectorProxy $eventId)
        {
          $eventId->map(['GET'], '', \App\v1\Controllers\Audit::class . ':showItem');
          $eventId->map(['POST'], '', \App\v1\Controllers\Audit::class . ':updateItem');
        });
      });

      $view->group('/locations', function (RouteCollectorProxy $locations)
      {
        $locations->map(['GET'], '', \App\v1\Controllers\Location::class . ':showAll');
        $locations->group("/new", function (RouteCollectorProxy $locationNew)
        {
          $locationNew->map(['GET'], '', \App\v1\Controllers\Location::class . ':showNewItem');
          $locationNew->map(['POST'], '', \App\v1\Controllers\Location::class . ':newItem');
        });

        $locations->group("/{id:[0-9]+}", function (RouteCollectorProxy $locationId)
        {
          $locationId->map(['GET'], '', \App\v1\Controllers\Location::class . ':showItem');
          $locationId->map(['POST'], '', \App\v1\Controllers\Location::class . ':updateItem');
          $locationId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Location::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Location::class . ':restoreItem');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Location::class . ':showSubDocuments');
            $sub->map(['GET'], 'locations', \App\v1\Controllers\Location::class . ':showSubLocations');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Location::class . ':showSubHistory');
          });
        });
      });

      $view->group('/states', function (RouteCollectorProxy $states)
      {
        $states->map(['GET'], '', \App\v1\Controllers\State::class . ':showAll');
        $states->group("/new", function (RouteCollectorProxy $stateNew)
        {
          $stateNew->map(['GET'], '', \App\v1\Controllers\State::class . ':showNewItem');
          $stateNew->map(['POST'], '', \App\v1\Controllers\State::class . ':newItem');
        });

        $states->group("/{id:[0-9]+}", function (RouteCollectorProxy $stateId)
        {
          $stateId->map(['GET'], '', \App\v1\Controllers\State::class . ':showItem');
          $stateId->map(['POST'], '', \App\v1\Controllers\State::class . ':updateItem');
          $stateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\State::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\State::class . ':restoreItem');
            $sub->map(['GET'], 'states', \App\v1\Controllers\State::class . ':showSubStates');
            $sub->map(['GET'], 'history', \App\v1\Controllers\State::class . ':showSubHistory');
          });
        });
      });

      $view->group('/manufacturers', function (RouteCollectorProxy $manufacturers)
      {
        $manufacturers->map(['GET'], '', \App\v1\Controllers\Manufacturer::class . ':showAll');
        $manufacturers->group("/new", function (RouteCollectorProxy $manufacturerNew)
        {
          $manufacturerNew->map(['GET'], '', \App\v1\Controllers\Manufacturer::class . ':showNewItem');
          $manufacturerNew->map(['POST'], '', \App\v1\Controllers\Manufacturer::class . ':newItem');
        });

        $manufacturers->group("/{id:[0-9]+}", function (RouteCollectorProxy $manufacturerId)
        {
          $manufacturerId->map(['GET'], '', \App\v1\Controllers\Manufacturer::class . ':showItem');
          $manufacturerId->map(['POST'], '', \App\v1\Controllers\Manufacturer::class . ':updateItem');
          $manufacturerId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Manufacturer::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Manufacturer::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Manufacturer::class . ':showSubHistory');
          });
        });
      });
      $view->group('/blacklists', function (RouteCollectorProxy $blacklists)
      {
        $blacklists->map(['GET'], '', \App\v1\Controllers\Blacklist::class . ':showAll');
        $blacklists->group("/new", function (RouteCollectorProxy $blacklistNew)
        {
          $blacklistNew->map(['GET'], '', \App\v1\Controllers\Blacklist::class . ':showNewItem');
          $blacklistNew->map(['POST'], '', \App\v1\Controllers\Blacklist::class . ':newItem');
        });

        $blacklists->group("/{id:[0-9]+}", function (RouteCollectorProxy $blacklistId)
        {
          $blacklistId->map(['GET'], '', \App\v1\Controllers\Blacklist::class . ':showItem');
          $blacklistId->map(['POST'], '', \App\v1\Controllers\Blacklist::class . ':updateItem');
          $blacklistId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Blacklist::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Blacklist::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Blacklist::class . ':showSubHistory');
          });
        });
      });
      $view->group('/blacklistedmailcontents', function (RouteCollectorProxy $blacklistedmailcontents)
      {
        $blacklistedmailcontents->map(['GET'], '', \App\v1\Controllers\Blacklistedmailcontent::class . ':showAll');
        $blacklistedmailcontents->group("/new", function (RouteCollectorProxy $blmcNew)
        {
          $blmcNew->map(['GET'], '', \App\v1\Controllers\Blacklistedmailcontent::class . ':showNewItem');
          $blmcNew->map(['POST'], '', \App\v1\Controllers\Blacklistedmailcontent::class . ':newItem');
        });

        $blacklistedmailcontents->group("/{id:[0-9]+}", function (RouteCollectorProxy $blmcId)
        {
          $blmcId->map(['GET'], '', \App\v1\Controllers\Blacklistedmailcontent::class . ':showItem');
          $blmcId->map(['POST'], '', \App\v1\Controllers\Blacklistedmailcontent::class . ':updateItem');
          $blmcId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Blacklistedmailcontent::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Blacklistedmailcontent::class . ':restoreItem');
          });
        });
      });
      $view->group('/categories', function (RouteCollectorProxy $categories)
      {
        $categories->map(['GET'], '', \App\v1\Controllers\Category::class . ':showAll');

        $categories->group("/new", function (RouteCollectorProxy $categoryNew)
        {
          $categoryNew->map(['GET'], '', \App\v1\Controllers\Category::class . ':showNewItem');
          $categoryNew->map(['POST'], '', \App\v1\Controllers\Category::class . ':newItem');
        });

        $categories->group("/{id:[0-9]+}", function (RouteCollectorProxy $categoryId)
        {
          $categoryId->map(['GET'], '', \App\v1\Controllers\Category::class . ':showItem');
          $categoryId->map(['POST'], '', \App\v1\Controllers\Category::class . ':updateItem');
          $categoryId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Category::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Category::class . ':restoreItem');
            $sub->map(['GET'], 'categories', \App\v1\Controllers\Category::class . ':showSubCategories');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Category::class . ':showSubHistory');
          });
        });
      });
      $view->group('/ticketemplates', function (RouteCollectorProxy $ticketemplates)
      {
        $ticketemplates->map(['GET'], '', \App\v1\Controllers\Tickettemplate::class . ':showAll');
        $ticketemplates->map(['POST'], '', \App\v1\Controllers\Tickettemplate::class . ':postItem');
        $ticketemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $ticketemplateId)
        {
          $ticketemplateId->map(['GET'], '', \App\v1\Controllers\Tickettemplate::class . ':showItem');
          $ticketemplateId->map(['POST'], '', \App\v1\Controllers\Tickettemplate::class . ':updateItem');
          $ticketemplateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(
              ['GET'],
              'mandatoryfields',
              \App\v1\Controllers\Tickettemplate::class . ':showSubMandatoryFields'
            );
            $sub->map(
              ['GET'],
              'predefinedfields',
              \App\v1\Controllers\Tickettemplate::class . ':showSubPredefinedFields'
            );
            $sub->map(['GET'], 'hiddenfields', \App\v1\Controllers\Tickettemplate::class . ':showSubHiddenFields');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Tickettemplate::class . ':showSubHistory');
          });
        });
      });

      $view->group('/solutiontypes', function (RouteCollectorProxy $solutiontypes)
      {
        $solutiontypes->map(['GET'], '', \App\v1\Controllers\Solutiontype::class . ':showAll');
        $solutiontypes->group("/new", function (RouteCollectorProxy $stypeNew)
        {
          $stypeNew->map(['GET'], '', \App\v1\Controllers\Solutiontype::class . ':showNewItem');
          $stypeNew->map(['POST'], '', \App\v1\Controllers\Solutiontype::class . ':newItem');
        });

        $solutiontypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $solutiontypeId)
        {
          $solutiontypeId->map(['GET'], '', \App\v1\Controllers\Solutiontype::class . ':showItem');
          $solutiontypeId->map(['POST'], '', \App\v1\Controllers\Solutiontype::class . ':updateItem');
          $solutiontypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Solutiontype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Solutiontype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Solutiontype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/solutiontemplates', function (RouteCollectorProxy $solutiontemplates)
      {
        $solutiontemplates->map(['GET'], '', \App\v1\Controllers\Solutiontemplate::class . ':showAll');
        $solutiontemplates->group("/new", function (RouteCollectorProxy $stemplateNew)
        {
          $stemplateNew->map(['GET'], '', \App\v1\Controllers\Solutiontemplate::class . ':showNewItem');
          $stemplateNew->map(['POST'], '', \App\v1\Controllers\Solutiontemplate::class . ':newItem');
        });

        $solutiontemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $solutiontemplateId)
        {
          $solutiontemplateId->map(['GET'], '', \App\v1\Controllers\Solutiontemplate::class . ':showItem');
          $solutiontemplateId->map(['POST'], '', \App\v1\Controllers\Solutiontemplate::class . ':updateItem');
          $solutiontemplateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Solutiontemplate::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Solutiontemplate::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Solutiontemplate::class . ':showSubHistory');
          });
        });
      });

      $view->group('/requesttypes', function (RouteCollectorProxy $requesttypes)
      {
        $requesttypes->map(['GET'], '', \App\v1\Controllers\Requesttype::class . ':showAll');
        $requesttypes->group("/new", function (RouteCollectorProxy $requesttypeNew)
        {
          $requesttypeNew->map(['GET'], '', \App\v1\Controllers\Requesttype::class . ':showNewItem');
          $requesttypeNew->map(['POST'], '', \App\v1\Controllers\Requesttype::class . ':newItem');
        });

        $requesttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $requesttypeId)
        {
          $requesttypeId->map(['GET'], '', \App\v1\Controllers\Requesttype::class . ':showItem');
          $requesttypeId->map(['POST'], '', \App\v1\Controllers\Requesttype::class . ':updateItem');
          $requesttypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Requesttype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Requesttype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Requesttype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/followuptemplates', function (RouteCollectorProxy $followuptemplates)
      {
        $followuptemplates->map(['GET'], '', \App\v1\Controllers\Followuptemplate::class . ':showAll');
        $followuptemplates->group("/new", function (RouteCollectorProxy $ftemplateNew)
        {
          $ftemplateNew->map(['GET'], '', \App\v1\Controllers\Followuptemplate::class . ':showNewItem');
          $ftemplateNew->map(['POST'], '', \App\v1\Controllers\Followuptemplate::class . ':newItem');
        });

        $followuptemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $itilfollowuptemplateId)
        {
          $itilfollowuptemplateId->map(['GET'], '', \App\v1\Controllers\Followuptemplate::class . ':showItem');
          $itilfollowuptemplateId->map(['POST'], '', \App\v1\Controllers\Followuptemplate::class . ':updateItem');
          $itilfollowuptemplateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Followuptemplate::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Followuptemplate::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Followuptemplate::class . ':showSubHistory');
          });
        });
      });

      $view->group('/projectstates', function (RouteCollectorProxy $projectstates)
      {
        $projectstates->map(['GET'], '', \App\v1\Controllers\Projectstate::class . ':showAll');
        $projectstates->group("/new", function (RouteCollectorProxy $projectstateNew)
        {
          $projectstateNew->map(['GET'], '', \App\v1\Controllers\Projectstate::class . ':showNewItem');
          $projectstateNew->map(['POST'], '', \App\v1\Controllers\Projectstate::class . ':newItem');
        });

        $projectstates->group("/{id:[0-9]+}", function (RouteCollectorProxy $projectstateId)
        {
          $projectstateId->map(['GET'], '', \App\v1\Controllers\Projectstate::class . ':showItem');
          $projectstateId->map(['POST'], '', \App\v1\Controllers\Projectstate::class . ':updateItem');
          $projectstateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Projectstate::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Projectstate::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Projectstate::class . ':showSubHistory');
          });
        });
      });

      $view->group('/projecttypes', function (RouteCollectorProxy $projecttypes)
      {
        $projecttypes->map(['GET'], '', \App\v1\Controllers\Projecttype::class . ':showAll');
        $projecttypes->group("/new", function (RouteCollectorProxy $ptypeNew)
        {
          $ptypeNew->map(['GET'], '', \App\v1\Controllers\Projecttype::class . ':showNewItem');
          $ptypeNew->map(['POST'], '', \App\v1\Controllers\Projecttype::class . ':newItem');
        });

        $projecttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $projecttypeId)
        {
          $projecttypeId->map(['GET'], '', \App\v1\Controllers\Projecttype::class . ':showItem');
          $projecttypeId->map(['POST'], '', \App\v1\Controllers\Projecttype::class . ':updateItem');
          $projecttypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Projecttype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Projecttype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Projecttype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/projecttasks', function (RouteCollectorProxy $projecttasks)
      {
        $projecttasks->map(['GET'], '', \App\v1\Controllers\Projecttask::class . ':showAll');
        $projecttasks->group("/new", function (RouteCollectorProxy $ptaskNew)
        {
          $ptaskNew->map(['GET'], '', \App\v1\Controllers\Projecttask::class . ':showNewItem');
          $ptaskNew->map(['POST'], '', \App\v1\Controllers\Projecttask::class . ':newItem');
        });

        $projecttasks->group("/{id:[0-9]+}", function (RouteCollectorProxy $projecttaskId)
        {
          $projecttaskId->map(['GET'], '', \App\v1\Controllers\Projecttask::class . ':showItem');
          $projecttaskId->map(['POST'], '', \App\v1\Controllers\Projecttask::class . ':updateItem');
          $projecttaskId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Projecttask::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Projecttask::class . ':restoreItem');
            $sub->map(['GET'], 'projecttasks', \App\v1\Controllers\Projecttask::class . ':showSubProjecttasks');
            $sub->map(
              ['GET'],
              'projecttaskteams',
              \App\v1\Controllers\Projecttask::class . ':showSubProjecttaskteams'
            );
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Projecttask::class . ':showSubDocuments');
            $sub->map(['GET'], 'notes', \App\v1\Controllers\Projecttask::class . ':showSubNotes');
            $sub->map(['GET'], 'tickets', \App\v1\Controllers\Projecttask::class . ':showSubTickets');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Projecttask::class . ':showSubHistory');
          });
        });
      });

      $view->group('/projecttasktypes', function (RouteCollectorProxy $projecttasktypes)
      {
        $projecttasktypes->map(['GET'], '', \App\v1\Controllers\Projecttasktype::class . ':showAll');
        $projecttasktypes->group("/new", function (RouteCollectorProxy $pttypeNew)
        {
          $pttypeNew->map(['GET'], '', \App\v1\Controllers\Projecttasktype::class . ':showNewItem');
          $pttypeNew->map(['POST'], '', \App\v1\Controllers\Projecttasktype::class . ':newItem');
        });

        $projecttasktypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $projecttasktypeId)
        {
          $projecttasktypeId->map(['GET'], '', \App\v1\Controllers\Projecttasktype::class . ':showItem');
          $projecttasktypeId->map(['POST'], '', \App\v1\Controllers\Projecttasktype::class . ':updateItem');
          $projecttasktypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Projecttasktype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Projecttasktype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Projecttasktype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/projecttasktemplates', function (RouteCollectorProxy $projecttasktemplates)
      {
        $projecttasktemplates->map(['GET'], '', \App\v1\Controllers\Projecttasktemplate::class . ':showAll');
        $projecttasktemplates->group("/new", function (RouteCollectorProxy $pttemplateNew)
        {
          $pttemplateNew->map(['GET'], '', \App\v1\Controllers\Projecttasktemplate::class . ':showNewItem');
          $pttemplateNew->map(['POST'], '', \App\v1\Controllers\Projecttasktemplate::class . ':newItem');
        });

        $projecttasktemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $projecttasktemplateId)
        {
          $projecttasktemplateId->map(['GET'], '', \App\v1\Controllers\Projecttasktemplate::class . ':showItem');
          $projecttasktemplateId->map(['POST'], '', \App\v1\Controllers\Projecttasktemplate::class . ':updateItem');
          $projecttasktemplateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Projecttasktemplate::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Projecttasktemplate::class . ':restoreItem');
            $sub->map(['GET'], 'documents', \App\v1\Controllers\Projecttasktemplate::class . ':showSubDocuments');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Projecttasktemplate::class . ':showSubHistory');
          });
        });
      });

      $view->group('/planningeventcategories', function (RouteCollectorProxy $planningeventcategories)
      {
        $planningeventcategories->map(['GET'], '', \App\v1\Controllers\Planningeventcategory::class . ':showAll');
        $planningeventcategories->group("/new", function (RouteCollectorProxy $pecategoryNew)
        {
          $pecategoryNew->map(['GET'], '', \App\v1\Controllers\Planningeventcategory::class . ':showNewItem');
          $pecategoryNew->map(['POST'], '', \App\v1\Controllers\Planningeventcategory::class . ':newItem');
        });

        $planningeventcategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $planningeventcategoryId)
        {
          $planningeventcategoryId->map(['GET'], '', \App\v1\Controllers\Planningeventcategory::class . ':showItem');
          $planningeventcategoryId->map(
            ['POST'],
            '',
            \App\v1\Controllers\Planningeventcategory::class . ':updateItem'
          );
          $planningeventcategoryId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Planningeventcategory::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Planningeventcategory::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Planningeventcategory::class . ':showSubHistory');
          });
        });
      });

      $view->group('/planningexternaleventtemplates', function (RouteCollectorProxy $pe_eventtemplates)
      {
        $pe_eventtemplates->map(['GET'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':showAll');
        $pe_eventtemplates->group("/new", function (RouteCollectorProxy $peetemplateNew)
        {
          $peetemplateNew->map(['GET'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':showNewItem');
          $peetemplateNew->map(['POST'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':newItem');
        });

        $pe_eventtemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $peetId)
        {
          $peetId->map(['GET'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':showItem');
          $peetId->map(['POST'], '', \App\v1\Controllers\Planningexternaleventtemplate::class . ':updateItem');
          $peetId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Planningexternaleventtemplate::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Planningexternaleventtemplate::class . ':restoreItem');
            $sub->map(
              ['GET'],
              'history',
              \App\v1\Controllers\Planningexternaleventtemplate::class . ':showSubHistory'
            );
          });
        });
      });
      $view->group('/computertypes', function (RouteCollectorProxy $computertypes)
      {
        $computertypes->map(['GET'], '', \App\v1\Controllers\Computertype::class . ':showAll');
        $computertypes->group("/new", function (RouteCollectorProxy $computertypeNew)
        {
          $computertypeNew->map(['GET'], '', \App\v1\Controllers\Computertype::class . ':showNewItem');
          $computertypeNew->map(['POST'], '', \App\v1\Controllers\Computertype::class . ':newItem');
        });

        $computertypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $computertypeId)
        {
          $computertypeId->map(['GET'], '', \App\v1\Controllers\Computertype::class . ':showItem');
          $computertypeId->map(['POST'], '', \App\v1\Controllers\Computertype::class . ':updateItem');
          $computertypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Computertype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Computertype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Computertype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/networkequipmenttypes', function (RouteCollectorProxy $networkequipmenttypes)
      {
        $networkequipmenttypes->map(['GET'], '', \App\v1\Controllers\Networkequipmenttype::class . ':showAll');
        $networkequipmenttypes->group("/new", function (RouteCollectorProxy $neTypeNew)
        {
          $neTypeNew->map(['GET'], '', \App\v1\Controllers\Networkequipmenttype::class . ':showNewItem');
          $neTypeNew->map(['POST'], '', \App\v1\Controllers\Networkequipmenttype::class . ':newItem');
        });

        $networkequipmenttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkequipmenttypeId)
        {
          $networkequipmenttypeId->map(['GET'], '', \App\v1\Controllers\Networkequipmenttype::class . ':showItem');
          $networkequipmenttypeId->map(['POST'], '', \App\v1\Controllers\Networkequipmenttype::class . ':updateItem');
          $networkequipmenttypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Networkequipmenttype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Networkequipmenttype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Networkequipmenttype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/printertypes', function (RouteCollectorProxy $printertypes)
      {
        $printertypes->map(['GET'], '', \App\v1\Controllers\Printertype::class . ':showAll');
        $printertypes->group("/new", function (RouteCollectorProxy $ptypeNew)
        {
          $ptypeNew->map(['GET'], '', \App\v1\Controllers\Printertype::class . ':showNewItem');
          $ptypeNew->map(['POST'], '', \App\v1\Controllers\Printertype::class . ':newItem');
        });

        $printertypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $printertypeId)
        {
          $printertypeId->map(['GET'], '', \App\v1\Controllers\Printertype::class . ':showItem');
          $printertypeId->map(['POST'], '', \App\v1\Controllers\Printertype::class . ':updateItem');
          $printertypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Printertype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Printertype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Printertype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/monitortypes', function (RouteCollectorProxy $monitortypes)
      {
        $monitortypes->map(['GET'], '', \App\v1\Controllers\Monitortype::class . ':showAll');
        $monitortypes->group("/new", function (RouteCollectorProxy $typeNew)
        {
          $typeNew->map(['GET'], '', \App\v1\Controllers\Monitortype::class . ':showNewItem');
          $typeNew->map(['POST'], '', \App\v1\Controllers\Monitortype::class . ':newItem');
        });

        $monitortypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $monitortypeId)
        {
          $monitortypeId->map(['GET'], '', \App\v1\Controllers\Monitortype::class . ':showItem');
          $monitortypeId->map(['POST'], '', \App\v1\Controllers\Monitortype::class . ':updateItem');
          $monitortypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Monitortype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Monitortype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Monitortype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/peripheraltypes', function (RouteCollectorProxy $peripheraltypes)
      {
        $peripheraltypes->map(['GET'], '', \App\v1\Controllers\Peripheraltype::class . ':showAll');
        $peripheraltypes->group("/new", function (RouteCollectorProxy $peripheraltypeNew)
        {
          $peripheraltypeNew->map(['GET'], '', \App\v1\Controllers\Peripheraltype::class . ':showNewItem');
          $peripheraltypeNew->map(['POST'], '', \App\v1\Controllers\Peripheraltype::class . ':newItem');
        });

        $peripheraltypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $peripheraltypeId)
        {
          $peripheraltypeId->map(['GET'], '', \App\v1\Controllers\Peripheraltype::class . ':showItem');
          $peripheraltypeId->map(['POST'], '', \App\v1\Controllers\Peripheraltype::class . ':updateItem');
          $peripheraltypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Peripheraltype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Peripheraltype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Peripheraltype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/phonetypes', function (RouteCollectorProxy $phonetypes)
      {
        $phonetypes->map(['GET'], '', \App\v1\Controllers\Phonetype::class . ':showAll');
        $phonetypes->group("/new", function (RouteCollectorProxy $phonetypeNew)
        {
          $phonetypeNew->map(['GET'], '', \App\v1\Controllers\Phonetype::class . ':showNewItem');
          $phonetypeNew->map(['POST'], '', \App\v1\Controllers\Phonetype::class . ':newItem');
        });

        $phonetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $phonetypeId)
        {
          $phonetypeId->map(['GET'], '', \App\v1\Controllers\Phonetype::class . ':showItem');
          $phonetypeId->map(['POST'], '', \App\v1\Controllers\Phonetype::class . ':updateItem');
          $phonetypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Phonetype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Phonetype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Phonetype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/softwarelicensetypes', function (RouteCollectorProxy $softwarelicensetypes)
      {
        $softwarelicensetypes->map(['GET'], '', \App\v1\Controllers\Softwarelicensetype::class . ':showAll');
        $softwarelicensetypes->group("/new", function (RouteCollectorProxy $sltypeNew)
        {
          $sltypeNew->map(['GET'], '', \App\v1\Controllers\Softwarelicensetype::class . ':showNewItem');
          $sltypeNew->map(['POST'], '', \App\v1\Controllers\Softwarelicensetype::class . ':newItem');
        });

        $softwarelicensetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwarelicensetypeId)
        {
          $softwarelicensetypeId->map(['GET'], '', \App\v1\Controllers\Softwarelicensetype::class . ':showItem');
          $softwarelicensetypeId->map(['POST'], '', \App\v1\Controllers\Softwarelicensetype::class . ':updateItem');
          $softwarelicensetypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Softwarelicensetype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Softwarelicensetype::class . ':restoreItem');
            $sub->map(
              ['GET'],
              'licencetypes',
              \App\v1\Controllers\Softwarelicensetype::class . ':showSubLicencetypes'
            );
            $sub->map(['GET'], 'history', \App\v1\Controllers\Softwarelicensetype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/cartridgeitemtypes', function (RouteCollectorProxy $cartridgeitemtypes)
      {
        $cartridgeitemtypes->map(['GET'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':showAll');
        $cartridgeitemtypes->group("/new", function (RouteCollectorProxy $citNew)
        {
          $citNew->map(['GET'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':showNewItem');
          $citNew->map(['POST'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':newItem');
        });

        $cartridgeitemtypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $cartridgeitemtypeId)
        {
          $cartridgeitemtypeId->map(['GET'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':showItem');
          $cartridgeitemtypeId->map(['POST'], '', \App\v1\Controllers\Cartridgeitemtype::class . ':updateItem');
          $cartridgeitemtypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Cartridgeitemtype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Cartridgeitemtype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Cartridgeitemtype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/consumableitemtypes', function (RouteCollectorProxy $consumableitemtypes)
      {
        $consumableitemtypes->map(['GET'], '', \App\v1\Controllers\Consumableitemtype::class . ':showAll');
        $consumableitemtypes->group("/new", function (RouteCollectorProxy $citNew)
        {
          $citNew->map(['GET'], '', \App\v1\Controllers\Consumableitemtype::class . ':showNewItem');
          $citNew->map(['POST'], '', \App\v1\Controllers\Consumableitemtype::class . ':newItem');
        });

        $consumableitemtypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $consumableitemtypeId)
        {
          $consumableitemtypeId->map(['GET'], '', \App\v1\Controllers\Consumableitemtype::class . ':showItem');
          $consumableitemtypeId->map(['POST'], '', \App\v1\Controllers\Consumableitemtype::class . ':updateItem');
          $consumableitemtypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Consumableitemtype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Consumableitemtype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Consumableitemtype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/contracttypes', function (RouteCollectorProxy $contracttypes)
      {
        $contracttypes->map(['GET'], '', \App\v1\Controllers\Contracttype::class . ':showAll');
        $contracttypes->group("/new", function (RouteCollectorProxy $contracttypeNew)
        {
          $contracttypeNew->map(['GET'], 'delete', \App\v1\Controllers\Contracttype::class . ':deleteItem');
          $contracttypeNew->map(['GET'], 'restore', \App\v1\Controllers\Contracttype::class . ':restoreItem');
        });

        $contracttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $contracttypeId)
        {
          $contracttypeId->map(['GET'], '', \App\v1\Controllers\Contracttype::class . ':showItem');
          $contracttypeId->map(['POST'], '', \App\v1\Controllers\Contracttype::class . ':updateItem');
          $contracttypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Contracttype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Contracttype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Contracttype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/contacttypes', function (RouteCollectorProxy $contacttypes)
      {
        $contacttypes->map(['GET'], '', \App\v1\Controllers\Contacttype::class . ':showAll');
        $contacttypes->group("/new", function (RouteCollectorProxy $contacttypeNew)
        {
          $contacttypeNew->map(['GET'], '', \App\v1\Controllers\Contacttype::class . ':showNewItem');
          $contacttypeNew->map(['POST'], '', \App\v1\Controllers\Contacttype::class . ':newItem');
        });

        $contacttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $contacttypeId)
        {
          $contacttypeId->map(['GET'], '', \App\v1\Controllers\Contacttype::class . ':showItem');
          $contacttypeId->map(['POST'], '', \App\v1\Controllers\Contacttype::class . ':updateItem');
          $contacttypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Contacttype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Contacttype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Contacttype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicegenerictypes', function (RouteCollectorProxy $devicegenerictype)
      {
        $devicegenerictype->map(['GET'], '', \App\v1\Controllers\Devicegenerictype::class . ':showAll');
        $devicegenerictype->group("/new", function (RouteCollectorProxy $generictypeNew)
        {
          $generictypeNew->map(['GET'], '', \App\v1\Controllers\Devicegenerictype::class . ':showNewItem');
          $generictypeNew->map(['POST'], '', \App\v1\Controllers\Devicegenerictype::class . ':newItem');
        });

        $devicegenerictype->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicegenerictypeId)
        {
          $devicegenerictypeId->map(['GET'], '', \App\v1\Controllers\Devicegenerictype::class . ':showItem');
          $devicegenerictypeId->map(['POST'], '', \App\v1\Controllers\Devicegenerictype::class . ':updateItem');
          $devicegenerictypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicegenerictype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicegenerictype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegenerictype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicesensortypes', function (RouteCollectorProxy $devicesensortype)
      {
        $devicesensortype->map(['GET'], '', \App\v1\Controllers\Devicesensortype::class . ':showAll');
        $devicesensortype->group("/new", function (RouteCollectorProxy $sensortypeNew)
        {
          $sensortypeNew->map(['GET'], '', \App\v1\Controllers\Devicesensortype::class . ':showNewItem');
          $sensortypeNew->map(['POST'], '', \App\v1\Controllers\Devicesensortype::class . ':newItem');
        });

        $devicesensortype->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesensortypeId)
        {
          $devicesensortypeId->map(['GET'], '', \App\v1\Controllers\Devicesensortype::class . ':showItem');
          $devicesensortypeId->map(['POST'], '', \App\v1\Controllers\Devicesensortype::class . ':updateItem');
          $devicesensortypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicesensortype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicesensortype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesensortype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicememorytypes', function (RouteCollectorProxy $devicememorytype)
      {
        $devicememorytype->map(['GET'], '', \App\v1\Controllers\Devicememorytype::class . ':showAll');
        $devicememorytype->group("/new", function (RouteCollectorProxy $memorytypeNew)
        {
          $memorytypeNew->map(['GET'], '', \App\v1\Controllers\Devicememorytype::class . ':showNewItem');
          $memorytypeNew->map(['POST'], '', \App\v1\Controllers\Devicememorytype::class . ':newItem');
        });

        $devicememorytype->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicememorytypeId)
        {
          $devicememorytypeId->map(['GET'], '', \App\v1\Controllers\Devicememorytype::class . ':showItem');
          $devicememorytypeId->map(['POST'], '', \App\v1\Controllers\Devicememorytype::class . ':updateItem');
          $devicememorytypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicememorytype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicememorytype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicememorytype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/suppliertypes', function (RouteCollectorProxy $suppliertypes)
      {
        $suppliertypes->map(['GET'], '', \App\v1\Controllers\Suppliertype::class . ':showAll');
        $suppliertypes->group("/new", function (RouteCollectorProxy $suppliertypeNew)
        {
          $suppliertypeNew->map(['GET'], '', \App\v1\Controllers\Suppliertype::class . ':showNewItem');
          $suppliertypeNew->map(['POST'], '', \App\v1\Controllers\Suppliertype::class . ':newItem');
        });

        $suppliertypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $suppliertypeId)
        {
          $suppliertypeId->map(['GET'], '', \App\v1\Controllers\Suppliertype::class . ':showItem');
          $suppliertypeId->map(['POST'], '', \App\v1\Controllers\Suppliertype::class . ':updateItem');
          $suppliertypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Suppliertype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Suppliertype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Suppliertype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/interfacetypes', function (RouteCollectorProxy $interfacetypes)
      {
        $interfacetypes->map(['GET'], '', \App\v1\Controllers\Interfacetype::class . ':showAll');
        $interfacetypes->group("/new", function (RouteCollectorProxy $interfacetypeNew)
        {
          $interfacetypeNew->map(['GET'], '', \App\v1\Controllers\Interfacetype::class . ':showNewItem');
          $interfacetypeNew->map(['POST'], '', \App\v1\Controllers\Interfacetype::class . ':newItem');
        });

        $interfacetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $interfacetypeId)
        {
          $interfacetypeId->map(['GET'], '', \App\v1\Controllers\Interfacetype::class . ':showItem');
          $interfacetypeId->map(['POST'], '', \App\v1\Controllers\Interfacetype::class . ':updateItem');
          $interfacetypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Interfacetype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Interfacetype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Interfacetype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicecasetypes', function (RouteCollectorProxy $devicecasetypes)
      {
        $devicecasetypes->map(['GET'], '', \App\v1\Controllers\Devicecasetype::class . ':showAll');
        $devicecasetypes->group("/new", function (RouteCollectorProxy $casetypeNew)
        {
          $casetypeNew->map(['GET'], '', \App\v1\Controllers\Devicecasetype::class . ':showNewItem');
          $casetypeNew->map(['POST'], '', \App\v1\Controllers\Devicecasetype::class . ':newItem');
        });

        $devicecasetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecasetypeId)
        {
          $devicecasetypeId->map(['GET'], '', \App\v1\Controllers\Devicecasetype::class . ':showItem');
          $devicecasetypeId->map(['POST'], '', \App\v1\Controllers\Devicecasetype::class . ':updateItem');
          $devicecasetypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicecasetype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicecasetype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecasetype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/phonepowersupplies', function (RouteCollectorProxy $phonepowersupplies)
      {
        $phonepowersupplies->map(['GET'], '', \App\v1\Controllers\Phonepowersupply::class . ':showAll');
        $phonepowersupplies->group("/new", function (RouteCollectorProxy $ppowersupplyNew)
        {
          $ppowersupplyNew->map(['GET'], '', \App\v1\Controllers\Phonepowersupply::class . ':showNewItem');
          $ppowersupplyNew->map(['POST'], '', \App\v1\Controllers\Phonepowersupply::class . ':newItem');
        });

        $phonepowersupplies->group("/{id:[0-9]+}", function (RouteCollectorProxy $phonepowersupplyId)
        {
          $phonepowersupplyId->map(['GET'], '', \App\v1\Controllers\Phonepowersupply::class . ':showItem');
          $phonepowersupplyId->map(['POST'], '', \App\v1\Controllers\Phonepowersupply::class . ':updateItem');
          $phonepowersupplyId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Phonepowersupply::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Phonepowersupply::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Phonepowersupply::class . ':showSubHistory');
          });
        });
      });

      $view->group('/filesystems', function (RouteCollectorProxy $filesystems)
      {
        $filesystems->map(['GET'], '', \App\v1\Controllers\Filesystem::class . ':showAll');
        $filesystems->group("/new", function (RouteCollectorProxy $filesystemNew)
        {
          $filesystemNew->map(['GET'], '', \App\v1\Controllers\Filesystem::class . ':showNewItem');
          $filesystemNew->map(['POST'], '', \App\v1\Controllers\Filesystem::class . ':newItem');
        });

        $filesystems->group("/{id:[0-9]+}", function (RouteCollectorProxy $filesystemId)
        {
          $filesystemId->map(['GET'], '', \App\v1\Controllers\Filesystem::class . ':showItem');
          $filesystemId->map(['POST'], '', \App\v1\Controllers\Filesystem::class . ':updateItem');
          $filesystemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Filesystem::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Filesystem::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Filesystem::class . ':showSubHistory');
          });
        });
      });

      $view->group('/certificatetypes', function (RouteCollectorProxy $certificatetypes)
      {
        $certificatetypes->map(['GET'], '', \App\v1\Controllers\Certificatetype::class . ':showAll');
        $certificatetypes->group("/new", function (RouteCollectorProxy $certtypeNew)
        {
          $certtypeNew->map(['GET'], '', \App\v1\Controllers\Certificatetype::class . ':showNewItem');
          $certtypeNew->map(['POST'], '', \App\v1\Controllers\Certificatetype::class . ':newItem');
        });

        $certificatetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $certificatetypeId)
        {
          $certificatetypeId->map(['GET'], '', \App\v1\Controllers\Certificatetype::class . ':showItem');
          $certificatetypeId->map(['POST'], '', \App\v1\Controllers\Certificatetype::class . ':updateItem');
          $certificatetypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Certificatetype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Certificatetype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Certificatetype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/budgettypes', function (RouteCollectorProxy $budgettypes)
      {
        $budgettypes->map(['GET'], '', \App\v1\Controllers\Budgettype::class . ':showAll');
        $budgettypes->group("/new", function (RouteCollectorProxy $budgettypeNew)
        {
          $budgettypeNew->map(['GET'], '', \App\v1\Controllers\Budgettype::class . ':showNewItem');
          $budgettypeNew->map(['POST'], '', \App\v1\Controllers\Budgettype::class . ':newItem');
        });

        $budgettypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $budgettypeId)
        {
          $budgettypeId->map(['GET'], '', \App\v1\Controllers\Budgettype::class . ':showItem');
          $budgettypeId->map(['POST'], '', \App\v1\Controllers\Budgettype::class . ':updateItem');
          $budgettypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Budgettype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Budgettype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Budgettype::class . ':showSubHistory');
          });
        });
      });
      $view->group('/devicesimcardtypes', function (RouteCollectorProxy $devicesimcardtypes)
      {
        $devicesimcardtypes->map(['GET'], '', \App\v1\Controllers\Devicesimcardtype::class . ':showAll');
        $devicesimcardtypes->group("/new", function (RouteCollectorProxy $simcardtypeNew)
        {
          $simcardtypeNew->map(['GET'], '', \App\v1\Controllers\Devicesimcardtype::class . ':showNewItem');
          $simcardtypeNew->map(['POST'], '', \App\v1\Controllers\Devicesimcardtype::class . ':newItem');
        });

        $devicesimcardtypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesimcardtypeId)
        {
          $devicesimcardtypeId->map(['GET'], '', \App\v1\Controllers\Devicesimcardtype::class . ':showItem');
          $devicesimcardtypeId->map(['POST'], '', \App\v1\Controllers\Devicesimcardtype::class . ':updateItem');
          $devicesimcardtypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicesimcardtype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicesimcardtype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesimcardtype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/linetypes', function (RouteCollectorProxy $linetypes)
      {
        $linetypes->map(['GET'], '', \App\v1\Controllers\Linetype::class . ':showAll');
        $linetypes->group("/new", function (RouteCollectorProxy $linetypeNew)
        {
          $linetypeNew->map(['GET'], '', \App\v1\Controllers\Linetype::class . ':showNewItem');
          $linetypeNew->map(['POST'], '', \App\v1\Controllers\Linetype::class . ':newItem');
        });

        $linetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $linetypeId)
        {
          $linetypeId->map(['GET'], '', \App\v1\Controllers\Linetype::class . ':showItem');
          $linetypeId->map(['POST'], '', \App\v1\Controllers\Linetype::class . ':updateItem');
          $linetypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Linetype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Linetype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Linetype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/racktypes', function (RouteCollectorProxy $racktypes)
      {
        $racktypes->map(['GET'], '', \App\v1\Controllers\Racktype::class . ':showAll');
        $racktypes->group("/new", function (RouteCollectorProxy $racktypeNew)
        {
          $racktypeNew->map(['GET'], '', \App\v1\Controllers\Racktype::class . ':showNewItem');
          $racktypeNew->map(['POST'], '', \App\v1\Controllers\Racktype::class . ':newItem');
        });

        $racktypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $racktypeId)
        {
          $racktypeId->map(['GET'], '', \App\v1\Controllers\Racktype::class . ':showItem');
          $racktypeId->map(['POST'], '', \App\v1\Controllers\Racktype::class . ':updateItem');
          $racktypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Racktype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Racktype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Racktype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/pdutypes', function (RouteCollectorProxy $pdutypes)
      {
        $pdutypes->map(['GET'], '', \App\v1\Controllers\Pdutype::class . ':showAll');
        $pdutypes->group("/new", function (RouteCollectorProxy $pdutypeNew)
        {
          $pdutypeNew->map(['GET'], '', \App\v1\Controllers\Pdutype::class . ':showNewItem');
          $pdutypeNew->map(['POST'], '', \App\v1\Controllers\Pdutype::class . ':newItem');
        });

        $pdutypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $pdutypeId)
        {
          $pdutypeId->map(['GET'], '', \App\v1\Controllers\Pdutype::class . ':showItem');
          $pdutypeId->map(['POST'], '', \App\v1\Controllers\Pdutype::class . ':updateItem');
          $pdutypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Pdutype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Pdutype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Pdutype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/passivedcequipmenttypes', function (RouteCollectorProxy $passivedcequipmenttypes)
      {
        $passivedcequipmenttypes->map(['GET'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':showAll');
        $passivedcequipmenttypes->group("/new", function (RouteCollectorProxy $petypeNew)
        {
          $petypeNew->map(['GET'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':showNewItem');
          $petypeNew->map(['POST'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':newItem');
        });

        $passivedcequipmenttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $pcetId)
        {
          $pcetId->map(['GET'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':showItem');
          $pcetId->map(['POST'], '', \App\v1\Controllers\Passivedcequipmenttype::class . ':updateItem');
          $pcetId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Passivedcequipmenttype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Passivedcequipmenttype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Passivedcequipmenttype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/clustertypes', function (RouteCollectorProxy $clustertypes)
      {
        $clustertypes->map(['GET'], '', \App\v1\Controllers\Clustertype::class . ':showAll');
        $clustertypes->group("/new", function (RouteCollectorProxy $clustertypeNew)
        {
          $clustertypeNew->map(['GET'], '', \App\v1\Controllers\Clustertype::class . ':showNewItem');
          $clustertypeNew->map(['POST'], '', \App\v1\Controllers\Clustertype::class . ':newItem');
        });

        $clustertypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $clustertypeId)
        {
          $clustertypeId->map(['GET'], '', \App\v1\Controllers\Clustertype::class . ':showItem');
          $clustertypeId->map(['POST'], '', \App\v1\Controllers\Clustertype::class . ':updateItem');
          $clustertypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Clustertype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Clustertype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Clustertype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/computermodels', function (RouteCollectorProxy $computermodels)
      {
        $computermodels->map(['GET'], '', \App\v1\Controllers\Computermodel::class . ':showAll');
        $computermodels->group("/new", function (RouteCollectorProxy $cmodelNew)
        {
          $cmodelNew->map(['GET'], '', \App\v1\Controllers\Computermodel::class . ':showNewItem');
          $cmodelNew->map(['POST'], '', \App\v1\Controllers\Computermodel::class . ':newItem');
        });

        $computermodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $computermodelId)
        {
          $computermodelId->map(['GET'], '', \App\v1\Controllers\Computermodel::class . ':showItem');
          $computermodelId->map(['POST'], '', \App\v1\Controllers\Computermodel::class . ':updateItem');
          $computermodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Computermodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Computermodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Computermodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/networkequipmentmodels', function (RouteCollectorProxy $networkequipmentmodels)
      {
        $networkequipmentmodels->map(['GET'], '', \App\v1\Controllers\Networkequipmentmodel::class . ':showAll');
        $networkequipmentmodels->group("/new", function (RouteCollectorProxy $nemodelNew)
        {
          $nemodelNew->map(['GET'], '', \App\v1\Controllers\Networkequipmentmodel::class . ':showNewItem');
          $nemodelNew->map(['POST'], '', \App\v1\Controllers\Networkequipmentmodel::class . ':newItem');
        });

        $networkequipmentmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkequipmentmodelId)
        {
          $networkequipmentmodelId->map(['GET'], '', \App\v1\Controllers\Networkequipmentmodel::class . ':showItem');
          $networkequipmentmodelId->map(
            ['POST'],
            '',
            \App\v1\Controllers\Networkequipmentmodel::class . ':updateItem'
          );
          $networkequipmentmodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Networkequipmentmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Networkequipmentmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Networkequipmentmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/printermodels', function (RouteCollectorProxy $printermodels)
      {
        $printermodels->map(['GET'], '', \App\v1\Controllers\Printermodel::class . ':showAll');
        $printermodels->group("/new", function (RouteCollectorProxy $pmodelNew)
        {
          $pmodelNew->map(['GET'], '', \App\v1\Controllers\Printermodel::class . ':showNewItem');
          $pmodelNew->map(['POST'], '', \App\v1\Controllers\Printermodel::class . ':newItem');
        });

        $printermodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $printermodelId)
        {
          $printermodelId->map(['GET'], '', \App\v1\Controllers\Printermodel::class . ':showItem');
          $printermodelId->map(['POST'], '', \App\v1\Controllers\Printermodel::class . ':updateItem');
          $printermodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Printermodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Printermodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Printermodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/monitormodels', function (RouteCollectorProxy $monitormodels)
      {
        $monitormodels->map(['GET'], '', \App\v1\Controllers\Monitormodel::class . ':showAll');
        $monitormodels->group("/new", function (RouteCollectorProxy $modelNew)
        {
          $modelNew->map(['GET'], '', \App\v1\Controllers\Monitormodel::class . ':showNewItem');
          $modelNew->map(['POST'], '', \App\v1\Controllers\Monitormodel::class . ':newItem');
        });

        $monitormodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $monitormodelId)
        {
          $monitormodelId->map(['GET'], '', \App\v1\Controllers\Monitormodel::class . ':showItem');
          $monitormodelId->map(['POST'], '', \App\v1\Controllers\Monitormodel::class . ':updateItem');
          $monitormodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Monitormodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Monitormodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Monitormodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/peripheralmodels', function (RouteCollectorProxy $peripheralmodels)
      {
        $peripheralmodels->map(['GET'], '', \App\v1\Controllers\Peripheralmodel::class . ':showAll');
        $peripheralmodels->group("/new", function (RouteCollectorProxy $pmodelNew)
        {
          $pmodelNew->map(['GET'], '', \App\v1\Controllers\Peripheralmodel::class . ':showNewItem');
          $pmodelNew->map(['POST'], '', \App\v1\Controllers\Peripheralmodel::class . ':newItem');
        });

        $peripheralmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $peripheralmodelId)
        {
          $peripheralmodelId->map(['GET'], '', \App\v1\Controllers\Peripheralmodel::class . ':showItem');
          $peripheralmodelId->map(['POST'], '', \App\v1\Controllers\Peripheralmodel::class . ':updateItem');
          $peripheralmodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Peripheralmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Peripheralmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Peripheralmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/phonemodels', function (RouteCollectorProxy $phonemodels)
      {
        $phonemodels->map(['GET'], '', \App\v1\Controllers\Phonemodel::class . ':showAll');
        $phonemodels->group("/new", function (RouteCollectorProxy $phonemodelNew)
        {
          $phonemodelNew->map(['GET'], '', \App\v1\Controllers\Phonemodel::class . ':showNewItem');
          $phonemodelNew->map(['POST'], '', \App\v1\Controllers\Phonemodel::class . ':newItem');
        });

        $phonemodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $phonemodelId)
        {
          $phonemodelId->map(['GET'], '', \App\v1\Controllers\Phonemodel::class . ':showItem');
          $phonemodelId->map(['POST'], '', \App\v1\Controllers\Phonemodel::class . ':updateItem');
          $phonemodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Phonemodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Phonemodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Phonemodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicecasemodels', function (RouteCollectorProxy $devicecasemodels)
      {
        $devicecasemodels->map(['GET'], '', \App\v1\Controllers\Devicecasemodel::class . ':showAll');
        $devicecasemodels->group("/new", function (RouteCollectorProxy $casemodelNew)
        {
          $casemodelNew->map(['GET'], '', \App\v1\Controllers\Devicecasemodel::class . ':showNewItem');
          $casemodelNew->map(['POST'], '', \App\v1\Controllers\Devicecasemodel::class . ':newItem');
        });

        $devicecasemodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecasemodelId)
        {
          $devicecasemodelId->map(['GET'], '', \App\v1\Controllers\Devicecasemodel::class . ':showItem');
          $devicecasemodelId->map(['POST'], '', \App\v1\Controllers\Devicecasemodel::class . ':updateItem');
          $devicecasemodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicecasemodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicecasemodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecasemodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicecontrolmodels', function (RouteCollectorProxy $devicecontrolmodels)
      {
        $devicecontrolmodels->map(['GET'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':showAll');
        $devicecontrolmodels->group("/new", function (RouteCollectorProxy $dcmodelNew)
        {
          $dcmodelNew->map(['GET'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':showNewItem');
          $dcmodelNew->map(['POST'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':newItem');
        });

        $devicecontrolmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecontrolmodelId)
        {
          $devicecontrolmodelId->map(['GET'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':showItem');
          $devicecontrolmodelId->map(['POST'], '', \App\v1\Controllers\Devicecontrolmodel::class . ':updateItem');
          $devicecontrolmodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicecontrolmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicecontrolmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecontrolmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicedrivemodels', function (RouteCollectorProxy $devicedrivemodels)
      {
        $devicedrivemodels->map(['GET'], '', \App\v1\Controllers\Devicedrivemodel::class . ':showAll');
        $devicedrivemodels->group("/new", function (RouteCollectorProxy $drivemodelNew)
        {
          $drivemodelNew->map(['GET'], '', \App\v1\Controllers\Devicedrivemodel::class . ':showNewItem');
          $drivemodelNew->map(['POST'], '', \App\v1\Controllers\Devicedrivemodel::class . ':newItem');
        });

        $devicedrivemodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicedrivemodelId)
        {
          $devicedrivemodelId->map(['GET'], '', \App\v1\Controllers\Devicedrivemodel::class . ':showItem');
          $devicedrivemodelId->map(['POST'], '', \App\v1\Controllers\Devicedrivemodel::class . ':updateItem');
          $devicedrivemodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicedrivemodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicedrivemodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicedrivemodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicegenericmodels', function (RouteCollectorProxy $devicegenericmodels)
      {
        $devicegenericmodels->map(['GET'], '', \App\v1\Controllers\Devicegenericmodel::class . ':showAll');
        $devicegenericmodels->map(['POST'], '', \App\v1\Controllers\Devicegenericmodel::class . ':postItem');
        $devicegenericmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicegenericmodelId)
        {
          $devicegenericmodelId->map(['GET'], '', \App\v1\Controllers\Devicegenericmodel::class . ':showItem');
          $devicegenericmodelId->map(['POST'], '', \App\v1\Controllers\Devicegenericmodel::class . ':updateitem');
          $devicegenericmodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegenericmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicegraphiccardmodels', function (RouteCollectorProxy $devicegraphiccardmodels)
      {
        $devicegraphiccardmodels->map(['GET'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':showAll');
        $devicegraphiccardmodels->group("/new", function (RouteCollectorProxy $dgcmodelNew)
        {
          $dgcmodelNew->map(['GET'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':showNewItem');
          $dgcmodelNew->map(['POST'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':newItem');
        });

        $devicegraphiccardmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $dgcmId)
        {
          $dgcmId->map(['GET'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':showItem');
          $dgcmId->map(['POST'], '', \App\v1\Controllers\Devicegraphiccardmodel::class . ':updateItem');
          $dgcmId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicegraphiccardmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicegraphiccardmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegraphiccardmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/deviceharddrivemodels', function (RouteCollectorProxy $deviceharddrivemodels)
      {
        $deviceharddrivemodels->map(['GET'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':showAll');
        $deviceharddrivemodels->group("/new", function (RouteCollectorProxy $dhdmodelNew)
        {
          $dhdmodelNew->map(['GET'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':showNewItem');
          $dhdmodelNew->map(['POST'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':newItem');
        });

        $deviceharddrivemodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $deviceharddrivemodelId)
        {
          $deviceharddrivemodelId->map(['GET'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':showItem');
          $deviceharddrivemodelId->map(['POST'], '', \App\v1\Controllers\Deviceharddrivemodel::class . ':updateItem');
          $deviceharddrivemodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Deviceharddrivemodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Deviceharddrivemodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Deviceharddrivemodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicememorymodels', function (RouteCollectorProxy $devicememorymodels)
      {
        $devicememorymodels->map(['GET'], '', \App\v1\Controllers\Devicememorymodel::class . ':showAll');
        $devicememorymodels->group("/new", function (RouteCollectorProxy $memorymodelNew)
        {
          $memorymodelNew->map(['GET'], '', \App\v1\Controllers\Devicememorymodel::class . ':showNewItem');
          $memorymodelNew->map(['POST'], '', \App\v1\Controllers\Devicememorymodel::class . ':newItem');
        });

        $devicememorymodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicememorymodelId)
        {
          $devicememorymodelId->map(['GET'], '', \App\v1\Controllers\Devicememorymodel::class . ':showItem');
          $devicememorymodelId->map(['POST'], '', \App\v1\Controllers\Devicememorymodel::class . ':updateItem');
          $devicememorymodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicememorymodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicememorymodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicememorymodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicemotherboardmodels', function (RouteCollectorProxy $devicemotherboardmodels)
      {
        $devicemotherboardmodels->map(['GET'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':showAll');
        $devicemotherboardmodels->group("/new", function (RouteCollectorProxy $motherboardmodelNew)
        {
          $motherboardmodelNew->map(['GET'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':showNewItem');
          $motherboardmodelNew->map(['POST'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':newItem');
        });

        $devicemotherboardmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $dmmId)
        {
          $dmmId->map(['GET'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':showItem');
          $dmmId->map(['POST'], '', \App\v1\Controllers\Devicemotherboardmodel::class . ':updateItem');
          $dmmId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicemotherboardmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicemotherboardmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicemotherboardmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicenetworkcardmodels', function (RouteCollectorProxy $devicenetworkcardmodels)
      {
        $devicenetworkcardmodels->map(['GET'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':showAll');
        $devicenetworkcardmodels->group("/new", function (RouteCollectorProxy $networkcardmodelNew)
        {
          $networkcardmodelNew->map(['GET'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':showNewItem');
          $networkcardmodelNew->map(['POST'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':newItem');
        });

        $devicenetworkcardmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $dncmId)
        {
          $dncmId->map(['GET'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':showItem');
          $dncmId->map(['POST'], '', \App\v1\Controllers\Devicenetworkcardmodel::class . ':updateItem');
          $dncmId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicenetworkcardmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicenetworkcardmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicenetworkcardmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicepcimodels', function (RouteCollectorProxy $devicepcimodels)
      {
        $devicepcimodels->map(['GET'], '', \App\v1\Controllers\Devicepcimodel::class . ':showAll');
        $devicepcimodels->group("/new", function (RouteCollectorProxy $pcimodelNew)
        {
          $pcimodelNew->map(['GET'], '', \App\v1\Controllers\Devicepcimodel::class . ':showNewItem');
          $pcimodelNew->map(['POST'], '', \App\v1\Controllers\Devicepcimodel::class . ':newItem');
        });

        $devicepcimodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicepcimodelId)
        {
          $devicepcimodelId->map(['GET'], '', \App\v1\Controllers\Devicepcimodel::class . ':showItem');
          $devicepcimodelId->map(['POST'], '', \App\v1\Controllers\Devicepcimodel::class . ':updateItem');
          $devicepcimodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicepcimodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicepcimodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicepcimodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicepowersupplymodels', function (RouteCollectorProxy $devicepowersupplymodels)
      {
        $devicepowersupplymodels->map(['GET'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':showAll');
        $devicepowersupplymodels->group("/new", function (RouteCollectorProxy $dpsmodelNew)
        {
          $dpsmodelNew->map(['GET'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':showNewItem');
          $dpsmodelNew->map(['POST'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':newItem');
        });

        $devicepowersupplymodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $dpsmId)
        {
          $dpsmId->map(['GET'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':showItem');
          $dpsmId->map(['POST'], '', \App\v1\Controllers\Devicepowersupplymodel::class . ':updateItem');
          $dpsmId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicepowersupplymodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicepowersupplymodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicepowersupplymodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/deviceprocessormodels', function (RouteCollectorProxy $deviceprocessormodels)
      {
        $deviceprocessormodels->map(['GET'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':showAll');
        $deviceprocessormodels->group("/new", function (RouteCollectorProxy $processormodelNew)
        {
          $processormodelNew->map(['GET'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':showNewItem');
          $processormodelNew->map(['POST'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':newItem');
        });

        $deviceprocessormodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $deviceprocessormodelId)
        {
          $deviceprocessormodelId->map(['GET'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':showItem');
          $deviceprocessormodelId->map(['POST'], '', \App\v1\Controllers\Deviceprocessormodel::class . ':updateItem');
          $deviceprocessormodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Deviceprocessormodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Deviceprocessormodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Deviceprocessormodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devicesoundcardmodels', function (RouteCollectorProxy $devicesoundcardmodels)
      {
        $devicesoundcardmodels->map(['GET'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':showAll');
        $devicesoundcardmodels->group("/new", function (RouteCollectorProxy $dscmodelNew)
        {
          $dscmodelNew->map(['GET'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':showNewItem');
          $dscmodelNew->map(['POST'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':newItem');
        });

        $devicesoundcardmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesoundcardmodelId)
        {
          $devicesoundcardmodelId->map(['GET'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':showItem');
          $devicesoundcardmodelId->map(['POST'], '', \App\v1\Controllers\Devicesoundcardmodel::class . ':updateItem');
          $devicesoundcardmodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicesoundcardmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicesoundcardmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesoundcardmodel::class . ':showSubHistory');
          });
        });
      });
      $view->group('/devicesensormodels', function (RouteCollectorProxy $devicesensormodels)
      {
        $devicesensormodels->map(['GET'], '', \App\v1\Controllers\Devicesensormodel::class . ':showAll');
        $devicesensormodels->map(['POST'], '', \App\v1\Controllers\Devicesensormodel::class . ':postItem');
        $devicesensormodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesensormodelId)
        {
          $devicesensormodelId->map(['GET'], '', \App\v1\Controllers\Devicesensormodel::class . ':showItem');
          $devicesensormodelId->map(['POST'], '', \App\v1\Controllers\Devicesensormodel::class . ':updateItem');
          $devicesensormodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesensormodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/rackmodels', function (RouteCollectorProxy $rackmodels)
      {
        $rackmodels->map(['GET'], '', \App\v1\Controllers\Rackmodel::class . ':showAll');
        $rackmodels->group("/new", function (RouteCollectorProxy $rackmodelNew)
        {
          $rackmodelNew->map(['GET'], '', \App\v1\Controllers\Rackmodel::class . ':showNewItem');
          $rackmodelNew->map(['POST'], '', \App\v1\Controllers\Rackmodel::class . ':newItem');
        });

        $rackmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $rackmodelId)
        {
          $rackmodelId->map(['GET'], '', \App\v1\Controllers\Rackmodel::class . ':showItem');
          $rackmodelId->map(['POST'], '', \App\v1\Controllers\Rackmodel::class . ':updateItem');
          $rackmodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Rackmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Rackmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Rackmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/enclosuremodels', function (RouteCollectorProxy $enclosuremodels)
      {
        $enclosuremodels->map(['GET'], '', \App\v1\Controllers\Enclosuremodel::class . ':showAll');
        $enclosuremodels->group("/new", function (RouteCollectorProxy $encmodelNew)
        {
          $encmodelNew->map(['GET'], '', \App\v1\Controllers\Enclosuremodel::class . ':showNewItem');
          $encmodelNew->map(['POST'], '', \App\v1\Controllers\Enclosuremodel::class . ':newItem');
        });

        $enclosuremodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $enclosuremodelId)
        {
          $enclosuremodelId->map(['GET'], '', \App\v1\Controllers\Enclosuremodel::class . ':showItem');
          $enclosuremodelId->map(['POST'], '', \App\v1\Controllers\Enclosuremodel::class . ':updateItem');
          $enclosuremodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Enclosuremodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Enclosuremodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Enclosuremodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/pdumodels', function (RouteCollectorProxy $pdumodels)
      {
        $pdumodels->map(['GET'], '', \App\v1\Controllers\Pdumodel::class . ':showAll');
        $pdumodels->group("/new", function (RouteCollectorProxy $pdumodelNew)
        {
          $pdumodelNew->map(['GET'], '', \App\v1\Controllers\Pdumodel::class . ':showNewItem');
          $pdumodelNew->map(['POST'], '', \App\v1\Controllers\Pdumodel::class . ':newItem');
        });

        $pdumodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $pdumodelId)
        {
          $pdumodelId->map(['GET'], '', \App\v1\Controllers\Pdumodel::class . ':showItem');
          $pdumodelId->map(['POST'], '', \App\v1\Controllers\Pdumodel::class . ':updateItem');
          $pdumodelId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Pdumodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Pdumodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Pdumodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/passivedcequipmentmodels', function (RouteCollectorProxy $passivedcequipmentmodels)
      {
        $passivedcequipmentmodels->map(['GET'], '', \App\v1\Controllers\Passivedcequipmentmodel::class . ':showAll');
        $passivedcequipmentmodels->group("/new", function (RouteCollectorProxy $pemodelNew)
        {
          $pemodelNew->map(['GET'], '', \App\v1\Controllers\Passivedcequipmentmodel::class . ':showNewItem');
          $pemodelNew->map(['POST'], '', \App\v1\Controllers\Passivedcequipmentmodel::class . ':newItem');
        });

        $passivedcequipmentmodels->group("/{id:[0-9]+}", function (RouteCollectorProxy $pcemId)
        {
          $pcemId->map(['GET'], '', \App\v1\Controllers\Passivedcequipmentmodel::class . ':showItem');
          $pcemId->map(['POST'], '', \App\v1\Controllers\Passivedcequipmentmodel::class . ':updateItem');
          $pcemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Passivedcequipmentmodel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Passivedcequipmentmodel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Passivedcequipmentmodel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/virtualmachinetypes', function (RouteCollectorProxy $virtualmachinetypes)
      {
        $virtualmachinetypes->map(['GET'], '', \App\v1\Controllers\Virtualmachinetype::class . ':showAll');
        $virtualmachinetypes->group("/new", function (RouteCollectorProxy $vmtypeNew)
        {
          $vmtypeNew->map(['GET'], '', \App\v1\Controllers\Virtualmachinetype::class . ':showNewItem');
          $vmtypeNew->map(['POST'], '', \App\v1\Controllers\Virtualmachinetype::class . ':newItem');
        });

        $virtualmachinetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $virtualmachinetypeId)
        {
          $virtualmachinetypeId->map(['GET'], '', \App\v1\Controllers\Virtualmachinetype::class . ':showItem');
          $virtualmachinetypeId->map(['POST'], '', \App\v1\Controllers\Virtualmachinetype::class . ':updateItem');
          $virtualmachinetypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Virtualmachinetype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Virtualmachinetype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Virtualmachinetype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/virtualmachinesystems', function (RouteCollectorProxy $virtualmachinesystems)
      {
        $virtualmachinesystems->map(['GET'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':showAll');
        $virtualmachinesystems->group("/new", function (RouteCollectorProxy $vmsystemNew)
        {
          $vmsystemNew->map(['GET'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':showNewItem');
          $vmsystemNew->map(['POST'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':newItem');
        });

        $virtualmachinesystems->group("/{id:[0-9]+}", function (RouteCollectorProxy $virtualmachinesystemId)
        {
          $virtualmachinesystemId->map(['GET'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':showItem');
          $virtualmachinesystemId->map(['POST'], '', \App\v1\Controllers\Virtualmachinesystem::class . ':updateItem');
          $virtualmachinesystemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Virtualmachinesystem::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Virtualmachinesystem::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Virtualmachinesystem::class . ':showSubHistory');
          });
        });
      });

      $view->group('/virtualmachinestates', function (RouteCollectorProxy $virtualmachinestates)
      {
        $virtualmachinestates->map(['GET'], '', \App\v1\Controllers\Virtualmachinestate::class . ':showAll');
        $virtualmachinestates->group("/new", function (RouteCollectorProxy $vmstateNew)
        {
          $vmstateNew->map(['GET'], '', \App\v1\Controllers\Virtualmachinestate::class . ':showNewItem');
          $vmstateNew->map(['POST'], '', \App\v1\Controllers\Virtualmachinestate::class . ':newItem');
        });

        $virtualmachinestates->group("/{id:[0-9]+}", function (RouteCollectorProxy $virtualmachinestateId)
        {
          $virtualmachinestateId->map(['GET'], '', \App\v1\Controllers\Virtualmachinestate::class . ':showItem');
          $virtualmachinestateId->map(['POST'], '', \App\v1\Controllers\Virtualmachinestate::class . ':updateItem');
          $virtualmachinestateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Virtualmachinestate::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Virtualmachinestate::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Virtualmachinestate::class . ':showSubHistory');
          });
        });
      });

      $view->group('/documentcategories', function (RouteCollectorProxy $documentcategories)
      {
        $documentcategories->map(['GET'], '', \App\v1\Controllers\Documentcategory::class . ':showAll');
        $documentcategories->group("/new", function (RouteCollectorProxy $doccategoryNew)
        {
          $doccategoryNew->map(['GET'], '', \App\v1\Controllers\Documentcategory::class . ':showNewItem');
          $doccategoryNew->map(['POST'], '', \App\v1\Controllers\Documentcategory::class . ':newItem');
        });

        $documentcategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $documentcategoryId)
        {
          $documentcategoryId->map(['GET'], '', \App\v1\Controllers\Documentcategory::class . ':showItem');
          $documentcategoryId->map(['POST'], '', \App\v1\Controllers\Documentcategory::class . ':updateItem');
          $documentcategoryId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Documentcategory::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Documentcategory::class . ':restoreItem');
            $sub->map(
              ['GET'],
              'categories',
              \App\v1\Controllers\Documentcategory::class . ':showSubDocumentcategories'
            );
            $sub->map(['GET'], 'history', \App\v1\Controllers\Documentcategory::class . ':showSubHistory');
          });
        });
      });

      $view->group('/documenttypes', function (RouteCollectorProxy $documenttypes)
      {
        $documenttypes->map(['GET'], '', \App\v1\Controllers\Documenttype::class . ':showAll');
        $documenttypes->group("/new", function (RouteCollectorProxy $doctypeNew)
        {
          $doctypeNew->map(['GET'], '', \App\v1\Controllers\Documenttype::class . ':showNewItem');
          $doctypeNew->map(['POST'], '', \App\v1\Controllers\Documenttype::class . ':newItem');
        });

        $documenttypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $documenttypeId)
        {
          $documenttypeId->map(['GET'], '', \App\v1\Controllers\Documenttype::class . ':showItem');
          $documenttypeId->map(['POST'], '', \App\v1\Controllers\Documenttype::class . ':updateItem');
          $documenttypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Documenttype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Documenttype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Documenttype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/businesscriticities', function (RouteCollectorProxy $businesscriticities)
      {
        $businesscriticities->map(['GET'], '', \App\v1\Controllers\Businesscriticity::class . ':showAll');
        $businesscriticities->group("/new", function (RouteCollectorProxy $bcNew)
        {
          $bcNew->map(['GET'], '', \App\v1\Controllers\Businesscriticity::class . ':showNewItem');
          $bcNew->map(['POST'], '', \App\v1\Controllers\Businesscriticity::class . ':newItem');
        });

        $businesscriticities->group("/{id:[0-9]+}", function (RouteCollectorProxy $businesscriticityId)
        {
          $businesscriticityId->map(['GET'], '', \App\v1\Controllers\Businesscriticity::class . ':showItem');
          $businesscriticityId->map(['POST'], '', \App\v1\Controllers\Businesscriticity::class . ':updateItem');
          $businesscriticityId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Businesscriticity::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Businesscriticity::class . ':restoreItem');
            $sub->map(
              ['GET'],
              'businesscriticities',
              \App\v1\Controllers\Businesscriticity::class . ':showSubBusinesscriticities'
            );
            $sub->map(['GET'], 'history', \App\v1\Controllers\Businesscriticity::class . ':showSubHistory');
          });
        });
      });

      $view->group('/calendars', function (RouteCollectorProxy $calendars)
      {
        $calendars->map(['GET'], '', \App\v1\Controllers\Calendar::class . ':showAll');
        $calendars->group("/new", function (RouteCollectorProxy $calendarNew)
        {
          $calendarNew->map(['GET'], '', \App\v1\Controllers\Calendar::class . ':showNewItem');
          $calendarNew->map(['POST'], '', \App\v1\Controllers\Calendar::class . ':newItem');
        });

        $calendars->group("/{id:[0-9]+}", function (RouteCollectorProxy $calendarId)
        {
          $calendarId->map(['GET'], '', \App\v1\Controllers\Calendar::class . ':showItem');
          $calendarId->map(['POST'], '', \App\v1\Controllers\Calendar::class . ':updateItem');
          $calendarId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Calendar::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Calendar::class . ':restoreItem');
            $sub->map(['GET'], 'timeranges', \App\v1\Controllers\Calendar::class . ':showSubTimeranges');
            $sub->map(['GET'], 'holidays', \App\v1\Controllers\Calendar::class . ':showSubHolidays');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Calendar::class . ':showSubHistory');
          });
        });
      });

      $view->group('/holidays', function (RouteCollectorProxy $holidays)
      {
        $holidays->map(['GET'], '', \App\v1\Controllers\Holiday::class . ':showAll');
        $holidays->group("/new", function (RouteCollectorProxy $holidayNew)
        {
          $holidayNew->map(['GET'], '', \App\v1\Controllers\Holiday::class . ':showNewItem');
          $holidayNew->map(['POST'], '', \App\v1\Controllers\Holiday::class . ':newItem');
        });

        $holidays->group("/{id:[0-9]+}", function (RouteCollectorProxy $holidayId)
        {
          $holidayId->map(['GET'], '', \App\v1\Controllers\Holiday::class . ':showItem');
          $holidayId->map(['POST'], '', \App\v1\Controllers\Holiday::class . ':updateItem');
          $holidayId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Holiday::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Holiday::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Holiday::class . ':showSubHistory');
          });
        });
      });

      $view->group('/operatingsystems', function (RouteCollectorProxy $operatingsystems)
      {
        $operatingsystems->map(['GET'], '', \App\v1\Controllers\Operatingsystem::class . ':showAll');
        $operatingsystems->group("/new", function (RouteCollectorProxy $osNew)
        {
          $osNew->map(['GET'], '', \App\v1\Controllers\Operatingsystem::class . ':showNewItem');
          $osNew->map(['POST'], '', \App\v1\Controllers\Operatingsystem::class . ':newItem');
        });

        $operatingsystems->group("/{id:[0-9]+}", function (RouteCollectorProxy $operatingsystemId)
        {
          $operatingsystemId->map(['GET'], '', \App\v1\Controllers\Operatingsystem::class . ':showItem');
          $operatingsystemId->map(['POST'], '', \App\v1\Controllers\Operatingsystem::class . ':updateItem');
          $operatingsystemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Operatingsystem::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Operatingsystem::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystem::class . ':showSubHistory');
          });
        });
      });

      $view->group('/operatingsystemversions', function (RouteCollectorProxy $operatingsystemversions)
      {
        $operatingsystemversions->map(['GET'], '', \App\v1\Controllers\Operatingsystemversion::class . ':showAll');
        $operatingsystemversions->group("/new", function (RouteCollectorProxy $osvNew)
        {
          $osvNew->map(['GET'], '', \App\v1\Controllers\Operatingsystemversion::class . ':showNewItem');
          $osvNew->map(['POST'], '', \App\v1\Controllers\Operatingsystemversion::class . ':newItem');
        });

        $operatingsystemversions->group("/{id:[0-9]+}", function (RouteCollectorProxy $osvId)
        {
          $osvId->map(['GET'], '', \App\v1\Controllers\Operatingsystemversion::class . ':showItem');
          $osvId->map(['POST'], '', \App\v1\Controllers\Operatingsystemversion::class . ':updateItem');
          $osvId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Operatingsystemversion::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Operatingsystemversion::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemversion::class . ':showSubHistory');
          });
        });
      });

      $view->group('/operatingsystemservicepacks', function (RouteCollectorProxy $ossp)
      {
        $ossp->map(['GET'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':showAll');
        $ossp->group("/new", function (RouteCollectorProxy $osspNew)
        {
          $osspNew->map(['GET'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':showNewItem');
          $osspNew->map(['POST'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':newItem');
        });

        $ossp->group("/{id:[0-9]+}", function (RouteCollectorProxy $osspId)
        {
          $osspId->map(['GET'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':showItem');
          $osspId->map(['POST'], '', \App\v1\Controllers\Operatingsystemservicepack::class . ':updateItem');
          $osspId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Operatingsystemservicepack::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Operatingsystemservicepack::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemservicepack::class . ':showSubHistory');
          });
        });
      });

      $view->group('/operatingsystemarchitectures', function (RouteCollectorProxy $osa)
      {
        $osa->map(['GET'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':showAll');
        $osa->group("/new", function (RouteCollectorProxy $osaNew)
        {
          $osaNew->map(['GET'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':showNewItem');
          $osaNew->map(['POST'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':newItem');
        });

        $osa->group("/{id:[0-9]+}", function (RouteCollectorProxy $osaId)
        {
          $osaId->map(['GET'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':showItem');
          $osaId->map(['POST'], '', \App\v1\Controllers\Operatingsystemarchitecture::class . ':updateItem');
          $osaId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Operatingsystemarchitecture::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Operatingsystemarchitecture::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemarchitecture::class . ':showSubHistory');
          });
        });
      });

      $view->group('/operatingsystemeditions', function (RouteCollectorProxy $ose)
      {
        $ose->map(['GET'], '', \App\v1\Controllers\Operatingsystemedition::class . ':showAll');
        $ose->group("/new", function (RouteCollectorProxy $oseNew)
        {
          $oseNew->map(['GET'], '', \App\v1\Controllers\Operatingsystemedition::class . ':showNewItem');
          $oseNew->map(['POST'], '', \App\v1\Controllers\Operatingsystemedition::class . ':newItem');
        });

        $ose->group("/{id:[0-9]+}", function (RouteCollectorProxy $oseId)
        {
          $oseId->map(['GET'], '', \App\v1\Controllers\Operatingsystemedition::class . ':showItem');
          $oseId->map(['POST'], '', \App\v1\Controllers\Operatingsystemedition::class . ':updateItem');
          $oseId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Operatingsystemedition::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Operatingsystemedition::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemedition::class . ':showSubHistory');
          });
        });
      });

      $view->group('/operatingsystemkernels', function (RouteCollectorProxy $osk)
      {
        $osk->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':showAll');
        $osk->group("/new", function (RouteCollectorProxy $oskNew)
        {
          $oskNew->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':showNewItem');
          $oskNew->map(['POST'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':newItem');
        });

        $osk->group("/{id:[0-9]+}", function (RouteCollectorProxy $oskId)
        {
          $oskId->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':showItem');
          $oskId->map(['POST'], '', \App\v1\Controllers\Operatingsystemkernel::class . ':updateItem');
          $oskId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Operatingsystemkernel::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Operatingsystemkernel::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Operatingsystemkernel::class . ':showSubHistory');
          });
        });
      });

      $view->group('/operatingsystemkernelversions', function (RouteCollectorProxy $oskv)
      {
        $oskv->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':showAll');
        $oskv->group("/new", function (RouteCollectorProxy $oskvNew)
        {
          $oskvNew->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':showNewItem');
          $oskvNew->map(['POST'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':newItem');
        });

        $oskv->group("/{id:[0-9]+}", function (RouteCollectorProxy $oskvId)
        {
          $oskvId->map(['GET'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':showItem');
          $oskvId->map(['POST'], '', \App\v1\Controllers\Operatingsystemkernelversion::class . ':updateItem');
          $oskvId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Operatingsystemkernelversion::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Operatingsystemkernelversion::class . ':restoreItem');
            $sub->map(
              ['GET'],
              'history',
              \App\v1\Controllers\Operatingsystemkernelversion::class . ':showSubHistory'
            );
          });
        });
      });

      $view->group('/autoupdatesystems', function (RouteCollectorProxy $autoupdatesystems)
      {
        $autoupdatesystems->map(['GET'], '', \App\v1\Controllers\Autoupdatesystem::class . ':showAll');
        $autoupdatesystems->group("/new", function (RouteCollectorProxy $autoupdatesystemNew)
        {
          $autoupdatesystemNew->map(['GET'], '', \App\v1\Controllers\Autoupdatesystem::class . ':showNewItem');
          $autoupdatesystemNew->map(['POST'], '', \App\v1\Controllers\Autoupdatesystem::class . ':newItem');
        });

        $autoupdatesystems->group("/{id:[0-9]+}", function (RouteCollectorProxy $autoupdatesystemId)
        {
          $autoupdatesystemId->map(['GET'], '', \App\v1\Controllers\Autoupdatesystem::class . ':showItem');
          $autoupdatesystemId->map(['POST'], '', \App\v1\Controllers\Autoupdatesystem::class . ':updateItem');
          $autoupdatesystemId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Autoupdatesystem::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Autoupdatesystem::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Autoupdatesystem::class . ':showSubHistory');
          });
        });
      });

      $view->group('/networkinterfaces', function (RouteCollectorProxy $networkinterfaces)
      {
        $networkinterfaces->map(['GET'], '', \App\v1\Controllers\Networkinterface::class . ':showAll');
        $networkinterfaces->group("/new", function (RouteCollectorProxy $ninterfaceNew)
        {
          $ninterfaceNew->map(['GET'], '', \App\v1\Controllers\Networkinterface::class . ':showNewItem');
          $ninterfaceNew->map(['POST'], '', \App\v1\Controllers\Networkinterface::class . ':newItem');
        });

        $networkinterfaces->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkinterfaceId)
        {
          $networkinterfaceId->map(['GET'], '', \App\v1\Controllers\Networkinterface::class . ':showItem');
          $networkinterfaceId->map(['POST'], '', \App\v1\Controllers\Networkinterface::class . ':updateItem');
          $networkinterfaceId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Networkinterface::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Networkinterface::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Networkinterface::class . ':showSubHistory');
          });
        });
      });

      $view->group('/netpoints', function (RouteCollectorProxy $netpoints)
      {
        $netpoints->map(['GET'], '', \App\v1\Controllers\Netpoint::class . ':showAll');
        $netpoints->group("/new", function (RouteCollectorProxy $netpointNew)
        {
          $netpointNew->map(['GET'], '', \App\v1\Controllers\Netpoint::class . ':showNewItem');
          $netpointNew->map(['POST'], '', \App\v1\Controllers\Netpoint::class . ':newItem');
        });

        $netpoints->group("/{id:[0-9]+}", function (RouteCollectorProxy $netpointId)
        {
          $netpointId->map(['GET'], '', \App\v1\Controllers\Netpoint::class . ':showItem');
          $netpointId->map(['POST'], '', \App\v1\Controllers\Netpoint::class . ':updateItem');
          $netpointId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Netpoint::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Netpoint::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Netpoint::class . ':showSubHistory');
          });
        });
      });

      $view->group('/networks', function (RouteCollectorProxy $networks)
      {
        $networks->map(['GET'], '', \App\v1\Controllers\Network::class . ':showAll');
        $networks->group("/new", function (RouteCollectorProxy $networkNew)
        {
          $networkNew->map(['GET'], '', \App\v1\Controllers\Network::class . ':showNewItem');
          $networkNew->map(['POST'], '', \App\v1\Controllers\Network::class . ':newItem');
        });

        $networks->group("/{id:[0-9]+}", function (RouteCollectorProxy $networkId)
        {
          $networkId->map(['GET'], '', \App\v1\Controllers\Network::class . ':showItem');
          $networkId->map(['POST'], '', \App\v1\Controllers\Network::class . ':updateItem');
          $networkId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Network::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Network::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Network::class . ':showSubHistory');
          });
        });
      });

      $view->group('/vlans', function (RouteCollectorProxy $vlans)
      {
        $vlans->map(['GET'], '', \App\v1\Controllers\Vlan::class . ':showAll');
        $vlans->group("/new", function (RouteCollectorProxy $vlanNew)
        {
          $vlanNew->map(['GET'], '', \App\v1\Controllers\Vlan::class . ':showNewItem');
          $vlanNew->map(['POST'], '', \App\v1\Controllers\Vlan::class . ':newItem');
        });

        $vlans->group("/{id:[0-9]+}", function (RouteCollectorProxy $vlanId)
        {
          $vlanId->map(['GET'], '', \App\v1\Controllers\Vlan::class . ':showItem');
          $vlanId->map(['POST'], '', \App\v1\Controllers\Vlan::class . ':updateItem');
          $vlanId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Vlan::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Vlan::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Vlan::class . ':showSubHistory');
          });
        });
      });

      $view->group('/lineoperators', function (RouteCollectorProxy $lineoperators)
      {
        $lineoperators->map(['GET'], '', \App\v1\Controllers\Lineoperator::class . ':showAll');
        $lineoperators->group("/new", function (RouteCollectorProxy $lineoperatorNew)
        {
          $lineoperatorNew->map(['GET'], '', \App\v1\Controllers\Lineoperator::class . ':showNewItem');
          $lineoperatorNew->map(['POST'], '', \App\v1\Controllers\Lineoperator::class . ':newItem');
        });

        $lineoperators->group("/{id:[0-9]+}", function (RouteCollectorProxy $lineoperatorId)
        {
          $lineoperatorId->map(['GET'], '', \App\v1\Controllers\Lineoperator::class . ':showItem');
          $lineoperatorId->map(['POST'], '', \App\v1\Controllers\Lineoperator::class . ':updateItem');
          $lineoperatorId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Lineoperator::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Lineoperator::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Lineoperator::class . ':showSubHistory');
          });
        });
      });

      $view->group('/domaintypes', function (RouteCollectorProxy $domaintypes)
      {
        $domaintypes->map(['GET'], '', \App\v1\Controllers\Domaintype::class . ':showAll');
        $domaintypes->group("/new", function (RouteCollectorProxy $domtypeNew)
        {
          $domtypeNew->map(['GET'], '', \App\v1\Controllers\Domaintype::class . ':showNewItem');
          $domtypeNew->map(['POST'], '', \App\v1\Controllers\Domaintype::class . ':newItem');
        });

        $domaintypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $domaintypeId)
        {
          $domaintypeId->map(['GET'], '', \App\v1\Controllers\Domaintype::class . ':showItem');
          $domaintypeId->map(['POST'], '', \App\v1\Controllers\Domaintype::class . ':updateItem');
          $domaintypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Domaintype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Domaintype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Domaintype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/domainrelations', function (RouteCollectorProxy $domainrelations)
      {
        $domainrelations->map(['GET'], '', \App\v1\Controllers\Domainrelation::class . ':showAll');
        $domainrelations->group("/new", function (RouteCollectorProxy $relationNew)
        {
          $relationNew->map(['GET'], '', \App\v1\Controllers\Domainrelation::class . ':showNewItem');
          $relationNew->map(['POST'], '', \App\v1\Controllers\Domainrelation::class . ':newItem');
        });

        $domainrelations->group("/{id:[0-9]+}", function (RouteCollectorProxy $domainrelationId)
        {
          $domainrelationId->map(['GET'], '', \App\v1\Controllers\Domainrelation::class . ':showItem');
          $domainrelationId->map(['POST'], '', \App\v1\Controllers\Domainrelation::class . ':updateItem');
          $domainrelationId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Domainrelation::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Domainrelation::class . ':restoreItem');
            $sub->map(['GET'], 'domains', \App\v1\Controllers\Domainrelation::class . ':showSubDomains');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Domainrelation::class . ':showSubHistory');
          });
        });
      });

      $view->group('/domainrecordtypes', function (RouteCollectorProxy $domainrecordtypes)
      {
        $domainrecordtypes->map(['GET'], '', \App\v1\Controllers\Domainrecordtype::class . ':showAll');
        $domainrecordtypes->group("/new", function (RouteCollectorProxy $drtypeNew)
        {
          $drtypeNew->map(['GET'], '', \App\v1\Controllers\Domainrecordtype::class . ':showNewItem');
          $drtypeNew->map(['POST'], '', \App\v1\Controllers\Domainrecordtype::class . ':newItem');
        });

        $domainrecordtypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $domainrecordtypeId)
        {
          $domainrecordtypeId->map(['GET'], '', \App\v1\Controllers\Domainrecordtype::class . ':showItem');
          $domainrecordtypeId->map(['POST'], '', \App\v1\Controllers\Domainrecordtype::class . ':updateItem');
          $domainrecordtypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Domainrecordtype::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Domainrecordtype::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Domainrecordtype::class . ':showSubHistory');
          });
        });
      });

      $view->group('/ipnetworks', function (RouteCollectorProxy $ipnetworks)
      {
        $ipnetworks->map(['GET'], '', \App\v1\Controllers\Ipnetwork::class . ':showAll');
        $ipnetworks->group("/new", function (RouteCollectorProxy $ipnetworkNew)
        {
          $ipnetworkNew->map(['GET'], '', \App\v1\Controllers\Ipnetwork::class . ':showNewItem');
          $ipnetworkNew->map(['POST'], '', \App\v1\Controllers\Ipnetwork::class . ':newItem');
        });

        $ipnetworks->group("/{id:[0-9]+}", function (RouteCollectorProxy $ipnetworkId)
        {
          $ipnetworkId->map(['GET'], '', \App\v1\Controllers\Ipnetwork::class . ':showItem');
          $ipnetworkId->map(['POST'], '', \App\v1\Controllers\Ipnetwork::class . ':updateItem');
          $ipnetworkId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Ipnetwork::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Ipnetwork::class . ':restoreItem');
            $sub->map(['GET'], 'vlans', \App\v1\Controllers\Ipnetwork::class . ':showSubVlans');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Ipnetwork::class . ':showSubHistory');
          });
        });
      });

      $view->group('/fqdns', function (RouteCollectorProxy $fqdns)
      {
        $fqdns->map(['GET'], '', \App\v1\Controllers\Fqdn::class . ':showAll');
        $fqdns->group("/new", function (RouteCollectorProxy $fqdnNew)
        {
          $fqdnNew->map(['GET'], '', \App\v1\Controllers\Fqdn::class . ':showNewItem');
          $fqdnNew->map(['POST'], '', \App\v1\Controllers\Fqdn::class . ':newItem');
        });

        $fqdns->group("/{id:[0-9]+}", function (RouteCollectorProxy $fqdnId)
        {
          $fqdnId->map(['GET'], '', \App\v1\Controllers\Fqdn::class . ':showItem');
          $fqdnId->map(['POST'], '', \App\v1\Controllers\Fqdn::class . ':updateItem');
          $fqdnId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Fqdn::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Fqdn::class . ':restoreItem');
            $sub->map(['GET'], 'networkalias', \App\v1\Controllers\Fqdn::class . ':showSubNetworkalias');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Fqdn::class . ':showSubHistory');
          });
        });
      });

      $view->group('/wifinetworks', function (RouteCollectorProxy $wifinetworks)
      {
        $wifinetworks->map(['GET'], '', \App\v1\Controllers\Wifinetwork::class . ':showAll');
        $wifinetworks->group("/new", function (RouteCollectorProxy $wifiNew)
        {
          $wifiNew->map(['GET'], '', \App\v1\Controllers\Wifinetwork::class . ':showNewItem');
          $wifiNew->map(['POST'], '', \App\v1\Controllers\Wifinetwork::class . ':newItem');
        });

        $wifinetworks->group("/{id:[0-9]+}", function (RouteCollectorProxy $wifinetworkId)
        {
          $wifinetworkId->map(['GET'], '', \App\v1\Controllers\Wifinetwork::class . ':showItem');
          $wifinetworkId->map(['POST'], '', \App\v1\Controllers\Wifinetwork::class . ':updateItem');
          $wifinetworkId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Wifinetwork::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Wifinetwork::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Wifinetwork::class . ':showSubHistory');
          });
        });
      });

      $view->group('/networknames', function (RouteCollectorProxy $networknames)
      {
        $networknames->map(['GET'], '', \App\v1\Controllers\Networkname::class . ':showAll');
        $networknames->group("/new", function (RouteCollectorProxy $netnameNew)
        {
          $netnameNew->map(['GET'], '', \App\v1\Controllers\Networkname::class . ':showNewItem');
          $netnameNew->map(['POST'], '', \App\v1\Controllers\Networkname::class . ':newItem');
        });

        $networknames->group("/{id:[0-9]+}", function (RouteCollectorProxy $networknameId)
        {
          $networknameId->map(['GET'], '', \App\v1\Controllers\Networkname::class . ':showItem');
          $networknameId->map(['POST'], '', \App\v1\Controllers\Networkname::class . ':updateItem');
          $networknameId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Networkname::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Networkname::class . ':restoreItem');
            $sub->map(['GET'], 'networkalias', \App\v1\Controllers\Networkname::class . ':showSubNetworkalias');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Networkname::class . ':showSubHistory');
          });
        });
      });

      $view->group('/softwarecategories', function (RouteCollectorProxy $softwarecategories)
      {
        $softwarecategories->map(['GET'], '', \App\v1\Controllers\Softwarecategory::class . ':showAll');
        $softwarecategories->group("/new", function (RouteCollectorProxy $scategoryNew)
        {
          $scategoryNew->map(['GET'], '', \App\v1\Controllers\Softwarecategory::class . ':showNewItem');
          $scategoryNew->map(['POST'], '', \App\v1\Controllers\Softwarecategory::class . ':newItem');
        });

        $softwarecategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $softwarecategoryId)
        {
          $softwarecategoryId->map(['GET'], '', \App\v1\Controllers\Softwarecategory::class . ':showItem');
          $softwarecategoryId->map(['POST'], '', \App\v1\Controllers\Softwarecategory::class . ':updateItem');
          $softwarecategoryId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Softwarecategory::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Softwarecategory::class . ':restoreItem');
            $sub->map(
              ['GET'],
              'softwarecategories',
              \App\v1\Controllers\Softwarecategory::class . ':showSubSoftwarecategories'
            );
            $sub->map(['GET'], 'history', \App\v1\Controllers\Softwarecategory::class . ':showSubHistory');
          });
        });
      });

      $view->group('/usertitles', function (RouteCollectorProxy $usertitles)
      {
        $usertitles->map(['GET'], '', \App\v1\Controllers\Usertitle::class . ':showAll');
        $usertitles->map(['POST'], '', \App\v1\Controllers\Usertitle::class . ':postItem');
        $usertitles->group("/{id:[0-9]+}", function (RouteCollectorProxy $usertitleId)
        {
          $usertitleId->map(['GET'], '', \App\v1\Controllers\Usertitle::class . ':showItem');
          $usertitleId->map(['POST'], '', \App\v1\Controllers\Usertitle::class . ':updateItem');
          $usertitleId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Usertitle::class . ':showSubHistory');
          });
        });
      });

      $view->group('/usercategories', function (RouteCollectorProxy $usercategories)
      {
        $usercategories->map(['GET'], '', \App\v1\Controllers\Usercategory::class . ':showAll');
        $usercategories->group("/new", function (RouteCollectorProxy $usercatNew)
        {
          $usercatNew->map(['GET'], '', \App\v1\Controllers\Usercategory::class . ':showNewItem');
          $usercatNew->map(['POST'], '', \App\v1\Controllers\Usercategory::class . ':newItem');
        });

        $usercategories->group("/{id:[0-9]+}", function (RouteCollectorProxy $usercategoryId)
        {
          $usercategoryId->map(['GET'], '', \App\v1\Controllers\Usercategory::class . ':showItem');
          $usercategoryId->map(['POST'], '', \App\v1\Controllers\Usercategory::class . ':updateItem');
          $usercategoryId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Usercategory::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Usercategory::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Usercategory::class . ':showSubHistory');
          });
        });
      });

      $view->group('/rulerightparameters', function (RouteCollectorProxy $rulerightparameters)
      {
        $rulerightparameters->map(['GET'], '', \App\v1\Controllers\Rulerightparameter::class . ':showAll');
        $rulerightparameters->group("/new", function (RouteCollectorProxy $rrparameterNew)
        {
          $rrparameterNew->map(['GET'], '', \App\v1\Controllers\Rulerightparameter::class . ':showNewItem');
          $rrparameterNew->map(['POST'], '', \App\v1\Controllers\Rulerightparameter::class . ':newItem');
        });

        $rulerightparameters->group("/{id:[0-9]+}", function (RouteCollectorProxy $rulerightparameterId)
        {
          $rulerightparameterId->map(['GET'], '', \App\v1\Controllers\Rulerightparameter::class . ':showItem');
          $rulerightparameterId->map(['POST'], '', \App\v1\Controllers\Rulerightparameter::class . ':updateItem');
          $rulerightparameterId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Rulerightparameter::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Rulerightparameter::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Rulerightparameter::class . ':showSubHistory');
          });
        });
      });

      $view->group('/fieldblacklists', function (RouteCollectorProxy $fieldblacklists)
      {
        $fieldblacklists->map(['GET'], '', \App\v1\Controllers\Fieldblacklist::class . ':showAll');
        $fieldblacklists->group("/new", function (RouteCollectorProxy $fieldblacklistNew)
        {
          $fieldblacklistNew->map(['GET'], '', \App\v1\Controllers\Fieldblacklist::class . ':showNewItem');
          $fieldblacklistNew->map(['POST'], '', \App\v1\Controllers\Fieldblacklist::class . ':newItem');
        });

        $fieldblacklists->group("/{id:[0-9]+}", function (RouteCollectorProxy $fieldblacklistId)
        {
          $fieldblacklistId->map(['GET'], '', \App\v1\Controllers\Fieldblacklist::class . ':showItem');
          $fieldblacklistId->map(['POST'], '', \App\v1\Controllers\Fieldblacklist::class . ':updateItem');
          $fieldblacklistId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Fieldblacklist::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Fieldblacklist::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Fieldblacklist::class . ':showSubHistory');
          });
        });
      });

      $view->group('/ssovariables', function (RouteCollectorProxy $ssovariables)
      {
        $ssovariables->map(['GET'], '', \App\v1\Controllers\Ssovariable::class . ':showAll');
        $ssovariables->group("/new", function (RouteCollectorProxy $ssovarNew)
        {
          $ssovarNew->map(['GET'], '', \App\v1\Controllers\Ssovariable::class . ':showNewItem');
          $ssovarNew->map(['POST'], '', \App\v1\Controllers\Ssovariable::class . ':newItem');
        });

        $ssovariables->group("/{id:[0-9]+}", function (RouteCollectorProxy $ssovariableId)
        {
          $ssovariableId->map(['GET'], '', \App\v1\Controllers\Ssovariable::class . ':showItem');
          $ssovariableId->map(['POST'], '', \App\v1\Controllers\Ssovariable::class . ':updateItem');
          $ssovariableId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Ssovariable::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Ssovariable::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Ssovariable::class . ':showSubHistory');
          });
        });
      });

      $view->group('/plugs', function (RouteCollectorProxy $plugs)
      {
        $plugs->map(['GET'], '', \App\v1\Controllers\Plug::class . ':showAll');
        $plugs->group("/new", function (RouteCollectorProxy $plugNew)
        {
          $plugNew->map(['GET'], '', \App\v1\Controllers\Plug::class . ':showNewItem');
          $plugNew->map(['POST'], '', \App\v1\Controllers\Plug::class . ':newItem');
        });

        $plugs->group("/{id:[0-9]+}", function (RouteCollectorProxy $plugId)
        {
          $plugId->map(['GET'], '', \App\v1\Controllers\Plug::class . ':showItem');
          $plugId->map(['POST'], '', \App\v1\Controllers\Plug::class . ':updateItem');
          $plugId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Plug::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Plug::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Plug::class . ':showSubHistory');
          });
        });
      });

      $view->group('/appliancetypes', function (RouteCollectorProxy $viewliancetypes)
      {
        $viewliancetypes->map(['GET'], '', \App\v1\Controllers\Appliancetype::class . ':showAll');
        $viewliancetypes->map(['POST'], '', \App\v1\Controllers\Appliancetype::class . ':postItem');
        $viewliancetypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $viewliancetypeId)
        {
          $viewliancetypeId->map(['GET'], '', \App\v1\Controllers\Appliancetype::class . ':showItem');
          $viewliancetypeId->map(['POST'], '', \App\v1\Controllers\Appliancetype::class . ':updateItem');
          $viewliancetypeId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Appliancetype::class . ':showSubHistory');
          });
        });
      });
      $view->group('/applianceenvironments', function (RouteCollectorProxy $applianceenvs)
      {
        $applianceenvs->map(['GET'], '', \App\v1\Controllers\Applianceenvironment::class . ':showAll');
        $applianceenvs->group("/new", function (RouteCollectorProxy $applianceenvNew)
        {
          $applianceenvNew->map(['GET'], '', \App\v1\Controllers\Applianceenvironment::class . ':showNewItem');
          $applianceenvNew->map(['POST'], '', \App\v1\Controllers\Applianceenvironment::class . ':newItem');
        });

        $applianceenvs->group("/{id:[0-9]+}", function (RouteCollectorProxy $applianceenvId)
        {
          $applianceenvId->map(['GET'], '', \App\v1\Controllers\Applianceenvironment::class . ':showItem');
          $applianceenvId->map(
            ['POST'],
            '',
            \App\v1\Controllers\Applianceenvironment::class . ':updateItem'
          );
          $applianceenvId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Applianceenvironment::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Applianceenvironment::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Applianceenvironment::class . ':showSubHistory');
          });
        });
      });

      $view->group('/devices', function (RouteCollectorProxy $devices)
      {
        $devices->group('/devicepowersupplies', function (RouteCollectorProxy $devicepowersupplies)
        {
          $devicepowersupplies->map(['GET'], '', \App\v1\Controllers\Devicepowersupply::class . ':showAll');
          $devicepowersupplies->group("/new", function (RouteCollectorProxy $powersupplyNew)
          {
            $powersupplyNew->map(['GET'], '', \App\v1\Controllers\Devicepowersupply::class . ':showNewItem');
            $powersupplyNew->map(['POST'], '', \App\v1\Controllers\Devicepowersupply::class . ':newItem');
          });

          $devicepowersupplies->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicepowersupplyId)
          {
            $devicepowersupplyId->map(['GET'], '', \App\v1\Controllers\Devicepowersupply::class . ':showItem');
            $devicepowersupplyId->map(['POST'], '', \App\v1\Controllers\Devicepowersupply::class . ':updateItem');
            $devicepowersupplyId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicepowersupply::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicepowersupply::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicepowersupply::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicepowersupply::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicepowersupply::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicebatteries', function (RouteCollectorProxy $devicebatteries)
        {
          $devicebatteries->map(['GET'], '', \App\v1\Controllers\Devicebattery::class . ':showAll');
          $devicebatteries->group("/new", function (RouteCollectorProxy $batteryNew)
          {
            $batteryNew->map(['GET'], '', \App\v1\Controllers\Devicebattery::class . ':showNewItem');
            $batteryNew->map(['POST'], '', \App\v1\Controllers\Devicebattery::class . ':newItem');
          });

          $devicebatteries->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicebatteryId)
          {
            $devicebatteryId->map(['GET'], '', \App\v1\Controllers\Devicebattery::class . ':showItem');
            $devicebatteryId->map(['POST'], '', \App\v1\Controllers\Devicebattery::class . ':updateItem');
            $devicebatteryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicebattery::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicebattery::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicebattery::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicebattery::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicebattery::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicebatterytypes', function (RouteCollectorProxy $devicebatterytypes)
        {
          $devicebatterytypes->map(['GET'], '', \App\v1\Controllers\Devicebatterytype::class . ':showAll');
          $devicebatterytypes->group("/new", function (RouteCollectorProxy $dbtypeNew)
          {
            $dbtypeNew->map(['GET'], '', \App\v1\Controllers\Devicebatterytype::class . ':showNewItem');
            $dbtypeNew->map(['POST'], '', \App\v1\Controllers\Devicebatterytype::class . ':newItem');
          });

          $devicebatterytypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicebatterytypeId)
          {
            $devicebatterytypeId->map(['GET'], '', \App\v1\Controllers\Devicebatterytype::class . ':showItem');
            $devicebatterytypeId->map(['POST'], '', \App\v1\Controllers\Devicebatterytype::class . ':updateItem');
            $devicebatterytypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicebatterytype::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicebatterytype::class . ':restoreItem');
            });
          });
        });

        $devices->group('/devicecases', function (RouteCollectorProxy $devicecases)
        {
          $devicecases->map(['GET'], '', \App\v1\Controllers\Devicecase::class . ':showAll');
          $devicecases->group("/new", function (RouteCollectorProxy $devicecaseNew)
          {
            $devicecaseNew->map(['GET'], '', \App\v1\Controllers\Devicecase::class . ':showNewItem');
            $devicecaseNew->map(['POST'], '', \App\v1\Controllers\Devicecase::class . ':newItem');
          });

          $devicecases->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecaseId)
          {
            $devicecaseId->map(['GET'], '', \App\v1\Controllers\Devicecase::class . ':showItem');
            $devicecaseId->map(['POST'], '', \App\v1\Controllers\Devicecase::class . ':updateItem');
            $devicecaseId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicecase::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicecase::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicecase::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicecase::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecase::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicesensors', function (RouteCollectorProxy $devicesensors)
        {
          $devicesensors->map(['GET'], '', \App\v1\Controllers\Devicesensor::class . ':showAll');
          $devicesensors->group("/new", function (RouteCollectorProxy $devicesensorNew)
          {
            $devicesensorNew->map(['GET'], '', \App\v1\Controllers\Devicesensor::class . ':showNewItem');
            $devicesensorNew->map(['POST'], '', \App\v1\Controllers\Devicesensor::class . ':newItem');
          });

          $devicesensors->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesensorId)
          {
            $devicesensorId->map(['GET'], '', \App\v1\Controllers\Devicesensor::class . ':showItem');
            $devicesensorId->map(['POST'], '', \App\v1\Controllers\Devicesensor::class . ':updateItem');
            $devicesensorId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicesensor::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicesensor::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicesensor::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicesensor::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesensor::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicesimcards', function (RouteCollectorProxy $devicesimcards)
        {
          $devicesimcards->map(['GET'], '', \App\v1\Controllers\Devicesimcard::class . ':showAll');
          $devicesimcards->group("/new", function (RouteCollectorProxy $simcardNew)
          {
            $simcardNew->map(['GET'], '', \App\v1\Controllers\Devicesimcard::class . ':showNewItem');
            $simcardNew->map(['POST'], '', \App\v1\Controllers\Devicesimcard::class . ':newItem');
          });

          $devicesimcards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesimcardId)
          {
            $devicesimcardId->map(['GET'], '', \App\v1\Controllers\Devicesimcard::class . ':showItem');
            $devicesimcardId->map(['POST'], '', \App\v1\Controllers\Devicesimcard::class . ':updateItem');
            $devicesimcardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicesimcard::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicesimcard::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicesimcard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicesimcard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesimcard::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicegraphiccards', function (RouteCollectorProxy $devicegraphiccards)
        {
          $devicegraphiccards->map(['GET'], '', \App\v1\Controllers\Devicegraphiccard::class . ':showAll');
          $devicegraphiccards->group("/new", function (RouteCollectorProxy $graphiccardNew)
          {
            $graphiccardNew->map(['GET'], '', \App\v1\Controllers\Devicegraphiccard::class . ':showNewItem');
            $graphiccardNew->map(['POST'], '', \App\v1\Controllers\Devicegraphiccard::class . ':newItem');
          });

          $devicegraphiccards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicegraphiccardId)
          {
            $devicegraphiccardId->map(['GET'], '', \App\v1\Controllers\Devicegraphiccard::class . ':showItem');
            $devicegraphiccardId->map(['POST'], '', \App\v1\Controllers\Devicegraphiccard::class . ':updateItem');
            $devicegraphiccardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicegraphiccard::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicegraphiccard::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicegraphiccard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicegraphiccard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegraphiccard::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicemotherboards', function (RouteCollectorProxy $devicemotherboards)
        {
          $devicemotherboards->map(['GET'], '', \App\v1\Controllers\Devicemotherboard::class . ':showAll');
          $devicemotherboards->group("/new", function (RouteCollectorProxy $motherboardNew)
          {
            $motherboardNew->map(['GET'], '', \App\v1\Controllers\Devicemotherboard::class . ':showNewItem');
            $motherboardNew->map(['POST'], '', \App\v1\Controllers\Devicemotherboard::class . ':newItem');
          });

          $devicemotherboards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicemotherboardId)
          {
            $devicemotherboardId->map(['GET'], '', \App\v1\Controllers\Devicemotherboard::class . ':showItem');
            $devicemotherboardId->map(['POST'], '', \App\v1\Controllers\Devicemotherboard::class . ':updateItem');
            $devicemotherboardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicemotherboard::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicemotherboard::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicemotherboard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicemotherboard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicemotherboard::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicenetworkcards', function (RouteCollectorProxy $devicenetworkcards)
        {
          $devicenetworkcards->map(['GET'], '', \App\v1\Controllers\Devicenetworkcard::class . ':showAll');
          $devicenetworkcards->group("/new", function (RouteCollectorProxy $networkcardNew)
          {
            $networkcardNew->map(['GET'], '', \App\v1\Controllers\Devicenetworkcard::class . ':showNewItem');
            $networkcardNew->map(['POST'], '', \App\v1\Controllers\Devicenetworkcard::class . ':newItem');
          });

          $devicenetworkcards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicenetworkcardId)
          {
            $devicenetworkcardId->map(['GET'], '', \App\v1\Controllers\Devicenetworkcard::class . ':showItem');
            $devicenetworkcardId->map(['POST'], '', \App\v1\Controllers\Devicenetworkcard::class . ':updateItem');
            $devicenetworkcardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicenetworkcard::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicenetworkcard::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicenetworkcard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicenetworkcard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicenetworkcard::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicesoundcards', function (RouteCollectorProxy $devicesoundcards)
        {
          $devicesoundcards->map(['GET'], '', \App\v1\Controllers\Devicesoundcard::class . ':showAll');
          $devicesoundcards->group("/new", function (RouteCollectorProxy $soundcardNew)
          {
            $soundcardNew->map(['GET'], '', \App\v1\Controllers\Devicesoundcard::class . ':showNewItem');
            $soundcardNew->map(['POST'], '', \App\v1\Controllers\Devicesoundcard::class . ':newItem');
          });

          $devicesoundcards->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicesoundcardId)
          {
            $devicesoundcardId->map(['GET'], '', \App\v1\Controllers\Devicesoundcard::class . ':showItem');
            $devicesoundcardId->map(['POST'], '', \App\v1\Controllers\Devicesoundcard::class . ':updateItem');
            $devicesoundcardId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicesoundcard::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicesoundcard::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicesoundcard::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicesoundcard::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicesoundcard::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicegenerics', function (RouteCollectorProxy $devicegenerics)
        {
          $devicegenerics->map(['GET'], '', \App\v1\Controllers\Devicegeneric::class . ':showAll');
          $devicegenerics->group("/new", function (RouteCollectorProxy $genericNew)
          {
            $genericNew->map(['GET'], '', \App\v1\Controllers\Devicegeneric::class . ':showNewItem');
            $genericNew->map(['POST'], '', \App\v1\Controllers\Devicegeneric::class . ':newItem');
          });

          $devicegenerics->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicegenericId)
          {
            $devicegenericId->map(['GET'], '', \App\v1\Controllers\Devicegeneric::class . ':showItem');
            $devicegenericId->map(['POST'], '', \App\v1\Controllers\Devicegeneric::class . ':updateItem');
            $devicegenericId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicegeneric::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicegeneric::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicegeneric::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicegeneric::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicegeneric::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicecontrols', function (RouteCollectorProxy $devicecontrols)
        {
          $devicecontrols->map(['GET'], '', \App\v1\Controllers\Devicecontrol::class . ':showAll');
          $devicecontrols->group("/new", function (RouteCollectorProxy $controlNew)
          {
            $controlNew->map(['GET'], '', \App\v1\Controllers\Devicecontrol::class . ':showNewItem');
            $controlNew->map(['POST'], '', \App\v1\Controllers\Devicecontrol::class . ':newItem');
          });

          $devicecontrols->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicecontrolId)
          {
            $devicecontrolId->map(['GET'], '', \App\v1\Controllers\Devicecontrol::class . ':showItem');
            $devicecontrolId->map(['POST'], '', \App\v1\Controllers\Devicecontrol::class . ':updateItem');
            $devicecontrolId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicecontrol::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicecontrol::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicecontrol::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicecontrol::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicecontrol::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/deviceharddrives', function (RouteCollectorProxy $deviceharddrives)
        {
          $deviceharddrives->map(['GET'], '', \App\v1\Controllers\Deviceharddrive::class . ':showAll');
          $deviceharddrives->group("/new", function (RouteCollectorProxy $harddriveNew)
          {
            $harddriveNew->map(['GET'], '', \App\v1\Controllers\Deviceharddrive::class . ':showNewItem');
            $harddriveNew->map(['POST'], '', \App\v1\Controllers\Deviceharddrive::class . ':newItem');
          });

          $deviceharddrives->group("/{id:[0-9]+}", function (RouteCollectorProxy $deviceharddriveId)
          {
            $deviceharddriveId->map(['GET'], '', \App\v1\Controllers\Deviceharddrive::class . ':showItem');
            $deviceharddriveId->map(['POST'], '', \App\v1\Controllers\Deviceharddrive::class . ':updateItem');
            $deviceharddriveId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Deviceharddrive::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Deviceharddrive::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Deviceharddrive::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Deviceharddrive::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Deviceharddrive::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicefirmwares', function (RouteCollectorProxy $devicefirmwares)
        {
          $devicefirmwares->map(['GET'], '', \App\v1\Controllers\Devicefirmware::class . ':showAll');
          $devicefirmwares->group("/new", function (RouteCollectorProxy $dfNew)
          {
            $dfNew->map(['GET'], '', \App\v1\Controllers\Devicefirmware::class . ':showNewItem');
            $dfNew->map(['POST'], '', \App\v1\Controllers\Devicefirmware::class . ':newItem');
          });

          $devicefirmwares->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicefirmwareId)
          {
            $devicefirmwareId->map(['GET'], '', \App\v1\Controllers\Devicefirmware::class . ':showItem');
            $devicefirmwareId->map(['POST'], '', \App\v1\Controllers\Devicefirmware::class . ':updateItem');
            $devicefirmwareId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicefirmware::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicefirmware::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicefirmware::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicefirmware::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicefirmware::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicefirmwaretypes', function (RouteCollectorProxy $devicefirmwaretypes)
        {
          $devicefirmwaretypes->map(['GET'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':showAll');
          $devicefirmwaretypes->group("/new", function (RouteCollectorProxy $dftypeNew)
          {
            $dftypeNew->map(['GET'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':showNewItem');
            $dftypeNew->map(['POST'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':newItem');
          });

          $devicefirmwaretypes->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicefirmwaretypeId)
          {
            $devicefirmwaretypeId->map(['GET'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':showItem');
            $devicefirmwaretypeId->map(['POST'], '', \App\v1\Controllers\Devicefirmwaretype::class . ':updateItem');
            $devicefirmwaretypeId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicefirmwaretype::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicefirmwaretype::class . ':restoreItem');
            });
          });
        });

        $devices->group('/devicedrives', function (RouteCollectorProxy $devicedrives)
        {
          $devicedrives->map(['GET'], '', \App\v1\Controllers\Devicedrive::class . ':showAll');
          $devicedrives->group("/new", function (RouteCollectorProxy $driveNew)
          {
            $driveNew->map(['GET'], '', \App\v1\Controllers\Devicedrive::class . ':showNewItem');
            $driveNew->map(['POST'], '', \App\v1\Controllers\Devicedrive::class . ':newItem');
          });

          $devicedrives->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicedriveId)
          {
            $devicedriveId->map(['GET'], '', \App\v1\Controllers\Devicedrive::class . ':showItem');
            $devicedriveId->map(['POST'], '', \App\v1\Controllers\Devicedrive::class . ':updateItem');
            $devicedriveId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicedrive::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicedrive::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicedrive::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicedrive::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicedrive::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicememories', function (RouteCollectorProxy $devicememories)
        {
          $devicememories->map(['GET'], '', \App\v1\Controllers\Devicememory::class . ':showAll');
          $devicememories->group("/new", function (RouteCollectorProxy $memoryNew)
          {
            $memoryNew->map(['GET'], '', \App\v1\Controllers\Devicememory::class . ':showNewItem');
            $memoryNew->map(['POST'], '', \App\v1\Controllers\Devicememory::class . ':newItem');
          });

          $devicememories->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicememoryId)
          {
            $devicememoryId->map(['GET'], '', \App\v1\Controllers\Devicememory::class . ':showItem');
            $devicememoryId->map(['POST'], '', \App\v1\Controllers\Devicememory::class . ':updateItem');
            $devicememoryId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicememory::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicememory::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicememory::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicememory::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicememory::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/deviceprocessors', function (RouteCollectorProxy $deviceprocessors)
        {
          $deviceprocessors->map(['GET'], '', \App\v1\Controllers\Deviceprocessor::class . ':showAll');
          $deviceprocessors->group("/new", function (RouteCollectorProxy $processorNew)
          {
            $processorNew->map(['GET'], '', \App\v1\Controllers\Deviceprocessor::class . ':showNewItem');
            $processorNew->map(['POST'], '', \App\v1\Controllers\Deviceprocessor::class . ':newItem');
          });

          $deviceprocessors->group("/{id:[0-9]+}", function (RouteCollectorProxy $deviceprocessorId)
          {
            $deviceprocessorId->map(['GET'], '', \App\v1\Controllers\Deviceprocessor::class . ':showItem');
            $deviceprocessorId->map(['POST'], '', \App\v1\Controllers\Deviceprocessor::class . ':updateItem');
            $deviceprocessorId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Deviceprocessor::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Deviceprocessor::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Deviceprocessor::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Deviceprocessor::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Deviceprocessor::class . ':showSubHistory');
            });
          });
        });

        $devices->group('/devicepcis', function (RouteCollectorProxy $devicepcis)
        {
          $devicepcis->map(['GET'], '', \App\v1\Controllers\Devicepci::class . ':showAll');
          $devicepcis->group("/new", function (RouteCollectorProxy $pciNew)
          {
            $pciNew->map(['GET'], '', \App\v1\Controllers\Devicepci::class . ':showNewItem');
            $pciNew->map(['POST'], '', \App\v1\Controllers\Devicepci::class . ':newItem');
          });

          $devicepcis->group("/{id:[0-9]+}", function (RouteCollectorProxy $devicepciId)
          {
            $devicepciId->map(['GET'], '', \App\v1\Controllers\Devicepci::class . ':showItem');
            $devicepciId->map(['POST'], '', \App\v1\Controllers\Devicepci::class . ':updateItem');
            $devicepciId->group('/', function (RouteCollectorProxy $sub)
            {
              $sub->map(['GET'], 'delete', \App\v1\Controllers\Devicepci::class . ':deleteItem');
              $sub->map(['GET'], 'restore', \App\v1\Controllers\Devicepci::class . ':restoreItem');
              $sub->map(['GET'], 'documents', \App\v1\Controllers\Devicepci::class . ':showSubDocuments');
              $sub->map(['GET'], 'items', \App\v1\Controllers\Devicepci::class . ':showSubItems');
              $sub->map(['GET'], 'history', \App\v1\Controllers\Devicepci::class . ':showSubHistory');
            });
          });
        });
      });

      $view->group('/notificationtemplates', function (RouteCollectorProxy $notificationtemplates)
      {
        $notificationtemplates->map(['GET'], '', \App\v1\Controllers\Notificationtemplate::class . ':showAll');
        $notificationtemplates->group("/new", function (RouteCollectorProxy $ntemplateNew)
        {
          $ntemplateNew->map(['GET'], '', \App\v1\Controllers\Notificationtemplate::class . ':showNewItem');
          $ntemplateNew->map(['POST'], '', \App\v1\Controllers\Notificationtemplate::class . ':newItem');
        });

        $notificationtemplates->group("/{id:[0-9]+}", function (RouteCollectorProxy $notificationtemplateId)
        {
          $notificationtemplateId->map(['GET'], '', \App\v1\Controllers\Notificationtemplate::class . ':showItem');
          $notificationtemplateId->map(['POST'], '', \App\v1\Controllers\Notificationtemplate::class . ':updateItem');
          $notificationtemplateId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Notificationtemplate::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Notificationtemplate::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Notificationtemplate::class . ':showSubHistory');
            $sub->map(
              ['GET'],
              'templatetranslation',
              \App\v1\Controllers\Notificationtemplate::class . ':showSubTemplatetranslations'
            );
            $sub->map(
              ['GET'],
              'templatetranslation/{translationid:[0-9]+}',
              \App\v1\Controllers\Notificationtemplate::class . ':showSubTemplatetranslation'
            );
          });
        });
      });

      $view->group('/notifications', function (RouteCollectorProxy $notifications)
      {
        $notifications->map(['GET'], '', \App\v1\Controllers\Notification::class . ':showAll');
        $notifications->group("/new", function (RouteCollectorProxy $notificationNew)
        {
          $notificationNew->map(['GET'], '', \App\v1\Controllers\Notification::class . ':showNewItem');
          $notificationNew->map(['POST'], '', \App\v1\Controllers\Notification::class . ':newItem');
        });

        $notifications->group("/{id:[0-9]+}", function (RouteCollectorProxy $notificationId)
        {
          $notificationId->map(['GET'], '', \App\v1\Controllers\Notification::class . ':showItem');
          $notificationId->map(['POST'], '', \App\v1\Controllers\Notification::class . ':updateItem');
          $notificationId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Notification::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Notification::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Notification::class . ':showSubHistory');
          });
        });
      });

      $view->group('/slms', function (RouteCollectorProxy $slms)
      {
        $slms->map(['GET'], '', \App\v1\Controllers\Slm::class . ':showAll');
        $slms->group("/new", function (RouteCollectorProxy $slmNew)
        {
          $slmNew->map(['GET'], '', \App\v1\Controllers\Slm::class . ':showNewItem');
          $slmNew->map(['POST'], '', \App\v1\Controllers\Slm::class . ':newItem');
        });

        $slms->group("/{id:[0-9]+}", function (RouteCollectorProxy $slmId)
        {
          $slmId->map(['GET'], '', \App\v1\Controllers\Slm::class . ':showItem');
          $slmId->map(['POST'], '', \App\v1\Controllers\Slm::class . ':updateItem');
          $slmId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Slm::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Slm::class . ':restoreItem');
            $sub->map(['GET'], 'slas', \App\v1\Controllers\Slm::class . ':showSubSlas');
            $sub->map(['GET'], 'olas', \App\v1\Controllers\Slm::class . ':showSubOlas');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Slm::class . ':showSubHistory');
          });
        });
      });

      $view->group('/fieldunicities', function (RouteCollectorProxy $fieldunicities)
      {
        $fieldunicities->map(['GET'], '', \App\v1\Controllers\Fieldunicity::class . ':showAll');
        $fieldunicities->group("/new", function (RouteCollectorProxy $unicityNew)
        {
          $unicityNew->map(['GET'], '', \App\v1\Controllers\Fieldunicity::class . ':showNewItem');
          $unicityNew->map(['POST'], '', \App\v1\Controllers\Fieldunicity::class . ':newItem');
        });

        $fieldunicities->group("/{id:[0-9]+}", function (RouteCollectorProxy $fieldunicityId)
        {
          $fieldunicityId->map(['GET'], '', \App\v1\Controllers\Fieldunicity::class . ':showItem');
          $fieldunicityId->map(['POST'], '', \App\v1\Controllers\Fieldunicity::class . ':updateItem');
          $fieldunicityId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Fieldunicity::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Fieldunicity::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Fieldunicity::class . ':showSubHistory');
          });
        });
      });

      $view->group('/crontasks', function (RouteCollectorProxy $crontasks)
      {
        $crontasks->map(['GET'], '', \App\v1\Controllers\Crontask::class . ':showAll');
        $crontasks->map(['POST'], '', \App\v1\Controllers\Crontask::class . ':postItem');
        $crontasks->group("/{id:[0-9]+}", function (RouteCollectorProxy $crontaskId)
        {
          $crontaskId->map(['GET'], '', \App\v1\Controllers\Crontask::class . ':showItem');
          $crontaskId->map(['POST'], '', \App\v1\Controllers\Crontask::class . ':updateItem');

          $crontaskId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'executions', \App\v1\Controllers\Crontask::class . ':showSubExecutions');
            $sub->map(['GET'], 'executions/{executionid:[0-9]+}', \App\v1\Controllers\Crontask::class .
              ':showSubExecutionlogs');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Crontask::class . ':showSubHistory');
          });
        });
      });

      $view->group('/links', function (RouteCollectorProxy $links)
      {
        $links->map(['GET'], '', \App\v1\Controllers\Link::class . ':showAll');
        $links->group("/new", function (RouteCollectorProxy $linkNew)
        {
          $linkNew->map(['GET'], '', \App\v1\Controllers\Link::class . ':showNewItem');
          $linkNew->map(['POST'], '', \App\v1\Controllers\Link::class . ':newItem');
        });

        $links->group("/{id:[0-9]+}", function (RouteCollectorProxy $linkId)
        {
          $linkId->map(['GET'], '', \App\v1\Controllers\Link::class . ':showItem');
          $linkId->map(['POST'], '', \App\v1\Controllers\Link::class . ':updateItem');
          $linkId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Link::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Link::class . ':restoreItem');
            $sub->map(['GET'], 'associateditemtypes', \App\v1\Controllers\Link::class . ':showSubAssociatedItemType');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Link::class . ':showSubHistory');
          });
        });
      });

      $view->group('/mailcollectors', function (RouteCollectorProxy $mailcollectors)
      {
        $mailcollectors->map(['GET'], '', \App\v1\Controllers\Mailcollector::class . ':showAll');
        $mailcollectors->group("/new", function (RouteCollectorProxy $collectorNew)
        {
          $collectorNew->map(['GET'], '', \App\v1\Controllers\Mailcollector::class . ':showNewItem');
          $collectorNew->map(['POST'], '', \App\v1\Controllers\Mailcollector::class . ':newItem');
        });

        $mailcollectors->group("/{id:[0-9]+}", function (RouteCollectorProxy $mailcollectorId)
        {
          $mailcollectorId->map(['GET'], '', \App\v1\Controllers\Mailcollector::class . ':showItem');
          $mailcollectorId->map(['POST'], '', \App\v1\Controllers\Mailcollector::class . ':updateItem');
          $mailcollectorId->map(['GET'], '/oauth', \App\v1\Controllers\Mailcollector::class . ':doOauth');
          $mailcollectorId->map(['GET'], '/oauth/cb', \App\v1\Controllers\Mailcollector::class . ':callbackOauth');

          $mailcollectorId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Mailcollector::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Mailcollector::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Mailcollector::class . ':showSubHistory');
          });
        });
      });

      $view->group('/authssos', function (RouteCollectorProxy $authssos)
      {
        $authssos->map(['GET'], '', \App\v1\Controllers\Authsso::class . ':showAll');
        $authssos->group("/new", function (RouteCollectorProxy $ssonew)
        {
          $ssonew->map(['GET'], '', \App\v1\Controllers\Authsso::class . ':showNewItem');
          $ssonew->map(['POST'], '', \App\v1\Controllers\Authsso::class . ':newItem');
        });

        $authssos->group("/{id:[0-9]+}", function (RouteCollectorProxy $ssoId)
        {
          $ssoId->map(['GET'], '', \App\v1\Controllers\Authsso::class . ':showItem');
          $ssoId->map(['POST'], '', \App\v1\Controllers\Authsso::class . ':updateItem');

          $ssoId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Authsso::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Authsso::class . ':restoreItem');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Authsso::class . ':showSubHistory');
          });
        });
      });

      $view->group('/authldaps', function (RouteCollectorProxy $ldaps)
      {
        $ldaps->map(['GET'], '', \App\v1\Controllers\Authldap::class . ':showAll');
        $ldaps->group("/new", function (RouteCollectorProxy $ldapNew)
        {
          $ldapNew->map(['GET'], '', \App\v1\Controllers\Authldap::class . ':showNewItem');
          $ldapNew->map(['POST'], '', \App\v1\Controllers\Authldap::class . ':newItem');
        });

        $ldaps->group("/{id:[0-9]+}", function (RouteCollectorProxy $ldapId)
        {
          $ldapId->map(['GET'], '', \App\v1\Controllers\Authldap::class . ':showItem');
          $ldapId->map(['POST'], '', \App\v1\Controllers\Authldap::class . ':updateItem');
          $ldapId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Authldap::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Authldap::class . ':restoreItem');
          });
        });
      });

      $view->group('/forms', function (RouteCollectorProxy $forms)
      {
        $forms->map(['GET'], '', \App\v1\Controllers\Forms\Form::class . ':showAll');
        $forms->group("/new", function (RouteCollectorProxy $formNew)
        {
          $formNew->map(['GET'], '', \App\v1\Controllers\Forms\Form::class . ':showNewItem');
          $formNew->map(['POST'], '', \App\v1\Controllers\Forms\Form::class . ':newItem');
        });


        $forms->group("/{id:[0-9]+}", function (RouteCollectorProxy $formId)
        {
          $formId->map(['GET'], '', \App\v1\Controllers\Forms\Form::class . ':showItem');
          $formId->map(['POST'], '', \App\v1\Controllers\Forms\Form::class . ':updateItem');
          $formId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'sections', \App\v1\Controllers\Forms\Form::class . ':showSubSections');
            $sub->map(['GET'], 'questions', \App\v1\Controllers\Forms\Form::class . ':showSubQuestions');
            $sub->map(['GET'], 'answers', \App\v1\Controllers\Forms\Form::class . ':showSubAnswers');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Forms\Form::class . ':showSubHistory');
          });
        });
      });
      $view->group('/sections', function (RouteCollectorProxy $sections)
      {
        $sections->map(['GET'], '', \App\v1\Controllers\Forms\Section::class . ':showAll');
        $sections->group("/new", function (RouteCollectorProxy $sectionNew)
        {
          $sectionNew->map(['GET'], '', \App\v1\Controllers\Forms\Section::class . ':showNewItem');
          $sectionNew->map(['POST'], '', \App\v1\Controllers\Forms\Section::class . ':newItem');
        });

        $sections->group("/{id:[0-9]+}", function (RouteCollectorProxy $sectionId)
        {
          $sectionId->map(['GET'], '', \App\v1\Controllers\Forms\Section::class . ':showItem');
          $sectionId->map(['POST'], '', \App\v1\Controllers\Forms\Section::class . ':updateItem');
          $sectionId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Forms\Section::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Forms\Section::class . ':restoreItem');
            $sub->map(['GET'], 'forms', \App\v1\Controllers\Forms\Section::class . ':showSubForms');
            $sub->map(['GET'], 'questions', \App\v1\Controllers\Forms\Section::class . ':showSubQuestions');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Forms\Section::class . ':showSubHistory');
          });
        });
      });
      $view->group('/questions', function (RouteCollectorProxy $questions)
      {
        $questions->map(['GET'], '', \App\v1\Controllers\Forms\Question::class . ':showAll');
        $questions->group("/new", function (RouteCollectorProxy $questionNew)
        {
          $questionNew->map(['GET'], '', \App\v1\Controllers\Forms\Question::class . ':showNewItem');
          $questionNew->map(['POST'], '', \App\v1\Controllers\Forms\Question::class . ':newItem');
        });

        $questions->group("/{id:[0-9]+}", function (RouteCollectorProxy $questionId)
        {
          $questionId->map(['GET'], '', \App\v1\Controllers\Forms\Question::class . ':showItem');
          $questionId->map(['POST'], '', \App\v1\Controllers\Forms\Question::class . ':updateItem');
          $questionId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'delete', \App\v1\Controllers\Forms\Question::class . ':deleteItem');
            $sub->map(['GET'], 'restore', \App\v1\Controllers\Forms\Question::class . ':restoreItem');
            $sub->map(['GET'], 'sections', \App\v1\Controllers\Forms\Question::class . ':showSubSections');
            $sub->map(['GET'], 'forms', \App\v1\Controllers\Forms\Question::class . ':showSubForms');
            $sub->map(['GET'], 'history', \App\v1\Controllers\Forms\Question::class . ':showSubHistory');
          });
        });
      });
      $view->group('/answers', function (RouteCollectorProxy $answers)
      {
        // $answers->map(['GET'], '', \App\v1\Controllers\Forms\Answer::class . ':showAll');
        // $answers->map(['POST'], '', \App\v1\Controllers\Forms\Answer::class . ':postItem');

        // $answers->group("/new", function (RouteCollectorProxy $answerNew)
        // {
        //   $answerNew->map(['GET'], '', \App\v1\Controllers\Forms\Answer::class . ':showNewItem');
        //   $answerNew->map(['POST'], '', \App\v1\Controllers\Forms\Answer::class . ':newItem');
        // });

        $answers->group("/{id:[0-9]+}", function (RouteCollectorProxy $answerId)
        {
          $answerId->map(['GET'], '', \App\v1\Controllers\Forms\Answer::class . ':showAnswer');
          // $answerId->map(['POST'], '', \App\v1\Controllers\Forms\Answer::class . ':updateItem');
          $answerId->group('/', function (RouteCollectorProxy $sub)
          {
            $sub->map(['GET'], 'history', \App\v1\Controllers\Forms\Answer::class . ':showSubHistory');
          });
        });
      });

      $view->group('/menubookmarks', function (RouteCollectorProxy $menu)
      {
        $menu->map(['GET'], '{endpoint:[a-zA-Z/]+}', \App\v1\Controllers\Menubookmark::class . ':newItem');
        $menu->map(['GET'], '/delete/{id:[0-9]+}', \App\v1\Controllers\Menubookmark::class . ':deleteItem');
      });

      $view->group('/infocoms', function (RouteCollectorProxy $infocoms)
      {
        $infocoms->map(['POST'], '', \App\v1\Controllers\Infocom::class . ':saveItem');
      });
    });
  }
}
