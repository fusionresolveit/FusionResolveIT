<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;

final class Menu
{
  protected $rights = [];

  public function getMenu(Request $request)
  {
    $this->loadRights();
    return $this->cleanMenuByDisplay($this->menuData($request));
  }

  public function getMenubookmark($menu)
  {
    $bookmarkItems = \App\Models\Menubookmark::where('user_id', $GLOBALS['user_id'])->get();
    $endpoints = [];
    foreach ($bookmarkItems as $item)
    {
      $endpoints[$item->endpoint] = $item->id;
    }

    $bookmarks = [];
    foreach ($menu as $item)
    {
      foreach ($item['sub'] as $sub)
      {
        if (isset($endpoints[$sub['endpoint']]))
        {
          $sub['id'] = $endpoints[$sub['endpoint']];
          $bookmarks[] = $sub;
        }
      }
    }
    return $bookmarks;
  }

  private function menuData($request)
  {
    global $basePath, $translator;

    $uri = $request->getUri();

    return [
      [
        'name' => $translator->translate('ITAM - Hardware inventory'),
        'id'   => 'hardwareinventory',
        'icon' => 'laptop house',
        'sub'  => [
          [
            'name' => $translator->translatePlural('Data center', 'Data centers', 2),
            'endpoint' => '/view/datacenters',
            'icon' => 'warehouse',
            'display' => $this->getRightForModel('\App\Models\Datacenter'),
          ],
          [
            'name'  => $translator->translatePlural('Computer', 'Computers', 2),
            'endpoint' => '/view/computers',
            'icon'  => 'laptop',
            'display' => $this->getRightForModel('\App\Models\Computer'),
            'comment' => 'Computers, servers, laptop...',
          ],
          [
            'name'  => $translator->translatePlural('Monitor', 'Monitors', 2),
            'endpoint' => '/view/monitors',
            'icon'  => 'desktop',
            'display' => $this->getRightForModel('\App\Models\Monitor'),
          ],
          [
            'name' => $translator->translatePlural('Network device', 'Network devices', 2),
            'endpoint' => '/view/networkequipments',
            'icon' => 'network wired',
            'display' => $this->getRightForModel('\App\Models\Networkequipment'),
          ],
          [
            'name' => $translator->translatePlural('Device', 'Devices', 2),
            'endpoint' => '/view/peripherals',
            'icon' => 'usb',
            'display' => $this->getRightForModel('\App\Models\Peripheral'),
          ],
          [
            'name' => $translator->translatePlural('Printer', 'Printers', 2),
            'endpoint' => '/view/printers',
            'icon' => 'print',
            'display' => $this->getRightForModel('\App\Models\Printer'),
          ],
          [
            'name' => $translator->translatePlural('Phone', 'Phones', 2),
            'endpoint' => '/view/phones',
            'icon' => 'phone',
            'display' => $this->getRightForModel('\App\Models\Phone'),
          ],
          [
            'name' => $translator->translatePlural('Cartridge', 'Cartridges', 2),
            'endpoint' => '/view/cartridgeitems',
            'icon' => 'fill drip',
            'display' => $this->getRightForModel('\App\Models\Cartridgeitem'),
          ],
          [
            'name' => $translator->translatePlural('Consumable', 'Consumables', 2),
            'endpoint' => '/view/consumableitems',
            'icon' => 'box open',
            'display' => $this->getRightForModel('\App\Models\Consumableitem'),
          ],
          [
            'name' => $translator->translatePlural('Rack', 'Racks', 2),
            'endpoint' => '/view/racks',
            'icon' => 'server',
            'display' => $this->getRightForModel('\App\Models\Rack'),
          ],
          [
            'name' => $translator->translatePlural('Enclosure', 'Enclosures', 2),
            'endpoint' => '/view/enclosures',
            'icon' => 'th',
            'display' => $this->getRightForModel('\App\Models\Enclosure'),
          ],
          [
            'name' => $translator->translatePlural('PDU', 'PDUs', 2),
            'endpoint' => '/view/pdus',
            'icon' => 'plug',
            'display' => $this->getRightForModel('\App\Models\Pdu'),
          ],
          [
            'name' => $translator->translatePlural('Passive device', 'Passive devices', 2),
            'endpoint' => '/view/passivedcequipments',
            'icon' => 'th list',
            'display' => $this->getRightForModel('\App\Models\Passivedcequipment'),
          ],
          [
            'name' => $translator->translatePlural('Simcard', 'Simcards', 2),
            'endpoint' => '/view/itemdevicesimcards',
            'icon' => 'sim card',
            'display' => $this->getRightForModel('\App\Models\ItemDevicesimcard'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('ITAM - Software inventory'),
        'id'   => 'softwareinventiry',
        'icon' => 'laptop house',
        'sub'  => [
          [
            'name' => $translator->translatePlural('Software', 'Software', 2),
            'endpoint' => '/view/softwares',
            'svgicon' => 'software',
            'display' => $this->getRightForModel('\App\Models\Software'),
          ],
          [
            'name' => $translator->translatePlural('Operating system', 'Operating systems', 2),
            'endpoint' => '/view/dropdowns/operatingsystems',
            'svgicon' => 'operatingsystem',
            'display' => $this->getRightForModel('\App\Models\Operatingsystem'),
          ],
          [
            'name' => $translator->translatePlural('Appliance', 'Appliances', 2),
            'endpoint' => '/view/appliances',
            'icon' => 'cubes',
            'display' => $this->getRightForModel('\App\Models\Appliance'),
          ],
          [
            'name' => $translator->translatePlural('Cluster', 'Clusters', 2),
            'endpoint' => '/view/clusters',
            'icon' => 'project diagram',
            'display' => $this->getRightForModel('\App\Models\Cluster'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('ITAM - Contracts & cost'),
        'id'   => 'contractcost',
        'icon' => 'laptop house',
        'sub'  => [
          [
            'name' => $translator->translatePlural('License', 'Licenses', 2),
            'endpoint' => '/view/softwarelicenses',
            'icon' => 'key',
            'display' => $this->getRightForModel('\App\Models\Softwarelicense'),
          ],
          [
            'name' => $translator->translatePlural('Budget', 'Budgets', 2),
            'endpoint' => '/view/budgets',
            'icon' => 'calculator',
            'display' => $this->getRightForModel('\App\Models\Budget'),
          ],
          [
            'name' => $translator->translatePlural('Contract', 'Contracts', 2),
            'endpoint' => '/view/contracts',
            'icon' => 'file signature',
            'display' => $this->getRightForModel('\App\Models\Contract'),
          ],
          [
            'name' => $translator->translatePlural('Line', 'Lines', 2),
            'endpoint' => '/view/lines',
            'icon' => 'phone',
            'display' => $this->getRightForModel('\App\Models\Line'),
          ],
          [
            'name' => $translator->translatePlural('Certificate', 'Certificates', 2),
            'endpoint' => '/view/certificates',
            'icon' => 'certificate',
            'display' => $this->getRightForModel('\App\Models\Certificate'),
          ],
          [
            'name' => $translator->translatePlural('Domain', 'Domains', 2),
            'endpoint' => '/view/domains',
            'icon' => 'globe americas',
            'display' => $this->getRightForModel('\App\Models\Domain'),
          ],
          [
            'name' => $translator->translatePlural('Supplier', 'Suppliers', 2),
            'endpoint' => '/view/suppliers',
            'icon' => 'dolly',
            'display' => $this->getRightForModel('\App\Models\Supplier'),
          ],
          [
            'name' => $translator->translatePlural('Contact', 'Contacts', 2),
            'endpoint' => '/view/contacts',
            'icon' => 'user tie',
            'display' => $this->getRightForModel('\App\Models\Contact'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('ITAM - Components'),
        'id'   => 'components',
        'icon' => 'laptop house',
        'sub'  => [
          [
            'name' => $translator->translatePlural('Power supply', 'Power supplies', 2),
            'endpoint' => '/view/devices/devicepowersupplies',
            'svgicon' => 'power-supply-unit',
            'display' => $this->getRightForModel('\App\Models\Devicepowersupply'),
          ],
          [
            'name' => $translator->translatePlural('Battery', 'Batteries', 2),
            'endpoint' => '/view/devices/devicebatteries',
            'icon' => 'battery half',
            'display' => $this->getRightForModel('\App\Models\Devicebattery'),
          ],
          [
            'name' => $translator->translatePlural('Case', 'Cases', 2),
            'endpoint' => '/view/devices/devicecases',
            'svgicon' => 'case',
            'display' => $this->getRightForModel('\App\Models\Devicecase'),
          ],
          [
            'name' => $translator->translatePlural('Sensor', 'Sensors', 2),
            'endpoint' => '/view/devices/devicesensors',
            'svgicon' => 'sensor',
            'display' => $this->getRightForModel('\App\Models\Devicesensor'),
          ],
          [
            'name' => $translator->translatePlural('Simcard', 'Simcards', 2),
            'endpoint' => '/view/devices/devicesimcards',
            'icon' => 'sim card',
            'display' => $this->getRightForModel('\App\Models\Devicesimcard'),
          ],
          [
            'name' => $translator->translatePlural('Graphics card', 'Graphics cards', 2),
            'endpoint' => '/view/devices/devicegraphiccards',
            'svgicon' => 'graphiccard',
            'display' => $this->getRightForModel('\App\Models\Devicegraphiccard'),
          ],
          [
            'name' => $translator->translatePlural('System board', 'System boards', 2),
            'endpoint' => '/view/devices/devicemotherboards',
            'svgicon' => 'motherboard',
            'display' => $this->getRightForModel('\App\Models\Devicemotherboard'),
          ],
          [
            'name' => $translator->translatePlural('Network card', 'Network cards', 2),
            'endpoint' => '/view/devices/devicenetworkcards',
            'svgicon' => 'networkcard',
            'display' => $this->getRightForModel('\App\Models\Devicenetworkcard'),
          ],
          [
            'name' => $translator->translatePlural('Soundcard', 'Soundcards', 2),
            'endpoint' => '/view/devices/devicesoundcards',
            'icon' => 'volume down',
            'display' => $this->getRightForModel('\App\Models\Devicesoundcard'),
          ],
          [
            'name' => $translator->translatePlural('Generic device', 'Generic devices', 2),
            'endpoint' => '/view/devices/devicegenerics',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicegeneric'),
          ],
          [
            'name' => $translator->translatePlural('Controller', 'Controllers', 2),
            'endpoint' => '/view/devices/devicecontrols',
            'icon' => 'microchip',
            'display' => $this->getRightForModel('\App\Models\Devicecontrol'),
          ],
          [
            'name' => $translator->translatePlural('Hard drive', 'Hard drives', 2),
            'endpoint' => '/view/devices/deviceharddrives',
            'svgicon' => 'ssd',
            'display' => $this->getRightForModel('\App\Models\Deviceharddrive'),
          ],
          [
            'name' => $translator->translatePlural('Firmware', 'Firmware', 2),
            'endpoint' => '/view/devices/devicefirmwares',
            'svgicon' => 'rom',
            'display' => $this->getRightForModel('\App\Models\Devicefirmware'),
          ],
          [
            'name' => $translator->translatePlural('Drive', 'Drives', 2),
            'endpoint' => '/view/devices/devicedrives',
            'icon' => 'hdd',
            'display' => $this->getRightForModel('\App\Models\Devicedrive'),
          ],
          [
            'name' => $translator->translatePlural('Memory', 'Memory', 2),
            'endpoint' => '/view/devices/devicememories',
            'icon' => 'memory',
            'display' => $this->getRightForModel('\App\Models\Devicememory'),
          ],
          [
            'name' => $translator->translatePlural('Processor', 'Processors', 2),
            'endpoint' => '/view/devices/deviceprocessors',
            'svgicon' => 'processor',
            'display' => $this->getRightForModel('\App\Models\Deviceprocessor'),
          ],
          [
            'name' => $translator->translatePlural('PCI device', 'PCI devices', 2),
            'endpoint' => '/view/devices/devicepcis',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicepci'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('ITSM'),
        'id'   => 'assistance',
        'icon' => 'hands helping',
        'sub'  => [
          [
            'name'  => $translator->translatePlural('Ticket', 'Tickets', 2),
            'endpoint' => '/view/tickets',
            'icon'  => 'hands helping',
            'display' => $this->getRightForModel('\App\Models\Ticket'),
          ],
          [
            'name'  => $translator->translatePlural('Problem', 'Problems', 2),
            'endpoint' => '/view/problems',
            'icon'  => 'drafting compass',
            'display' => $this->getRightForModel('\App\Models\Problem'),
          ],
          [
            'name'  => $translator->translatePlural('Change', 'Changes', 2),
            'endpoint' => '/view/changes',
            'icon'  => 'paint roller',
            'display' => $this->getRightForModel('\App\Models\Change'),
          ],
          [
            'name'  => $translator->translate('Recurrent tickets'),
            'endpoint' => '/view/ticketrecurrents',
            'icon'  => 'stopwatch',
            'display' => $this->getRightForModel('\App\Models\Ticketrecurrent'),
          ],
          [
            'name' => $translator->translatePlural('Receiver', 'Receivers', 2),
            'endpoint' => '/view/mailcollectors',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Mailcollector'),
          ],
          [
            'name' => $translator->translate('Business rules for tickets'),
            'endpoint' => '/view/rules/tickets',
            'icon' => 'magic',
            'display' => $this->getRightForModel('\App\Models\Rules\Ticket'),
          ],
          [
            'name' => $translator->translatePlural('Service level', 'Service levels', 2),
            'endpoint' => '/view/slms',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Slm'),
          ],
        ],
      ],
      [
        'name' => $translator->translatePlural('Form', 'Forms', 2),
        'id'   => 'form',
        'icon' => 'hands helping',
        'sub'  => [
          [
            'name'  => $translator->translatePlural('Form', 'Forms', 2),
            'endpoint' => '/view/forms',
            'icon'  => 'hands helping',
            'display' => $this->getRightForModel('\App\Models\Forms\Form'),
          ],
          [
            'name'  => $translator->translatePlural('Section', 'Sections', 2),
            'endpoint' => '/view/sections',
            'icon'  => 'exclamation triangle',
            'display' => $this->getRightForModel('\App\Models\Forms\Section'),
          ],
          [
            'name'  => $translator->translatePlural('Question', 'Questions', 2),
            'endpoint' => '/view/questions',
            'icon'  => 'clipboard check',
            'display' => $this->getRightForModel('\App\Models\Forms\Question'),
          ],
          // [
          //   'name'  => $translator->translatePlural('Answer', 'Answers', 2),
          //   'endpoint' => '/view/answers',
          //   'icon'  => 'clipboard check',
          //   'display' => $this->getRightForModel('\App\Models\Forms\Answer'),
          // ],
        ],
      ],
      [
        'name' => $translator->translate('User data'),
        'id'   => 'userdata',
        'icon' => 'block layout',
        'sub'  => [
          [
            'name' => $translator->translatePlural('User', 'Users', 2),
            'endpoint' => '/view/users',
            'icon' => 'user',
            'display' => $this->getRightForModel('\App\Models\User'),
          ],
          [
            'name' => $translator->translatePlural('Group', 'Groups', 2),
            'endpoint' => '/view/groups',
            'icon' => 'users',
            'display' => $this->getRightForModel('\App\Models\Group'),
          ],
          [
            'name' => $translator->translatePlural('Profile', 'Profiles', 2),
            'endpoint' => '/view/profiles',
            'icon' => 'user check',
            'display' => $this->getRightForModel('\App\Models\Profile'),
          ],
          [
            'name' => $translator->translate('Authentication SSO'),
            'endpoint' => '/view/authssos',
            'icon' => 'id card alternate',
            'display' => $this->getRightForModel('\App\Models\Authsso'),
          ],
          [
            'name' => $translator->translate('Provisionning LDAP'),
            'endpoint' => '/view/authldaps',
            'icon' => 'address book outline',
            'display' => $this->getRightForModel('\App\Models\Authldap'),
          ],
          [
            'name' => $translator->translatePlural('Saved search', 'Saved searches', 2),
            'endpoint' => '/view/savedsearchs',
            'icon' => 'bookmark',
            'display' => $this->getRightForModel('\App\Models\Savedsearch'),
          ],
          [
            'name' => $translator->translatePlural('Log', 'Logs', 2),
            'endpoint' => '/view/events',
            'icon' => 'scroll',
            'display' => $this->getRightForModel('\App\Models\Event'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('Alerting'),
        'id'   => 'userdata',
        'icon' => 'block layout',
        'sub'  => [
          [
            'name' => $translator->translate('Notification queue'),
            'endpoint' => '/view/queuednotifications',
            'icon' => 'list alt',
            'display' => $this->getRightForModel('\App\Models\Queuednotification'),
          ],
          [
            'name' => $translator->translatePlural('Notification', 'Notifications', 2) . ' - ' .
                      $translator->translatePlural('Notification template', 'Notification templates', 2),
            'endpoint' => '/view/notificationtemplates',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Notificationtemplate'),
          ],
          [
            'name' => $translator->translatePlural('Notification', 'Notifications', 2) . ' - ' .
                      $translator->translatePlural('Notification', 'Notifications', 2),
            'endpoint' => '/view/notifications',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Notification'),
          ],
          // [
          //   'name' => $translator->translatePlural('Alert', 'Alerts', 2),
          //   'endpoint' => '/view/news',
          //   'icon' => 'bell',
          //   'display' => $this->getRightForModel('\App\Models\Alert'),
          // ],
          [
            'name' => $translator->translatePlural('RSS feed', 'RSS feed', 2),
            'endpoint' => '/view/rssfeeds',
            'icon' => 'rss',
            'display' => $this->getRightForModel('\App\Models\Rssfeed'),
          ],
        ],
      ],
      [
        'name' => $translator->translate('System'),
        'id'   => 'system',
        'icon' => 'block layout',
        'sub'  => [
          [
            'name' => $translator->translatePlural('Entity', 'Entities', 2),
            'endpoint' => '/view/entities',
            'icon' => 'layer group',
            'display' => $this->getRightForModel('\App\Models\Entity'),
          ],
          [
            'name' => $translator->translatePlural('Automatic action', 'Automatic actions', 2),
            'endpoint' => '/view/crontasks',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Crontask'),
          ],
          // [
          //   'name' => $translator->translatePlural('Rule', 'Rules', 2),
          //   'endpoint' => '/view/rules',
          //   'icon' => 'book',
          // ],
        ],
      ],
      [
        'name' => $translator->translate('Knowledge hub'), // or
        'id'   => 'knowledgehub',
        'icon' => 'block layout',
        'sub'  => [
          [
            'name' => $translator->translatePlural('Document', 'Documents', 2),
            'endpoint' => '/view/documents',
            'icon' => 'file',
            'display' => $this->getRightForModel('\App\Models\Document'),
          ],
          [
            'name' => $translator->translatePlural('Project', 'Projects', 2),
            'endpoint' => '/view/projects',
            'icon' => 'columns',
            'display' => $this->getRightForModel('\App\Models\Project'),
          ],
          [
            'name' => $translator->translatePlural('Note', 'Notes', 2),
            'endpoint' => '/view/reminders',
            'icon' => 'sticky note',
            'display' => $this->getRightForModel('\App\Models\Reminder'),
          ],
          [
            'name' => $translator->translate('Fields unicity'),
            'endpoint' => '/view/fieldunicities',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Fieldunicity'),
          ],
          [
            'name' => $translator->translatePlural('External link', 'External links', 2),
            'endpoint' => '/view/links',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Link'),
          ],
        ],
      ],
      [
        'name' => 'Dropdowns',
        'id'   => 'dropdowns',
        'icon' => 'block layout',
        'sub'  => [
          [
            'name' => $translator->translatePlural('Location', 'Locations', 2),
            'endpoint' => '/view/dropdowns/locations',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Location'),
          ],
          [
            'name' => $translator->translatePlural('Status of items', 'Statuses of items', 2),
            'endpoint' => '/view/dropdowns/states',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\State'),
          ],
          [
            'name' => $translator->translatePlural('Manufacturer', 'Manufacturers', 2),
            'endpoint' => '/view/dropdowns/manufacturers',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Manufacturer'),
          ],
          [
            'name' => $translator->translatePlural('Blacklist', 'Blacklists', 2),
            'endpoint' => '/view/dropdowns/blacklists',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Blacklist'),
          ],
          [
            'name' => $translator->translate('Blacklisted mail content'),
            'endpoint' => '/view/dropdowns/blacklistedmailcontents',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Blacklistedmailcontent'),
          ],
          [
            'name' => $translator->translatePlural('ITIL category', 'ITIL categories', 2),
            'endpoint' => '/view/dropdowns/categories',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Category'),
          ],
          [
            'name' => $translator->translatePlural('Ticket template', 'Ticket templates', 2),
            'endpoint' => '/view/dropdowns/ticketemplates',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Tickettemplate'),
          ],
          // [
          //   'name' => $translator->translatePlural('Task category', 'Task categories', 2),
          //   'endpoint' => '/view/dropdowns/taskcategory',
          //   'icon' => 'edit',
          //   'display' => $this->getRightForModel('\App\Models\Taskcategories'),
          // ],
          // [
          //   'name' => $translator->translatePlural('Task template', 'Task templates', 2),
          //   'endpoint' => '/view/dropdowns/tasktemplates',
          //   'icon' => 'edit',
          //   'display' => $this->getRightForModel('\App\Models\Tasktemplate'),
          // ],
          [
            'name' => $translator->translatePlural('Solution type', 'Solution types', 2),
            'endpoint' => '/view/dropdowns/solutiontypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Solutiontype'),
          ],
          [
            'name' => $translator->translatePlural('Solution template', 'Solution templates', 2),
            'endpoint' => '/view/dropdowns/solutiontemplates',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Solutiontemplate'),
          ],
          [
            'name' => $translator->translatePlural('Request source', 'Request sources', 2),
            'endpoint' => '/view/dropdowns/requesttypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Requesttype'),
          ],
          [
            'name' => $translator->translatePlural('Followup template', 'Followup templates', 2),
            'endpoint' => '/view/dropdowns/followuptemplates',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Followuptemplate'),
          ],
          [
            'name' => $translator->translatePlural('Project state', 'Project states', 2),
            'endpoint' => '/view/dropdowns/projectstates',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Projectstate'),
          ],
          [
            'name' => $translator->translatePlural('Project type', 'Project types', 2),
            'endpoint' => '/view/dropdowns/projecttypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Projecttype'),
          ],
          [
            'name' => $translator->translatePlural('Project task', 'Project tasks', 2),
            'endpoint' => '/view/dropdowns/projecttasks',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Projecttask'),
          ],
          [
            'name' => $translator->translatePlural('Project tasks type', 'Project tasks types', 2),
            'endpoint' => '/view/dropdowns/projecttasktypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Projecttasktype'),
          ],
          [
            'name' => $translator->translatePlural('Project task template', 'Project task templates', 2),
            'endpoint' => '/view/dropdowns/projecttasktemplates',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Projecttasktemplate'),
          ],
          [
            'name' => $translator->translatePlural('Event category', 'Event categories', 2),
            'endpoint' => '/view/dropdowns/planningeventcategories',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Planningeventcategory'),
          ],
          [
            'name' => $translator->translatePlural('External events template', 'External events templates', 2),
            'endpoint' => '/view/dropdowns/planningexternaleventtemplates',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Planningexternaleventtemplate'),
          ],
          [
            'name' => $translator->translatePlural('Computer type', 'Computer types', 2),
            'endpoint' => '/view/dropdowns/computertypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Computertype'),
          ],
          [
            'name' => $translator->translatePlural('Networking equipment type', 'Networking equipment types', 2),
            'endpoint' => '/view/dropdowns/networkequipmenttypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Networkequipmenttype'),
          ],
          [
            'name' => $translator->translatePlural('Printer type', 'Printer types', 2),
            'endpoint' => '/view/dropdowns/printertypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Printertype'),
          ],
          [
            'name' => $translator->translatePlural('Monitor type', 'Monitor types', 2),
            'endpoint' => '/view/dropdowns/monitortypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Monitortype'),
          ],
          [
            'name' => $translator->translatePlural('Peripheral type', 'Peripheral types', 2),
            'endpoint' => '/view/dropdowns/peripheraltypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Peripheraltype'),
          ],
          [
            'name' => $translator->translatePlural('Phone type', 'Phone types', 2),
            'endpoint' => '/view/dropdowns/phonetypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Phonetype'),
          ],
          [
            'name' => $translator->translatePlural('License type', 'License types', 2),
            'endpoint' => '/view/dropdowns/softwarelicensetypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Softwarelicensetype'),
          ],
          [
            'name' => $translator->translatePlural('Cartridge type', 'Cartridge types', 2),
            'endpoint' => '/view/dropdowns/cartridgeitemtypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Cartridgeitemtype'),
          ],
          [
            'name' => $translator->translatePlural('Consumable type', 'Consumable types', 2),
            'endpoint' => '/view/dropdowns/consumableitemtypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Consumableitemtype'),
          ],
          [
            'name' => $translator->translatePlural('Contract type', 'Contract types', 2),
            'endpoint' => '/view/dropdowns/contracttypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Contracttype'),
          ],
          [
            'name' => $translator->translatePlural('Contact type', 'Contact types', 2),
            'endpoint' => '/view/dropdowns/contacttypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Contacttype'),
          ],
          [
            'name' => $translator->translatePlural('Generic type', 'Generic types', 2),
            'endpoint' => '/view/dropdowns/devicegenerictype',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicegenerictype'),
          ],
          [
            'name' => $translator->translatePlural('Sensor type', 'Sensor types', 2),
            'endpoint' => '/view/dropdowns/devicesensortype',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicesensortype'),
          ],
          [
            'name' => $translator->translatePlural('Memory type', 'Memory types', 2),
            'endpoint' => '/view/dropdowns/devicememorytype',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicememorytype'),
          ],
          [
            'name' => $translator->translatePlural('Third party type', 'Third party types', 2),
            'endpoint' => '/view/dropdowns/suppliertypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Suppliertype'),
          ],
          [
            'name' => $translator->translatePlural(
              'Interface type (Hard drive...)',
              'Interface types (Hard drive...)',
              2
            ),
            'endpoint' => '/view/dropdowns/interfacetypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Interfacetype'),
          ],
          [
            'name' => $translator->translatePlural('Case type', 'Case types', 2),
            'endpoint' => '/view/dropdowns/devicecasetype',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicecasetype'),
          ],
          [
            'name' => $translator->translatePlural('Phone power supply type', 'Phone power supply types', 2),
            'endpoint' => '/view/dropdowns/phonepowersupplies',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Phonepowersupply'),
          ],
          [
            'name' => $translator->translatePlural('File system', 'File systems', 2),
            'endpoint' => '/view/dropdowns/filesystems',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Filesystem'),
          ],
          [
            'name' => $translator->translatePlural('Certificate type', 'Certificate types', 2),
            'endpoint' => '/view/dropdowns/certificatetypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Certificatetype'),
          ],
          [
            'name' => $translator->translatePlural('Budget type', 'Budget types', 2),
            'endpoint' => '/view/dropdowns/budgettypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Budgettype'),
          ],
          [
            'name' => $translator->translatePlural('Simcard type', 'Simcard types', 2),
            'endpoint' => '/view/dropdowns/devicesimcardtypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicesimcardtype'),
          ],
          [
            'name' => $translator->translatePlural('Line type', 'Line types', 2),
            'endpoint' => '/view/dropdowns/linetypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Linetype'),
          ],
          [
            'name' => $translator->translatePlural('Rack type', 'Rack types', 2),
            'endpoint' => '/view/dropdowns/racktypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Racktype'),
          ],
          [
            'name' => $translator->translatePlural('PDU type', 'PDU types', 2),
            'endpoint' => '/view/dropdowns/pdutypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Pdutype'),
          ],
          [
            'name' => $translator->translatePlural('Passive device type', 'Passive device types', 2),
            'endpoint' => '/view/dropdowns/passivedcequipmenttypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Passivedcequipmenttype'),
          ],
          [
            'name' => $translator->translatePlural('Cluster type', 'Cluster types', 2),
            'endpoint' => '/view/dropdowns/clustertypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Clustertype'),
          ],
          [
            'name' => $translator->translatePlural('Computer model', 'Computer models', 2),
            'endpoint' => '/view/dropdowns/computermodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Computermodel'),
          ],
          [
            'name' => $translator->translatePlural('Networking equipment model', 'Networking equipment models', 2),
            'endpoint' => '/view/dropdowns/networkequipmentmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Networkequipmentmodel'),
          ],
          [
            'name' => $translator->translatePlural('Printer model', 'Printer models', 2),
            'endpoint' => '/view/dropdowns/printermodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Printermodel'),
          ],
          [
            'name' => $translator->translatePlural('Monitor model', 'Monitor models', 2),
            'endpoint' => '/view/dropdowns/monitormodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Monitormodel'),
          ],
          [
            'name' => $translator->translatePlural('Peripheral model', 'Peripheral models', 2),
            'endpoint' => '/view/dropdowns/peripheralmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Peripheralmodel'),
          ],
          [
            'name' => $translator->translatePlural('Phone model', 'Phone models', 2),
            'endpoint' => '/view/dropdowns/phonemodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Phonemodel'),
          ],
          [
            'name' => $translator->translatePlural('Device case model', 'Device case models', 2),
            'endpoint' => '/view/dropdowns/devicecasemodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicecasemodel'),
          ],
          [
            'name' => $translator->translatePlural('Device control model', 'Device control models', 2),
            'endpoint' => '/view/dropdowns/devicecontrolmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicecontrolmodel'),
          ],
          [
            'name' => $translator->translatePlural('Device drive model', 'Device drive models', 2),
            'endpoint' => '/view/dropdowns/devicedrivemodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicedrivemodel'),
          ],
          [
            'name' => $translator->translatePlural('Device generic model', 'Device generic models', 2),
            'endpoint' => '/view/dropdowns/devicegenericmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicegenericmodel'),
          ],
          [
            'name' => $translator->translatePlural('Device graphic card model', 'Device graphic card models', 2),
            'endpoint' => '/view/dropdowns/devicegraphiccardmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicegraphiccardmodel'),
          ],
          [
            'name' => $translator->translatePlural('Device hard drive model', 'Device hard drive models', 2),
            'endpoint' => '/view/dropdowns/deviceharddrivemodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Deviceharddrivemodel'),
          ],
          [
            'name' => $translator->translatePlural('Device memory model', 'Device memory models', 2),
            'endpoint' => '/view/dropdowns/devicememorymodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicememorymodel'),
          ],
          [
            'name' => $translator->translatePlural('System board model', 'System board models', 2),
            'endpoint' => '/view/dropdowns/devicemotherboardmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicemotherboardmodel'),
          ],
          [
            'name' => $translator->translatePlural('Network card model', 'Network card models', 2),
            'endpoint' => '/view/dropdowns/devicenetworkcardmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicenetworkcardmodel'),
          ],
          [
            'name' => $translator->translatePlural('Other component model', 'Other component models', 2),
            'endpoint' => '/view/dropdowns/devicepcimodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicepcimodel'),
          ],
          [
            'name' => $translator->translatePlural('Device power supply model', 'Device power supply models', 2),
            'endpoint' => '/view/dropdowns/devicepowersupplymodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicepowersupplymodel'),
          ],
          [
            'name' => $translator->translatePlural('Device processor model', 'Device processor models', 2),
            'endpoint' => '/view/dropdowns/deviceprocessormodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Deviceprocessormodel'),
          ],
          [
            'name' => $translator->translatePlural('Device sound card model', 'Device sound card models', 2),
            'endpoint' => '/view/dropdowns/devicesoundcardmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicesoundcardmodel'),
          ],
          [
            'name' => $translator->translatePlural('Device sensor model', 'Device sensor models', 2),
            'endpoint' => '/view/dropdowns/devicesensormodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Devicesensormodel'),
          ],
          [
            'name' => $translator->translatePlural('Rack model', 'Rack models', 2),
            'endpoint' => '/view/dropdowns/rackmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Rackmodel'),
          ],
          [
            'name' => $translator->translatePlural('Enclosure model', 'Enclosure models', 2),
            'endpoint' => '/view/dropdowns/enclosuremodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Enclosuremodel'),
          ],
          [
            'name' => $translator->translatePlural('PDU model', 'PDU models', 2),
            'endpoint' => '/view/dropdowns/pdumodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Pdumodel'),
          ],
          [
            'name' => $translator->translatePlural('Passive device model', 'Passive device models', 2),
            'endpoint' => '/view/dropdowns/passivedcequipmentmodels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Passivedcequipmentmodel'),
          ],
          [
            'name' => $translator->translatePlural('Virtualization system', 'Virtualization systems', 2),
            'endpoint' => '/view/dropdowns/virtualmachinetypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Virtualmachinetype'),
          ],
          [
            'name' => $translator->translatePlural('Virtualization model', 'Virtualization models', 2),
            'endpoint' => '/view/dropdowns/virtualmachinesystems',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Virtualmachinesystem'),
          ],
          [
            'name' => $translator->translatePlural('State of the virtual machine', 'States of the virtual machine', 2),
            'endpoint' => '/view/dropdowns/virtualmachinestates',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Virtualmachinestate'),
          ],
          [
            'name' => $translator->translatePlural('Document heading', 'Document headings', 2),
            'endpoint' => '/view/dropdowns/documentcategories',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Documentcategory'),
          ],
          [
            'name' => $translator->translatePlural('Document type', 'Document types', 2),
            'endpoint' => '/view/dropdowns/documenttypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Documenttype'),
          ],
          [
            'name' => $translator->translatePlural('Business criticity', 'Business criticities', 2),
            'endpoint' => '/view/dropdowns/businesscriticities',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Businesscriticity'),
          ],
          [
            'name' => $translator->translatePlural('Knowledge base category', 'Knowledge base categories', 2),
            'endpoint' => '/view/dropdowns/knowbaseitemcategories',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Knowbaseitemcategory'),
          ],
          [
            'name' => $translator->translatePlural('Calendar', 'Calendars', 2),
            'endpoint' => '/view/dropdowns/calendars',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Calendar'),
          ],
          [
            'name' => $translator->translatePlural('Close time', 'Close times', 2),
            'endpoint' => '/view/dropdowns/holidays',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Holiday'),
          ],
          [
            'name' => $translator->translatePlural(
              'Version of the operating system',
              'Versions of the operating systems',
              2
            ),
            'endpoint' => '/view/dropdowns/operatingsystemversions',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemversion'),
          ],
          [
            'name' => $translator->translatePlural('Service pack', 'Service packs', 2),
            'endpoint' => '/view/dropdowns/operatingsystemservicepacks',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemservicepack'),
          ],
          [
            'name' => $translator->translatePlural(
              'Operating system architecture',
              'Operating system architectures',
              2
            ),
            'endpoint' => '/view/dropdowns/operatingsystemarchitectures',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemarchitecture'),
          ],
          [
            'name' => $translator->translatePlural('Edition', 'Editions', 2),
            'endpoint' => '/view/dropdowns/operatingsystemeditions',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemedition'),
          ],
          [
            'name' => $translator->translatePlural('Kernel', 'Kernels', 2),
            'endpoint' => '/view/dropdowns/operatingsystemkernels',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemkernel'),
          ],
          [
            'name' => $translator->translatePlural('Kernel version', 'Kernel versions', 2),
            'endpoint' => '/view/dropdowns/operatingsystemkernelversions',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Operatingsystemkernelversion'),
          ],
          [
            'name' => $translator->translatePlural('Update Source', 'Update Sources', 2),
            'endpoint' => '/view/dropdowns/autoupdatesystems',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Autoupdatesystem'),
          ],
          [
            'name' => $translator->translatePlural('Network interface', 'Network interfaces', 2),
            'endpoint' => '/view/dropdowns/networkinterfaces',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Networkinterface'),
          ],
          [
            'name' => $translator->translatePlural('Network outlet', 'Network outlets', 2),
            'endpoint' => '/view/dropdowns/netpoints',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Netpoint'),
          ],
          [
            'name' => $translator->translatePlural('Network', 'Networks', 2),
            'endpoint' => '/view/dropdowns/networks',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Network'),
          ],
          [
            'name' => $translator->translatePlural('VLAN', 'VLANs', 2),
            'endpoint' => '/view/dropdowns/vlans',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Vlan'),
          ],
          [
            'name' => $translator->translatePlural('Line operator', 'Line operators', 2),
            'endpoint' => '/view/dropdowns/lineoperators',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Lineoperator'),
          ],
          [
            'name' => $translator->translatePlural('Domain type', 'Domain types', 2),
            'endpoint' => '/view/dropdowns/domaintypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Domaintype'),
          ],
          [
            'name' => $translator->translatePlural('Domain relation', 'Domains relations', 2),
            'endpoint' => '/view/dropdowns/domainrelations',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Domainrelation'),
          ],
          [
            'name' => $translator->translatePlural('Record type', 'Records types', 2),
            'endpoint' => '/view/dropdowns/domainrecordtypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Domainrecordtype'),
          ],
          [
            'name' => $translator->translatePlural('IP network', 'IP networks', 2),
            'endpoint' => '/view/dropdowns/ipnetworks',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Ipnetwork'),
          ],
          [
            'name' => $translator->translatePlural('Internet domain', 'Internet domains', 2),
            'endpoint' => '/view/dropdowns/fqdns',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Fqdn'),
          ],
          [
            'name' => $translator->translatePlural('Wifi network', 'Wifi networks', 2),
            'endpoint' => '/view/dropdowns/wifinetworks',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Wifinetwork'),
          ],
          [
            'name' => $translator->translatePlural('Network name', 'Network names', 2),
            'endpoint' => '/view/dropdowns/networknames',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Networkname'),
          ],
          [
            'name' => $translator->translatePlural('Software category', 'Software categories', 2),
            'endpoint' => '/view/dropdowns/softwarecategories',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Softwarecategory'),
          ],
          [
            'name' => $translator->translatePlural('User title', 'Users titles', 2),
            'endpoint' => '/view/dropdowns/usertitles',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Usertitle'),
          ],
          [
            'name' => $translator->translatePlural('User category', 'User categories', 2),
            'endpoint' => '/view/dropdowns/usercategories',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Usercategory'),
          ],
          [
            'name' => $translator->translatePlural('LDAP criterion', 'LDAP criteria', 2),
            'endpoint' => '/view/dropdowns/rulerightparameters',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Rulerightparameter'),
          ],
          [
            'name' => $translator->translatePlural(
              'Ignored value for the unicity',
              'Ignored values for the unicity',
              2
            ),
            'endpoint' => '/view/dropdowns/fieldblacklists',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Fieldblacklist'),
          ],
          [
            'name' => $translator->translatePlural(
              'Field storage of the login in the HTTP request',
              'Fields storage of the login in the HTTP request',
              2
            ),
            'endpoint' => '/view/dropdowns/ssovariables',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Ssovariable'),
          ],
          [
            'name' => $translator->translatePlural('Plug', 'Plugs', 2),
            'endpoint' => '/view/dropdowns/plugs',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Plug'),
          ],
          [
            'name' => $translator->translatePlural('Appliance type', 'Appliance types', 2),
            'endpoint' => '/view/dropdowns/appliancetypes',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Appliancetype'),
          ],
          [
            'name' => $translator->translatePlural('Appliance environment', 'Appliance environments', 2),
            'endpoint' => '/view/dropdowns/applianceenvironments',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\Applianceenvironment'),
          ],
          [
            'name' => $translator->translatePlural('Oauth IMAP application', 'Oauth IMAP applications', 2),
            'endpoint' => '/view/dropdowns/oauthimapapplications',
            'icon' => 'edit',
            'display' => $this->getRightForModel('\App\Models\OauthimapApplication'),
          ],
        ],
      ],
    ];
  }

  private function loadRights()
  {
    $profile = \App\Models\Profile::find($GLOBALS['profile_id']);
    if (!is_null($profile))
    {
      $dbRights = \App\Models\Profileright::where('profile_id', $profile->id)->get();
      foreach ($dbRights as $dbRight)
      {
        if ($dbRight->read || $dbRight->readmyitems || $dbRight->readmygroupitems)
        {
          $this->setRightForModel($dbRight->model);
        }
      }
    }
  }

  private function setRightForModel($model)
  {
    $this->rights['\\' . $model] = true;
  }

  private function getRightForModel($modelName)
  {
    if (isset($this->rights[$modelName]))
    {
      return true;
    }
    return false;
  }

  private function cleanMenuByDisplay($menu)
  {
    $newMenu = [];
    foreach ($menu as $item)
    {
      $submenu = [];
      foreach ($item['sub'] as $key => $subitem)
      {
        if (isset($subitem['display']) && $subitem['display'])
        {
          $submenu[] = $subitem;
        }
      }

      if (count($submenu) > 0)
      {
        $catMenu = [
          'name' => $item['name'],
          'id'   => $item['id'],
          'icon' => $item['icon'],
          'sub'  => $submenu,
        ];
        $newMenu[] = $catMenu;
      }
    }
    return $newMenu;
  }
}
