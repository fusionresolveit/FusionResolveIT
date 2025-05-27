<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\Menu as DataInterfaceMenu;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Menu
{
  /** @var array<mixed> */
  protected $rights = [];

  /**
   * @return array<mixed>
   */
  public function getMenu(Request $request): array
  {
    $this->loadRights();
    return $this->cleanMenuByDisplay($this->menuData($request));
  }

  /**
   * @param array<mixed> $menu
   *
   * @return array<mixed>
   */
  public function getMenubookmark(array $menu): array
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
        if (isset($endpoints[$sub->endpoint]))
        {
          $sub->name = $endpoints[$sub->endpoint];
          $bookmarks[] = $sub;
        }
      }
    }
    return $bookmarks;
  }

  /**
   * @return array<mixed>
   */
  private function menuData(Request $request): array
  {
    global $translator;

    $uri = $request->getUri();

    return [
      [
        'name'  => 'hardwareinventory',
        'title' => $translator->translate('ITAM - Hardware inventory'),
        'icon'  => 'laptop house',
        'sub'   => [
          new DataInterfaceMenu(
            'datacenters',
            $translator->translatePlural('Data center', 'Data centers', 2),
            'warehouse',
            $this->getRightForModel('\App\Models\Datacenter'),
            '/view/datacenters',
            $translator->translate('Dedicated space within a building'),
          ),
          new DataInterfaceMenu(
            'computers',
            $translator->translatePlural('Computer', 'Computers', 2),
            'laptop',
            $this->getRightForModel('\App\Models\Computer'),
            '/view/computers',
            $translator->translate('Computers, servers, laptop...'),
          ),
          new DataInterfaceMenu(
            'monitors',
            $translator->translatePlural('Monitor', 'Monitors', 2),
            'desktop',
            $this->getRightForModel('\App\Models\Monitor'),
            '/view/monitors',
            $translator->translate('Display device for computers')
          ),
          new DataInterfaceMenu(
            'networkequipments',
            $translator->translatePlural('Network device', 'Network devices', 2),
            'network wired',
            $this->getRightForModel('\App\Models\Networkequipment'),
            '/view/networkequipments',
            $translator->translate('Device for communication between devices on network'),
          ),
          new DataInterfaceMenu(
            'peripherals',
            $translator->translatePlural('Device', 'Devices', 2),
            'usb',
            $this->getRightForModel('\App\Models\Peripheral'),
            '/view/peripherals',
            $translator->translate('Peripherals like webcam, keyboard, mouse...'),
          ),
          new DataInterfaceMenu(
            'printers',
            $translator->translatePlural('Printer', 'Printers', 2),
            'print',
            $this->getRightForModel('\App\Models\Printer'),
            '/view/printers',
            $translator->translate('list of printers...'),
          ),
          new DataInterfaceMenu(
            'phones',
            $translator->translatePlural('Phone', 'Phones', 2),
            'phone',
            $this->getRightForModel('\App\Models\Phone'),
            '/view/phones',
            $translator->translate('Phones, smartphones'),
          ),
          new DataInterfaceMenu(
            'cartridges',
            $translator->translatePlural('Cartridge', 'Cartridges', 2),
            'fill drip',
            $this->getRightForModel('\App\Models\Cartridgeitem'),
            '/view/cartridgeitems',
            $translator->translate('Cartriges for printers'),
          ),
          new DataInterfaceMenu(
            'consumableitems',
            $translator->translatePlural('Consumable', 'Consumables', 2),
            'box open',
            $this->getRightForModel('\App\Models\Consumableitem'),
            '/view/consumableitems',
            $translator->translate('Consumable like USB keys, headphones...'),
          ),
          new DataInterfaceMenu(
            'racks',
            $translator->translatePlural('Rack', 'Racks', 2),
            'server',
            $this->getRightForModel('\App\Models\Rack'),
            '/view/racks',
            '',
          ),
          new DataInterfaceMenu(
            'enclosures',
            $translator->translatePlural('Enclosure', 'Enclosures', 2),
            'th',
            $this->getRightForModel('\App\Models\Enclosure'),
            '/view/enclosures',
            '',
          ),
          new DataInterfaceMenu(
            'pdus',
            $translator->translatePlural('PDU', 'PDUs', 2),
            'plug',
            $this->getRightForModel('\App\Models\Pdu'),
            '/view/pdus',
            '',
          ),
          new DataInterfaceMenu(
            'passivedcequipments',
            $translator->translatePlural('Passive device', 'Passive devices', 2),
            'th list',
            $this->getRightForModel('\App\Models\Passivedcequipment'),
            '/view/passivedcequipments',
            $translator->translate('cables, patch panels...'),
          ),
          new DataInterfaceMenu(
            'simcards',
            $translator->translatePlural('Simcard', 'Simcards', 2),
            'sim card',
            $this->getRightForModel('\App\Models\ItemDevicesimcard'),
            '/view/itemdevicesimcards',
            '',
          ),
        ],
      ],
      [
        'name'  => 'softwareinventiry',
        'title' => $translator->translate('ITAM - Software inventory'),
        'icon'  => 'archive',
        'sub'   => [
          new DataInterfaceMenu(
            'software',
            $translator->translatePlural('Software', 'Software', 2),
            'software',
            $this->getRightForModel('\App\Models\Software'),
            '/view/softwares',
            $translator->translate('List of software, version, where installed...'),
          ),
          new DataInterfaceMenu(
            'openratingsystems',
            $translator->translatePlural('Operating system', 'Operating systems', 2),
            'operatingsystem',
            $this->getRightForModel('\App\Models\Operatingsystem'),
            '/view/operatingsystems',
            '',
          ),
          new DataInterfaceMenu(
            'appliances',
            $translator->translatePlural('Appliance', 'Appliances', 2),
            'cubes',
            $this->getRightForModel('\App\Models\Appliance'),
            '/view/appliances',
            '',
          ),
          new DataInterfaceMenu(
            'clusters',
            $translator->translatePlural('Cluster', 'Clusters', 2),
            'project diagram',
            $this->getRightForModel('\App\Models\Cluster'),
            '/view/clusters',
            '',
          ),
        ],
      ],
      [
        'name'  => 'contractcost',
        'title' => $translator->translate('ITAM - Contracts & cost'),
        'icon'  => 'file signature',
        'sub'   => [
          new DataInterfaceMenu(
            'softwarelicenses',
            $translator->translatePlural('License', 'Licenses', 2),
            'key',
            $this->getRightForModel('\App\Models\Softwarelicense'),
            '/view/softwarelicenses',
            '',
          ),
          new DataInterfaceMenu(
            'budgets',
            $translator->translatePlural('Budget', 'Budgets', 2),
            'calculator',
            $this->getRightForModel('\App\Models\Budget'),
            '/view/budgets',
            '',
          ),
          new DataInterfaceMenu(
            'contracts',
            $translator->translatePlural('Contract', 'Contracts', 2),
            'file signature',
            $this->getRightForModel('\App\Models\Contract'),
            '/view/contracts',
            '',
          ),
          new DataInterfaceMenu(
            'lines',
            $translator->translatePlural('Line', 'Lines', 2),
            'phone',
            $this->getRightForModel('\App\Models\Line'),
            '/view/lines',
            '',
          ),
          new DataInterfaceMenu(
            'certificates',
            $translator->translatePlural('Certificate', 'Certificates', 2),
            'certificate',
            $this->getRightForModel('\App\Models\Certificate'),
            '/view/certificates',
            '',
          ),
          new DataInterfaceMenu(
            'domains',
            $translator->translatePlural('Domain', 'Domains', 2),
            'globe americas',
            $this->getRightForModel('\App\Models\Domain'),
            '/view/domains',
            '',
          ),
          new DataInterfaceMenu(
            'suppliers',
            $translator->translatePlural('Supplier', 'Suppliers', 2),
            'dolly',
            $this->getRightForModel('\App\Models\Supplier'),
            '/view/suppliers',
            '',
          ),
          new DataInterfaceMenu(
            'contacts',
            $translator->translatePlural('Contact', 'Contacts', 2),
            'user tie',
            $this->getRightForModel('\App\Models\Contact'),
            '/view/contacts',
            '',
          ),
        ],
      ],
      [
        'name'  => 'components',
        'title' => $translator->translate('ITAM - Components'),
        'icon'  => 'microchip',
        'sub'   => [
          new DataInterfaceMenu(
            'devicepowersupplies',
            $translator->translatePlural('Power supply', 'Power supplies', 2),
            'power-supply-unit',
            $this->getRightForModel('\App\Models\Devicepowersupply'),
            '/view/devices/devicepowersupplies',
            '',
          ),
          new DataInterfaceMenu(
            'devicebatteries',
            $translator->translatePlural('Battery', 'Batteries', 2),
            'battery half',
            $this->getRightForModel('\App\Models\Devicebattery'),
            '/view/devices/devicebatteries',
            '',
          ),
          new DataInterfaceMenu(
            'devicecases',
            $translator->translatePlural('Case', 'Cases', 2),
            'case',
            $this->getRightForModel('\App\Models\Devicecase'),
            '/view/devices/devicecases',
            '',
          ),
          new DataInterfaceMenu(
            'devicesensors',
            $translator->translatePlural('Sensor', 'Sensors', 2),
            'sensor',
            $this->getRightForModel('\App\Models\Devicesensor'),
            '/view/devices/devicesensors',
            '',
          ),
          new DataInterfaceMenu(
            'devicesimcards',
            $translator->translatePlural('Simcard', 'Simcards', 2),
            'sim card',
            $this->getRightForModel('\App\Models\Devicesimcard'),
            '/view/devices/devicesimcards',
            '',
          ),
          new DataInterfaceMenu(
            'devicegraphiccards',
            $translator->translatePlural('Graphics card', 'Graphics cards', 2),
            'graphiccard',
            $this->getRightForModel('\App\Models\Devicegraphiccard'),
            '/view/devices/devicegraphiccards',
            '',
          ),
          new DataInterfaceMenu(
            'devicemotherboards',
            $translator->translatePlural('System board', 'System boards', 2),
            'motherboard',
            $this->getRightForModel('\App\Models\Devicemotherboard'),
            '/view/devices/devicemotherboards',
            '',
          ),
          new DataInterfaceMenu(
            'devicenetworkcards',
            $translator->translatePlural('Network card', 'Network cards', 2),
            'networkcard',
            $this->getRightForModel('\App\Models\Devicenetworkcard'),
            '/view/devices/devicenetworkcards',
            '',
          ),
          new DataInterfaceMenu(
            'devicesoundcards',
            $translator->translatePlural('Soundcard', 'Soundcards', 2),
            'volume down',
            $this->getRightForModel('\App\Models\Devicesoundcard'),
            '/view/devices/devicesoundcards',
            '',
          ),
          new DataInterfaceMenu(
            'devicegenerics',
            $translator->translatePlural('Generic device', 'Generic devices', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicegeneric'),
            '/view/devices/devicegenerics',
            '',
          ),
          new DataInterfaceMenu(
            'devicecontrols',
            $translator->translatePlural('Controller', 'Controllers', 2),
            'microchip',
            $this->getRightForModel('\App\Models\Devicecontrol'),
            '/view/devices/devicecontrols',
            '',
          ),
          new DataInterfaceMenu(
            'deviceharddrives',
            $translator->translatePlural('Hard drive', 'Hard drives', 2),
            'ssd',
            $this->getRightForModel('\App\Models\Deviceharddrive'),
            '/view/devices/deviceharddrives',
            '',
          ),
          new DataInterfaceMenu(
            'devicefirmwares',
            $translator->translatePlural('Firmware', 'Firmware', 2),
            'rom',
            $this->getRightForModel('\App\Models\Devicefirmware'),
            '/view/devices/devicefirmwares',
            '',
          ),
          new DataInterfaceMenu(
            'devicedrives',
            $translator->translatePlural('Drive', 'Drives', 2),
            'hdd',
            $this->getRightForModel('\App\Models\Devicedrive'),
            '/view/devices/devicedrives',
            '',
          ),
          new DataInterfaceMenu(
            'memorymodules',
            $translator->translatePlural('Memory', 'Memory', 2),
            'memory',
            $this->getRightForModel('\App\Models\Memorymodule'),
            '/view/devices/memorymodules',
            '',
          ),
          new DataInterfaceMenu(
            'deviceprocessors',
            $translator->translatePlural('Processor', 'Processors', 2),
            'processor',
            $this->getRightForModel('\App\Models\Deviceprocessor'),
            '/view/devices/deviceprocessors',
            '',
          ),
          new DataInterfaceMenu(
            'devicepcis',
            $translator->translatePlural('PCI device', 'PCI devices', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicepci'),
            '/view/devices/devicepcis',
            '',
          ),
        ],
      ],
      [
        'name'  => 'assistance',
        'title' => $translator->translate('ITSM'),
        'icon'  => 'hands helping',
        'sub'   => [
          new DataInterfaceMenu(
            'tickets',
            $translator->translatePlural('Ticket', 'Tickets', 2),
            'hands helping',
            $this->getRightForModel('\App\Models\Ticket'),
            '/view/tickets',
            '',
          ),
          new DataInterfaceMenu(
            'problems',
            $translator->translatePlural('Problem', 'Problems', 2),
            'drafting compass',
            $this->getRightForModel('\App\Models\Problem'),
            '/view/problems',
            '',
          ),
          new DataInterfaceMenu(
            'changes',
            $translator->translatePlural('Change', 'Changes', 2),
            'paint roller',
            $this->getRightForModel('\App\Models\Change'),
            '/view/changes',
            '',
          ),
          new DataInterfaceMenu(
            'ticketrecurrents',
            $translator->translate('Recurrent tickets'),
            'stopwatch',
            $this->getRightForModel('\App\Models\Ticketrecurrent'),
            '/view/ticketrecurrents',
            '',
          ),
          new DataInterfaceMenu(
            'mailcollectors',
            $translator->translatePlural('Receiver', 'Receivers', 2),
            'edit',
            $this->getRightForModel('\App\Models\Mailcollector'),
            '/view/mailcollectors',
            '',
          ),
          new DataInterfaceMenu(
            'rulestickets',
            $translator->translate('Business rules for tickets'),
            'magic',
            $this->getRightForModel('\App\Models\Rules\Ticket'),
            '/view/rules/tickets',
            '',
          ),
          new DataInterfaceMenu(
            'slms',
            $translator->translatePlural('Service level', 'Service levels', 2),
            'edit',
            $this->getRightForModel('\App\Models\Slm'),
            '/view/slms',
            '',
          ),
        ],
      ],
      [
        'name'  => 'form',
        'title' => $translator->translatePlural('Form', 'Forms', 2),
        'icon'  => 'cubes',
        'sub'   => [
          new DataInterfaceMenu(
            'forms',
            $translator->translatePlural('Form', 'Forms', 2),
            'hands helping',
            $this->getRightForModel('\App\Models\Forms\Form'),
            '/view/forms',
            '',
          ),
          new DataInterfaceMenu(
            'formsections',
            $translator->translatePlural('Section', 'Sections', 2),
            'exclamation triangle',
            $this->getRightForModel('\App\Models\Forms\Section'),
            '/view/sections',
            '',
          ),
          new DataInterfaceMenu(
            'formquestions',
            $translator->translatePlural('Question', 'Questions', 2),
            'clipboard check',
            $this->getRightForModel('\App\Models\Forms\Question'),
            '/view/questions',
            '',
          ),
          // [
          //   'name'  => $translator->translatePlural('Answer', 'Answers', 2),
          //   'endpoint' => '/view/answers',
          //   'icon'  => 'clipboard check',
          //   'display' => $this->getRightForModel('\App\Models\Forms\Answer'),
          // ],
        ],
      ],
      [
        'name'  => 'userdata',
        'title' => $translator->translate('User data'),
        'icon'  => 'user',
        'sub'   => [
          new DataInterfaceMenu(
            'users',
            $translator->translatePlural('User', 'Users', 2),
            'user',
            $this->getRightForModel('\App\Models\User'),
            '/view/users',
            '',
          ),
          new DataInterfaceMenu(
            'groups',
            $translator->translatePlural('Group', 'Groups', 2),
            'users',
            $this->getRightForModel('\App\Models\Group'),
            '/view/groups',
            '',
          ),
          new DataInterfaceMenu(
            'profiles',
            $translator->translatePlural('Profile', 'Profiles', 2),
            'user check',
            $this->getRightForModel('\App\Models\Profile'),
            '/view/profiles',
            '',
          ),
          new DataInterfaceMenu(
            'authssos',
            $translator->translate('Authentication SSO'),
            'id card alternate',
            $this->getRightForModel('\App\Models\Authsso'),
            '/view/authssos',
            '',
          ),
          new DataInterfaceMenu(
            'authldaps',
            $translator->translate('Provisionning LDAP'),
            'address book outline',
            $this->getRightForModel('\App\Models\Authldap'),
            '/view/authldaps',
            '',
          ),
          new DataInterfaceMenu(
            'rulesusers',
            $translator->translate('Rules for users'),
            'magic',
            $this->getRightForModel('\App\Models\Rules\User'),
            '/view/rules/users',
            '',
          ),
          new DataInterfaceMenu(
            'savedsearches',
            $translator->translatePlural('Saved search', 'Saved searches', 2),
            'bookmark',
            $this->getRightForModel('\App\Models\Savedsearch'),
            '/view/savedsearchs',
            '',
          ),
          new DataInterfaceMenu(
            'audits',
            $translator->translatePlural('Audit', 'Audits', 2),
            'scroll',
            $this->getRightForModel('\App\Models\Audit'),
            '/view/audits',
            '',
          ),
        ],
      ],
      [
        'name'  => 'alerting',
        'title' => $translator->translate('Alerting'),
        'icon'  => 'bullhorn',
        'sub'   => [
          new DataInterfaceMenu(
            'queuednotifications',
            $translator->translate('Notification queue'),
            'list alt',
            $this->getRightForModel('\App\Models\Queuednotification'),
            '/view/queuednotifications',
            '',
          ),
          new DataInterfaceMenu(
            'notificationtemplates',
            $translator->translatePlural('Notification', 'Notifications', 2) . ' - ' .
              $translator->translatePlural('Notification template', 'Notification templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Notificationtemplate'),
            '/view/notificationtemplates',
            '',
          ),
          new DataInterfaceMenu(
            'notifications',
            $translator->translatePlural('Notification', 'Notifications', 2) . ' - ' .
              $translator->translatePlural('Notification', 'Notifications', 2),
            'edit',
            $this->getRightForModel('\App\Models\Notification'),
            '/view/notifications',
            '',
          ),
          new DataInterfaceMenu(
            'alerts',
            $translator->translatePlural('Alert', 'Alerts', 2),
            'bell',
            $this->getRightForModel('\App\Models\Alert'),
            '/view/alerts',
            '',
          ),
          new DataInterfaceMenu(
            'rssfeeds',
            $translator->translatePlural('RSS feed', 'RSS feed', 2),
            'rss',
            $this->getRightForModel('\App\Models\Rssfeed'),
            '/view/rssfeeds',
            '',
          ),
        ],
      ],
      [
        'name'  => 'system',
        'title' => $translator->translate('System'),
        'icon'  => 'layer group',
        'sub'   => [
          new DataInterfaceMenu(
            'entities',
            $translator->translatePlural('Entity', 'Entities', 2),
            'layer group',
            $this->getRightForModel('\App\Models\Entity'),
            '/view/entities',
            '',
          ),
          new DataInterfaceMenu(
            'crontasks',
            $translator->translatePlural('Automatic action', 'Automatic actions', 2),
            'edit',
            $this->getRightForModel('\App\Models\Crontask'),
            '/view/crontasks',
            '',
          ),
          // [
          //   'name' => $translator->translatePlural('Rule', 'Rules', 2),
          //   'endpoint' => '/view/rules',
          //   'icon' => 'book',
          // ],
        ],
      ],
      [
        'name'  => 'knowledgehub',
        'title' => $translator->translate('Knowledge hub'), // or
        'icon'  => 'book',
        'sub'   => [
          new DataInterfaceMenu(
            'knowledgebasearticles',
            $translator->translatePlural('Knowledge base article', 'Knowledge base articles', 2),
            'edit',
            $this->getRightForModel('\App\Models\Knowledgebasearticle'),
            '/view/knowledgebasearticles',
            '',
          ),
          new DataInterfaceMenu(
            'documents',
            $translator->translatePlural('Document', 'Documents', 2),
            'file',
            $this->getRightForModel('\App\Models\Document'),
            '/view/documents',
            '',
          ),
          new DataInterfaceMenu(
            'projects',
            $translator->translatePlural('Project', 'Projects', 2),
            'columns',
            $this->getRightForModel('\App\Models\Project'),
            '/view/projects',
            '',
          ),
          new DataInterfaceMenu(
            'reminders',
            $translator->translatePlural('Note', 'Notes', 2),
            'sticky note',
            $this->getRightForModel('\App\Models\Reminder'),
            '/view/reminders',
            '',
          ),
          new DataInterfaceMenu(
            'fieldunicities',
            $translator->translate('Fields unicity'),
            'edit',
            $this->getRightForModel('\App\Models\Fieldunicity'),
            '/view/fieldunicities',
            '',
          ),
          new DataInterfaceMenu(
            'links',
            $translator->translatePlural('External link', 'External links', 2),
            'edit',
            $this->getRightForModel('\App\Models\Link'),
            '/view/links',
            '',
          ),
        ],
      ],
      [
        'name'  => 'dropdowns',
        'title' => 'Dropdowns',
        'icon'  => 'list',
        'sub'   => [
          new DataInterfaceMenu(
            'locations',
            $translator->translatePlural('Location', 'Locations', 2),
            'edit',
            $this->getRightForModel('\App\Models\Location'),
            '/view/locations',
            '',
          ),
          new DataInterfaceMenu(
            'states',
            $translator->translatePlural('Status of items', 'Statuses of items', 2),
            'edit',
            $this->getRightForModel('\App\Models\State'),
            '/view/states',
            '',
          ),
          new DataInterfaceMenu(
            'manufacturers',
            $translator->translatePlural('Manufacturer', 'Manufacturers', 2),
            'edit',
            $this->getRightForModel('\App\Models\Manufacturer'),
            '/view/manufacturers',
            '',
          ),
          new DataInterfaceMenu(
            'blacklists',
            $translator->translatePlural('Blacklist', 'Blacklists', 2),
            'edit',
            $this->getRightForModel('\App\Models\Blacklist'),
            '/view/blacklists',
            '',
          ),
          new DataInterfaceMenu(
            'blacklistedmailcontents',
            $translator->translate('Blacklisted mail content'),
            'edit',
            $this->getRightForModel('\App\Models\Blacklistedmailcontent'),
            '/view/blacklistedmailcontents',
            '',
          ),
          new DataInterfaceMenu(
            'categories',
            $translator->translatePlural('Category', 'Categories', 2),
            'edit',
            $this->getRightForModel('\App\Models\Category'),
            '/view/categories',
            '',
          ),
          new DataInterfaceMenu(
            'tickettemplates',
            $translator->translatePlural('Ticket template', 'Ticket templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Tickettemplate'),
            '/view/ticketemplates',
            '',
          ),
          new DataInterfaceMenu(
            'solutiontypes',
            $translator->translatePlural('Solution type', 'Solution types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Solutiontype'),
            '/view/solutiontypes',
            '',
          ),
          new DataInterfaceMenu(
            'solutiontemplates',
            $translator->translatePlural('Solution template', 'Solution templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Solutiontemplate'),
            '/view/solutiontemplates',
            '',
          ),
          new DataInterfaceMenu(
            'requesttypes',
            $translator->translatePlural('Request source', 'Request sources', 2),
            'edit',
            $this->getRightForModel('\App\Models\Requesttype'),
            '/view/requesttypes',
            '',
          ),
          new DataInterfaceMenu(
            'followuptemplates',
            $translator->translatePlural('Followup template', 'Followup templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Followuptemplate'),
            '/view/followuptemplates',
            '',
          ),
          new DataInterfaceMenu(
            'projectstates',
            $translator->translatePlural('Project state', 'Project states', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projectstate'),
            '/view/projectstates',
            '',
          ),
          new DataInterfaceMenu(
            'projecttypes',
            $translator->translatePlural('Project type', 'Project types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projecttype'),
            '/view/projecttypes',
            '',
          ),
          new DataInterfaceMenu(
            'projecttasks',
            $translator->translatePlural('Project task', 'Project tasks', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projecttask'),
            '/view/projecttasks',
            '',
          ),
          new DataInterfaceMenu(
            'projecttasktypes',
            $translator->translatePlural('Project tasks type', 'Project tasks types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projecttasktype'),
            '/view/projecttasktypes',
            '',
          ),
          new DataInterfaceMenu(
            'projecttasktemplates',
            $translator->translatePlural('Project task template', 'Project task templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projecttasktemplate'),
            '/view/projecttasktemplates',
            '',
          ),
          new DataInterfaceMenu(
            'planningeventcategories',
            $translator->translatePlural('Event category', 'Event categories', 2),
            'edit',
            $this->getRightForModel('\App\Models\Planningeventcategory'),
            '/view/planningeventcategories',
            '',
          ),
          new DataInterfaceMenu(
            'planningexternaleventtemplates',
            $translator->translatePlural('External events template', 'External events templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Planningexternaleventtemplate'),
            '/view/planningexternaleventtemplates',
            '',
          ),
          new DataInterfaceMenu(
            'computertypes',
            $translator->translatePlural('Computer type', 'Computer types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Computertype'),
            '/view/computertypes',
            '',
          ),
          new DataInterfaceMenu(
            'networkequipmenttypes',
            $translator->translatePlural('Networking equipment type', 'Networking equipment types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Networkequipmenttype'),
            '/view/networkequipmenttypes',
            '',
          ),
          new DataInterfaceMenu(
            'printertypes',
            $translator->translatePlural('Printer type', 'Printer types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Printertype'),
            '/view/printertypes',
            '',
          ),
          new DataInterfaceMenu(
            'monitortypes',
            $translator->translatePlural('Monitor type', 'Monitor types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Monitortype'),
            '/view/monitortypes',
            '',
          ),
          new DataInterfaceMenu(
            'peripheraltypes',
            $translator->translatePlural('Peripheral type', 'Peripheral types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Peripheraltype'),
            '/view/peripheraltypes',
            '',
          ),
          new DataInterfaceMenu(
            'phonetypes',
            $translator->translatePlural('Phone type', 'Phone types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Phonetype'),
            '/view/phonetypes',
            '',
          ),
          new DataInterfaceMenu(
            'softwarelicensetypes',
            $translator->translatePlural('License type', 'License types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Softwarelicensetype'),
            '/view/softwarelicensetypes',
            '',
          ),
          new DataInterfaceMenu(
            'cartridgeitemtypes',
            $translator->translatePlural('Cartridge type', 'Cartridge types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Cartridgeitemtype'),
            '/view/cartridgeitemtypes',
            '',
          ),
          new DataInterfaceMenu(
            'consumableitemtypes',
            $translator->translatePlural('Consumable type', 'Consumable types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Consumableitemtype'),
            '/view/consumableitemtypes',
            '',
          ),
          new DataInterfaceMenu(
            'contracttypes',
            $translator->translatePlural('Contract type', 'Contract types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Contracttype'),
            '/view/contracttypes',
            '',
          ),
          new DataInterfaceMenu(
            'contacttypes',
            $translator->translatePlural('Contact type', 'Contact types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Contacttype'),
            '/view/contacttypes',
            '',
          ),
          new DataInterfaceMenu(
            'devicegenerictypes',
            $translator->translatePlural('Generic type', 'Generic types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicegenerictype'),
            '/view/devicegenerictypes',
            '',
          ),
          new DataInterfaceMenu(
            'devicesensortypes',
            $translator->translatePlural('Sensor type', 'Sensor types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicesensortype'),
            '/view/devicesensortypes',
            '',
          ),
          new DataInterfaceMenu(
            'memorytypes',
            $translator->translatePlural('Memory type', 'Memory types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Memorytype'),
            '/view/memorytypes',
            '',
          ),
          new DataInterfaceMenu(
            'suppliertypes',
            $translator->translatePlural('Third party type', 'Third party types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Suppliertype'),
            '/view/suppliertypes',
            '',
          ),
          new DataInterfaceMenu(
            'interfacetypes',
            $translator->translatePlural(
              'Interface type (Hard drive...)',
              'Interface types (Hard drive...)',
              2
            ),
            'edit',
            $this->getRightForModel('\App\Models\Interfacetype'),
            '/view/interfacetypes',
            '',
          ),
          new DataInterfaceMenu(
            'devicecasetypes',
            $translator->translatePlural('Case type', 'Case types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicecasetype'),
            '/view/devicecasetypes',
            '',
          ),
          new DataInterfaceMenu(
            'phonepowersupplies',
            $translator->translatePlural('Phone power supply type', 'Phone power supply types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Phonepowersupply'),
            '/view/phonepowersupplies',
            '',
          ),
          new DataInterfaceMenu(
            'filesystems',
            $translator->translatePlural('File system', 'File systems', 2),
            'edit',
            $this->getRightForModel('\App\Models\Filesystem'),
            '/view/filesystems',
            '',
          ),
          new DataInterfaceMenu(
            'certificatetypes',
            $translator->translatePlural('Certificate type', 'Certificate types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Certificatetype'),
            '/view/certificatetypes',
            '',
          ),
          new DataInterfaceMenu(
            'budgettypes',
            $translator->translatePlural('Budget type', 'Budget types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Budgettype'),
            '/view/budgettypes',
            '',
          ),
          new DataInterfaceMenu(
            'devicesimcardtypes',
            $translator->translatePlural('Simcard type', 'Simcard types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicesimcardtype'),
            '/view/devicesimcardtypes',
            '',
          ),
          new DataInterfaceMenu(
            'linetypes',
            $translator->translatePlural('Line type', 'Line types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Linetype'),
            '/view/linetypes',
            '',
          ),
          new DataInterfaceMenu(
            'racktypes',
            $translator->translatePlural('Rack type', 'Rack types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Racktype'),
            '/view/racktypes',
            '',
          ),
          new DataInterfaceMenu(
            'pdutypes',
            $translator->translatePlural('PDU type', 'PDU types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Pdutype'),
            '/view/pdutypes',
            '',
          ),
          new DataInterfaceMenu(
            'passivedcequipmenttypes',
            $translator->translatePlural('Passive device type', 'Passive device types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Passivedcequipmenttype'),
            '/view/passivedcequipmenttypes',
            '',
          ),
          new DataInterfaceMenu(
            'clustertypes',
            $translator->translatePlural('Cluster type', 'Cluster types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Clustertype'),
            '/view/clustertypes',
            '',
          ),
          new DataInterfaceMenu(
            'computermodels',
            $translator->translatePlural('Computer model', 'Computer models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Computermodel'),
            '/view/computermodels',
            '',
          ),
          new DataInterfaceMenu(
            'networkequipmentmodels',
            $translator->translatePlural('Networking equipment model', 'Networking equipment models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Networkequipmentmodel'),
            '/view/networkequipmentmodels',
            '',
          ),
          new DataInterfaceMenu(
            'printermodels',
            $translator->translatePlural('Printer model', 'Printer models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Printermodel'),
            '/view/printermodels',
            '',
          ),
          new DataInterfaceMenu(
            'monitormodels',
            $translator->translatePlural('Monitor model', 'Monitor models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Monitormodel'),
            '/view/monitormodels',
            '',
          ),
          new DataInterfaceMenu(
            'peripheralmodels',
            $translator->translatePlural('Peripheral model', 'Peripheral models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Peripheralmodel'),
            '/view/peripheralmodels',
            '',
          ),
          new DataInterfaceMenu(
            'phonemodels',
            $translator->translatePlural('Phone model', 'Phone models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Phonemodel'),
            '/view/phonemodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicecasemodels',
            $translator->translatePlural('Device case model', 'Device case models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicecasemodel'),
            '/view/devicecasemodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicecontrolmodels',
            $translator->translatePlural('Device control model', 'Device control models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicecontrolmodel'),
            '/view/devicecontrolmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicedrivemodels',
            $translator->translatePlural('Device drive model', 'Device drive models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicedrivemodel'),
            '/view/devicedrivemodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicegenericmodels',
            $translator->translatePlural('Device generic model', 'Device generic models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicegenericmodel'),
            '/view/devicegenericmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicegraphiccardmodels',
            $translator->translatePlural('Device graphic card model', 'Device graphic card models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicegraphiccardmodel'),
            '/view/devicegraphiccardmodels',
            '',
          ),
          new DataInterfaceMenu(
            'deviceharddrivemodels',
            $translator->translatePlural('Device hard drive model', 'Device hard drive models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Deviceharddrivemodel'),
            '/view/deviceharddrivemodels',
            '',
          ),
          new DataInterfaceMenu(
            'memorymodels',
            $translator->translatePlural('Memory model', 'Memory models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Memorymodel'),
            '/view/memorymodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicemotherboardmodels',
            $translator->translatePlural('System board model', 'System board models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicemotherboardmodel'),
            '/view/devicemotherboardmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicenetworkcardmodels',
            $translator->translatePlural('Network card model', 'Network card models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicenetworkcardmodel'),
            '/view/devicenetworkcardmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicepcimodels',
            $translator->translatePlural('Other component model', 'Other component models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicepcimodel'),
            '/view/devicepcimodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicepowersupplymodels',
            $translator->translatePlural('Device power supply model', 'Device power supply models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicepowersupplymodel'),
            '/view/devicepowersupplymodels',
            '',
          ),
          new DataInterfaceMenu(
            'deviceprocessormodels',
            $translator->translatePlural('Device processor model', 'Device processor models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Deviceprocessormodel'),
            '/view/deviceprocessormodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicesoundcardmodels',
            $translator->translatePlural('Device sound card model', 'Device sound card models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicesoundcardmodel'),
            '/view/devicesoundcardmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicesensormodels',
            $translator->translatePlural('Device sensor model', 'Device sensor models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicesensormodel'),
            '/view/devicesensormodels',
            '',
          ),
          new DataInterfaceMenu(
            'rackmodels',
            $translator->translatePlural('Rack model', 'Rack models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Rackmodel'),
            '/view/rackmodels',
            '',
          ),
          new DataInterfaceMenu(
            'enclosuremodels',
            $translator->translatePlural('Enclosure model', 'Enclosure models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Enclosuremodel'),
            '/view/enclosuremodels',
            '',
          ),
          new DataInterfaceMenu(
            'pdumodels',
            $translator->translatePlural('PDU model', 'PDU models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Pdumodel'),
            '/view/pdumodels',
            '',
          ),
          new DataInterfaceMenu(
            'passivedcequipmentmodels',
            $translator->translatePlural('Passive device model', 'Passive device models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Passivedcequipmentmodel'),
            '/view/passivedcequipmentmodels',
            '',
          ),
          new DataInterfaceMenu(
            'virtualmachinetypes',
            $translator->translatePlural('Virtualization system', 'Virtualization systems', 2),
            'edit',
            $this->getRightForModel('\App\Models\Virtualmachinetype'),
            '/view/virtualmachinetypes',
            '',
          ),
          new DataInterfaceMenu(
            'virtualmachinesystems',
            $translator->translatePlural('Virtualization model', 'Virtualization models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Virtualmachinesystem'),
            '/view/virtualmachinesystems',
            '',
          ),
          new DataInterfaceMenu(
            'virtualmachinestates',
            $translator->translatePlural('State of the virtual machine', 'States of the virtual machine', 2),
            'edit',
            $this->getRightForModel('\App\Models\Virtualmachinestate'),
            '/view/virtualmachinestates',
            '',
          ),
          new DataInterfaceMenu(
            'documentcategories',
            $translator->translatePlural('Document heading', 'Document headings', 2),
            'edit',
            $this->getRightForModel('\App\Models\Documentcategory'),
            '/view/documentcategories',
            '',
          ),
          new DataInterfaceMenu(
            'documenttypes',
            $translator->translatePlural('Document type', 'Document types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Documenttype'),
            '/view/documenttypes',
            '',
          ),
          new DataInterfaceMenu(
            'businesscriticities',
            $translator->translatePlural('Business criticity', 'Business criticities', 2),
            'edit',
            $this->getRightForModel('\App\Models\Businesscriticity'),
            '/view/businesscriticities',
            '',
          ),
          new DataInterfaceMenu(
            'calendars',
            $translator->translatePlural('Calendar', 'Calendars', 2),
            'edit',
            $this->getRightForModel('\App\Models\Calendar'),
            '/view/calendars',
            '',
          ),
          new DataInterfaceMenu(
            'holidays',
            $translator->translatePlural('Close time', 'Close times', 2),
            'edit',
            $this->getRightForModel('\App\Models\Holiday'),
            '/view/holidays',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemversions',
            $translator->translatePlural(
              'Version of the operating system',
              'Versions of the operating systems',
              2
            ),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemversion'),
            '/view/operatingsystemversions',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemservicepacks',
            $translator->translatePlural('Service pack', 'Service packs', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemservicepack'),
            '/view/operatingsystemservicepacks',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemarchitectures',
            $translator->translatePlural(
              'Operating system architecture',
              'Operating system architectures',
              2
            ),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemarchitecture'),
            '/view/operatingsystemarchitectures',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemeditions',
            $translator->translatePlural('Edition', 'Editions', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemedition'),
            '/view/operatingsystemeditions',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemkernels',
            $translator->translatePlural('Kernel', 'Kernels', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemkernel'),
            '/view/operatingsystemkernels',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemkernelversions',
            $translator->translatePlural('Kernel version', 'Kernel versions', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemkernelversion'),
            '/view/operatingsystemkernelversions',
            '',
          ),
          new DataInterfaceMenu(
            'autoupdatesystems',
            $translator->translatePlural('Update Source', 'Update Sources', 2),
            'edit',
            $this->getRightForModel('\App\Models\Autoupdatesystem'),
            '/view/autoupdatesystems',
            '',
          ),
          new DataInterfaceMenu(
            'networkinterfaces',
            $translator->translatePlural('Network interface', 'Network interfaces', 2),
            'edit',
            $this->getRightForModel('\App\Models\Networkinterface'),
            '/view/networkinterfaces',
            '',
          ),
          new DataInterfaceMenu(
            'netpoints',
            $translator->translatePlural('Network outlet', 'Network outlets', 2),
            'edit',
            $this->getRightForModel('\App\Models\Netpoint'),
            '/view/netpoints',
            '',
          ),
          new DataInterfaceMenu(
            'networks',
            $translator->translatePlural('Network', 'Networks', 2),
            'edit',
            $this->getRightForModel('\App\Models\Network'),
            '/view/networks',
            '',
          ),
          new DataInterfaceMenu(
            'vlans',
            $translator->translatePlural('VLAN', 'VLANs', 2),
            'edit',
            $this->getRightForModel('\App\Models\Vlan'),
            '/view/vlans',
            '',
          ),
          new DataInterfaceMenu(
            'lineoperators',
            $translator->translatePlural('Line operator', 'Line operators', 2),
            'edit',
            $this->getRightForModel('\App\Models\Lineoperator'),
            '/view/lineoperators',
            '',
          ),
          new DataInterfaceMenu(
            'domaintypes',
            $translator->translatePlural('Domain type', 'Domain types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Domaintype'),
            '/view/domaintypes',
            '',
          ),
          new DataInterfaceMenu(
            'domainrelations',
            $translator->translatePlural('Domain relation', 'Domains relations', 2),
            'edit',
            $this->getRightForModel('\App\Models\Domainrelation'),
            '/view/domainrelations',
            '',
          ),
          new DataInterfaceMenu(
            'domainrecordtypes',
            $translator->translatePlural('Record type', 'Records types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Domainrecordtype'),
            '/view/domainrecordtypes',
            '',
          ),
          new DataInterfaceMenu(
            'ipnetworks',
            $translator->translatePlural('IP network', 'IP networks', 2),
            'edit',
            $this->getRightForModel('\App\Models\Ipnetwork'),
            '/view/ipnetworks',
            '',
          ),
          new DataInterfaceMenu(
            'fqdns',
            $translator->translatePlural('Internet domain', 'Internet domains', 2),
            'edit',
            $this->getRightForModel('\App\Models\Fqdn'),
            '/view/fqdns',
            '',
          ),
          new DataInterfaceMenu(
            'wifinetworks',
            $translator->translatePlural('Wifi network', 'Wifi networks', 2),
            'edit',
            $this->getRightForModel('\App\Models\Wifinetwork'),
            '/view/wifinetworks',
            '',
          ),
          new DataInterfaceMenu(
            'networknames',
            $translator->translatePlural('Network name', 'Network names', 2),
            'edit',
            $this->getRightForModel('\App\Models\Networkname'),
            '/view/networknames',
            '',
          ),
          new DataInterfaceMenu(
            'softwarecategories',
            $translator->translatePlural('Software category', 'Software categories', 2),
            'edit',
            $this->getRightForModel('\App\Models\Softwarecategory'),
            '/view/softwarecategories',
            '',
          ),
          new DataInterfaceMenu(
            'usertitles',
            $translator->translatePlural('User title', 'Users titles', 2),
            'edit',
            $this->getRightForModel('\App\Models\Usertitle'),
            '/view/usertitles',
            '',
          ),
          new DataInterfaceMenu(
            'usercategories',
            $translator->translatePlural('User category', 'User categories', 2),
            'edit',
            $this->getRightForModel('\App\Models\Usercategory'),
            '/view/usercategories',
            '',
          ),
          new DataInterfaceMenu(
            'rulerightparameters',
            $translator->translatePlural('LDAP criterion', 'LDAP criteria', 2),
            'edit',
            $this->getRightForModel('\App\Models\Rulerightparameter'),
            '/view/rulerightparameters',
            '',
          ),
          new DataInterfaceMenu(
            'fieldblacklists',
            $translator->translatePlural(
              'Ignored value for the unicity',
              'Ignored values for the unicity',
              2
            ),
            'edit',
            $this->getRightForModel('\App\Models\Fieldblacklist'),
            '/view/fieldblacklists',
            '',
          ),
          new DataInterfaceMenu(
            'ssovariables',
            $translator->translatePlural(
              'Field storage of the login in the HTTP request',
              'Fields storage of the login in the HTTP request',
              2
            ),
            'edit',
            $this->getRightForModel('\App\Models\Ssovariable'),
            '/view/ssovariables',
            '',
          ),
          new DataInterfaceMenu(
            'plugs',
            $translator->translatePlural('Plug', 'Plugs', 2),
            'edit',
            $this->getRightForModel('\App\Models\Plug'),
            '/view/plugs',
            '',
          ),
          new DataInterfaceMenu(
            'appliancetypes',
            $translator->translatePlural('Appliance type', 'Appliance types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Appliancetype'),
            '/view/appliancetypes',
            '',
          ),
          new DataInterfaceMenu(
            'applianceenvironments',
            $translator->translatePlural('Appliance environment', 'Appliance environments', 2),
            'edit',
            $this->getRightForModel('\App\Models\Applianceenvironment'),
            '/view/applianceenvironments',
            '',
          ),
        ],
      ],
    ];
  }

  private function loadRights(): void
  {
    $profile = \App\Models\Profile::where('id', $GLOBALS['profile_id'])->first();
    if (!is_null($profile))
    {
      $dbRights = \App\Models\Profileright::where('profile_id', $profile->id)->get();
      foreach ($dbRights as $dbRight)
      {
        if (($dbRight->read || $dbRight->readmyitems || $dbRight->readmygroupitems) && !is_null($dbRight->model))
        {
          $this->setRightForModel($dbRight->model);
        }
      }
    }
  }

  private function setRightForModel(string $model): void
  {
    $this->rights['\\' . $model] = true;
  }

  private function getRightForModel(string $modelName): bool
  {
    if (isset($this->rights[$modelName]))
    {
      return true;
    }
    return false;
  }

  /**
   * @param array<mixed> $menu
   *
   * @return array<mixed>
   */
  private function cleanMenuByDisplay(array $menu): array
  {
    $newMenu = [];
    foreach ($menu as $item)
    {
      $submenu = [];
      foreach ($item['sub'] as $key => $subitem)
      {
        if ($subitem->display)
        {
          $submenu[] = $subitem;
        }
      }

      if (count($submenu) > 0)
      {
        $catMenu = [
          'name'  => $item['name'],
          'title' => $item['title'],
          'icon'  => $item['icon'],
          'sub'   => $submenu,
        ];
        $newMenu[] = $catMenu;
      }
    }
    return $newMenu;
  }
}
