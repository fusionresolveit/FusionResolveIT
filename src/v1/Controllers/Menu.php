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
    $uri = $request->getUri();

    return [
      [
        'name'  => 'hardwareinventory',
        'title' => pgettext('menu', 'ITAM - Hardware inventory'),
        'icon'  => 'laptop house',
        'sub'   => [
          new DataInterfaceMenu(
            'datacenters',
            npgettext('global', 'Data center', 'Data centers', 2),
            'warehouse',
            $this->getRightForModel('\App\Models\Datacenter'),
            '/view/datacenters',
            pgettext('menu', 'Dedicated space within a building'),
          ),
          new DataInterfaceMenu(
            'computers',
            npgettext('global', 'Computer', 'Computers', 2),
            'laptop',
            $this->getRightForModel('\App\Models\Computer'),
            '/view/computers',
            pgettext('menu', 'Computers, servers, laptop...'),
          ),
          new DataInterfaceMenu(
            'monitors',
            npgettext('inventory device', 'Monitor', 'Monitors', 2),
            'desktop',
            $this->getRightForModel('\App\Models\Monitor'),
            '/view/monitors',
            pgettext('menu', 'Display device for computers')
          ),
          new DataInterfaceMenu(
            'networkequipments',
            npgettext('global', 'Network device', 'Network devices', 2),
            'network wired',
            $this->getRightForModel('\App\Models\Networkequipment'),
            '/view/networkequipments',
            pgettext('menu', 'Device for communication between devices on network'),
          ),
          new DataInterfaceMenu(
            'peripherals',
            npgettext('global', 'Peripheral', 'Peripherals', 2),
            'usb',
            $this->getRightForModel('\App\Models\Peripheral'),
            '/view/peripherals',
            pgettext('menu', 'Peripherals like webcam, keyboard, mouse...'),
          ),
          new DataInterfaceMenu(
            'printers',
            npgettext('global', 'Printer', 'Printers', 2),
            'print',
            $this->getRightForModel('\App\Models\Printer'),
            '/view/printers',
            pgettext('menu', 'Local printer, photocopier, QRcode printer...'),
          ),
          new DataInterfaceMenu(
            'phones',
            npgettext('global', 'Phone', 'Phones', 2),
            'phone',
            $this->getRightForModel('\App\Models\Phone'),
            '/view/phones',
            pgettext('menu', 'Phones, smartphones'),
          ),
          new DataInterfaceMenu(
            'cartridges',
            npgettext('global', 'Cartridge', 'Cartridges', 2),
            'fill drip',
            $this->getRightForModel('\App\Models\Cartridgeitem'),
            '/view/cartridgeitems',
            pgettext('menu', 'Cartriges for printers'),
          ),
          new DataInterfaceMenu(
            'consumableitems',
            npgettext('global', 'Consumable', 'Consumables', 2),
            'box open',
            $this->getRightForModel('\App\Models\Consumableitem'),
            '/view/consumableitems',
            pgettext('menu', 'Consumable like USB keys, headphones...'),
          ),
          new DataInterfaceMenu(
            'racks',
            npgettext('global', 'Rack', 'Racks', 2),
            'server',
            $this->getRightForModel('\App\Models\Rack'),
            '/view/racks',
            '',
          ),
          new DataInterfaceMenu(
            'enclosures',
            npgettext('global', 'Enclosure', 'Enclosures', 2),
            'th',
            $this->getRightForModel('\App\Models\Enclosure'),
            '/view/enclosures',
            '',
          ),
          new DataInterfaceMenu(
            'pdus',
            npgettext('global', 'PDU', 'PDUs', 2),
            'plug',
            $this->getRightForModel('\App\Models\Pdu'),
            '/view/pdus',
            '',
          ),
          new DataInterfaceMenu(
            'passivedcequipments',
            npgettext('global', 'Passive device', 'Passive devices', 2),
            'th list',
            $this->getRightForModel('\App\Models\Passivedcequipment'),
            '/view/passivedcequipments',
            pgettext('menu', 'cables, patch panels...'),
          ),
          new DataInterfaceMenu(
            'simcards',
            npgettext('global', 'SIM card', 'SIM cards', 2),
            'sim card',
            $this->getRightForModel('\App\Models\ItemDevicesimcard'),
            '/view/itemdevicesimcards',
            '',
          ),
        ],
      ],
      [
        'name'  => 'softwareinventiry',
        'title' => pgettext('menu', 'ITAM - Software inventory'),
        'icon'  => 'archive',
        'sub'   => [
          new DataInterfaceMenu(
            'software',
            npgettext('global', 'Software', 'Software', 2),
            'software',
            $this->getRightForModel('\App\Models\Software'),
            '/view/softwares',
            pgettext('menu', 'List of software, version, where installed...'),
          ),
          new DataInterfaceMenu(
            'openratingsystems',
            npgettext('inventory device', 'Operating System', 'Operating Systems', 2),
            'operatingsystem',
            $this->getRightForModel('\App\Models\Operatingsystem'),
            '/view/operatingsystems',
            '',
          ),
          new DataInterfaceMenu(
            'firmware',
            npgettext('global', 'Firmware', 'Firmware', 2),
            'rom',
            $this->getRightForModel('\App\Models\Firmware'),
            '/view/devices/firmware',
            '',
          ),
          new DataInterfaceMenu(
            'appliances',
            npgettext('global', 'Appliance', 'Appliances', 2),
            'cubes',
            $this->getRightForModel('\App\Models\Appliance'),
            '/view/appliances',
            '',
          ),
          new DataInterfaceMenu(
            'clusters',
            npgettext('global', 'Cluster', 'Clusters', 2),
            'project diagram',
            $this->getRightForModel('\App\Models\Cluster'),
            '/view/clusters',
            '',
          ),
        ],
      ],
      [
        'name'  => 'contractcost',
        'title' => pgettext('menu', 'ITAM - Contracts & cost'),
        'icon'  => 'file signature',
        'sub'   => [
          new DataInterfaceMenu(
            'softwarelicenses',
            npgettext('global', 'License', 'Licenses', 2),
            'key',
            $this->getRightForModel('\App\Models\Softwarelicense'),
            '/view/softwarelicenses',
            '',
          ),
          new DataInterfaceMenu(
            'budgets',
            npgettext('global', 'Budget', 'Budgets', 2),
            'calculator',
            $this->getRightForModel('\App\Models\Budget'),
            '/view/budgets',
            '',
          ),
          new DataInterfaceMenu(
            'contracts',
            npgettext('global', 'Contract', 'Contracts', 2),
            'file signature',
            $this->getRightForModel('\App\Models\Contract'),
            '/view/contracts',
            '',
          ),
          new DataInterfaceMenu(
            'lines',
            npgettext('global', 'Line', 'Lines', 2),
            'phone',
            $this->getRightForModel('\App\Models\Line'),
            '/view/lines',
            '',
          ),
          new DataInterfaceMenu(
            'certificates',
            npgettext('global', 'Certificate', 'Certificates', 2),
            'certificate',
            $this->getRightForModel('\App\Models\Certificate'),
            '/view/certificates',
            '',
          ),
          new DataInterfaceMenu(
            'domains',
            npgettext('global', 'Domain', 'Domains', 2),
            'globe americas',
            $this->getRightForModel('\App\Models\Domain'),
            '/view/domains',
            '',
          ),
          new DataInterfaceMenu(
            'suppliers',
            npgettext('global', 'Supplier', 'Suppliers', 2),
            'dolly',
            $this->getRightForModel('\App\Models\Supplier'),
            '/view/suppliers',
            '',
          ),
          new DataInterfaceMenu(
            'contacts',
            npgettext('global', 'Contact', 'Contacts', 2),
            'user tie',
            $this->getRightForModel('\App\Models\Contact'),
            '/view/contacts',
            '',
          ),
        ],
      ],
      [
        'name'  => 'components',
        'title' => pgettext('menu', 'ITAM - Components'),
        'icon'  => 'microchip',
        'sub'   => [
          new DataInterfaceMenu(
            'devicepowersupplies',
            npgettext('global', 'Power supply', 'Power supplies', 2),
            'power-supply-unit',
            $this->getRightForModel('\App\Models\Devicepowersupply'),
            '/view/devices/devicepowersupplies',
            '',
          ),
          new DataInterfaceMenu(
            'devicebatteries',
            npgettext('global', 'Battery', 'Batteries', 2),
            'battery half',
            $this->getRightForModel('\App\Models\Devicebattery'),
            '/view/devices/devicebatteries',
            '',
          ),
          new DataInterfaceMenu(
            'devicecases',
            npgettext('global', 'Case', 'Cases', 2),
            'case',
            $this->getRightForModel('\App\Models\Devicecase'),
            '/view/devices/devicecases',
            '',
          ),
          new DataInterfaceMenu(
            'devicesensors',
            npgettext('global', 'Sensor', 'Sensors', 2),
            'sensor',
            $this->getRightForModel('\App\Models\Devicesensor'),
            '/view/devices/devicesensors',
            '',
          ),
          new DataInterfaceMenu(
            'devicesimcards',
            npgettext('global', 'SIM card', 'SIM cards', 2),
            'sim card',
            $this->getRightForModel('\App\Models\Devicesimcard'),
            '/view/devices/devicesimcards',
            '',
          ),
          new DataInterfaceMenu(
            'devicegraphiccards',
            npgettext('global', 'Graphics card', 'Graphics cards', 2),
            'graphiccard',
            $this->getRightForModel('\App\Models\Devicegraphiccard'),
            '/view/devices/devicegraphiccards',
            '',
          ),
          new DataInterfaceMenu(
            'devicemotherboards',
            npgettext('global', 'System board', 'System boards', 2),
            'motherboard',
            $this->getRightForModel('\App\Models\Devicemotherboard'),
            '/view/devices/devicemotherboards',
            '',
          ),
          new DataInterfaceMenu(
            'devicenetworkcards',
            npgettext('global', 'Network card', 'Network cards', 2),
            'networkcard',
            $this->getRightForModel('\App\Models\Devicenetworkcard'),
            '/view/devices/devicenetworkcards',
            '',
          ),
          new DataInterfaceMenu(
            'devicesoundcards',
            npgettext('global', 'Sound card', 'Sound cards', 2),
            'volume down',
            $this->getRightForModel('\App\Models\Devicesoundcard'),
            '/view/devices/devicesoundcards',
            '',
          ),
          new DataInterfaceMenu(
            'devicegenerics',
            npgettext('global', 'Generic device', 'Generic devices', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicegeneric'),
            '/view/devices/devicegenerics',
            '',
          ),
          new DataInterfaceMenu(
            'devicecontrols',
            npgettext('global', 'Controller', 'Controllers', 2),
            'microchip',
            $this->getRightForModel('\App\Models\Devicecontrol'),
            '/view/devices/devicecontrols',
            '',
          ),
          new DataInterfaceMenu(
            'sorages',
            npgettext('global', 'Storage', 'Storages', 2),
            'ssd',
            $this->getRightForModel('\App\Models\Storage'),
            '/view/devices/storages',
            '',
          ),
          new DataInterfaceMenu(
            'devicedrives',
            npgettext('global', 'Drive', 'Drives', 2),
            'hdd',
            $this->getRightForModel('\App\Models\Devicedrive'),
            '/view/devices/devicedrives',
            '',
          ),
          new DataInterfaceMenu(
            'memorymodules',
            npgettext('global', 'Memory', 'Memory', 2),
            'memory',
            $this->getRightForModel('\App\Models\Memorymodule'),
            '/view/devices/memorymodules',
            '',
          ),
          new DataInterfaceMenu(
            'deviceprocessors',
            npgettext('global', 'Processor', 'Processors', 2),
            'processor',
            $this->getRightForModel('\App\Models\Deviceprocessor'),
            '/view/devices/deviceprocessors',
            '',
          ),
          new DataInterfaceMenu(
            'devicepcis',
            npgettext('global', 'PCI device', 'PCI devices', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicepci'),
            '/view/devices/devicepcis',
            '',
          ),
        ],
      ],
      [
        'name'  => 'assistance',
        'title' => pgettext('menu', 'ITSM'),
        'icon'  => 'hands helping',
        'sub'   => [
          new DataInterfaceMenu(
            'tickets',
            npgettext('ticket', 'Ticket', 'Tickets', 2),
            'hands helping',
            $this->getRightForModel('\App\Models\Ticket'),
            '/view/tickets',
            '',
          ),
          new DataInterfaceMenu(
            'problems',
            npgettext('problem', 'Problem', 'Problems', 2),
            'drafting compass',
            $this->getRightForModel('\App\Models\Problem'),
            '/view/problems',
            '',
          ),
          new DataInterfaceMenu(
            'changes',
            npgettext('change', 'Change', 'Changes', 2),
            'paint roller',
            $this->getRightForModel('\App\Models\Change'),
            '/view/changes',
            '',
          ),
          new DataInterfaceMenu(
            'ticketrecurrents',
            npgettext('global', 'Recurrent ticket', 'Recurrent tickets', 2),
            'stopwatch',
            $this->getRightForModel('\App\Models\Ticketrecurrent'),
            '/view/ticketrecurrents',
            '',
          ),
          new DataInterfaceMenu(
            'mailcollectors',
            npgettext('global', 'Receiver', 'Receivers', 2),
            'edit',
            $this->getRightForModel('\App\Models\Mailcollector'),
            '/view/mailcollectors',
            '',
          ),
          new DataInterfaceMenu(
            'rulestickets',
            npgettext('global', 'Business rule for tickets', 'Business rules for tickets', 2),
            'magic',
            $this->getRightForModel('\App\Models\Rules\Ticket'),
            '/view/rules/tickets',
            '',
          ),
          new DataInterfaceMenu(
            'slms',
            npgettext('global', 'Service level', 'Service levels', 2),
            'edit',
            $this->getRightForModel('\App\Models\Slm'),
            '/view/slms',
            '',
          ),
        ],
      ],
      [
        'name'  => 'form',
        'title' => npgettext('global', 'Form', 'Forms', 2),
        'icon'  => 'cubes',
        'sub'   => [
          new DataInterfaceMenu(
            'forms',
            npgettext('global', 'Form', 'Forms', 2),
            'hands helping',
            $this->getRightForModel('\App\Models\Forms\Form'),
            '/view/forms',
            '',
          ),
          new DataInterfaceMenu(
            'formsections',
            npgettext('global', 'Section', 'Sections', 2),
            'exclamation triangle',
            $this->getRightForModel('\App\Models\Forms\Section'),
            '/view/sections',
            '',
          ),
          new DataInterfaceMenu(
            'formquestions',
            npgettext('global', 'Question', 'Questions', 2),
            'clipboard check',
            $this->getRightForModel('\App\Models\Forms\Question'),
            '/view/questions',
            '',
          ),
          // [
          //   'name'  => 'Answer', 'Answers', 2,
          //   'endpoint' => '/view/answers',
          //   'icon'  => 'clipboard check',
          //   'display' => $this->getRightForModel('\App\Models\Forms\Answer'),
          // ],
        ],
      ],
      [
        'name'  => 'userdata',
        'title' => pgettext('menu', 'User data'),
        'icon'  => 'user',
        'sub'   => [
          new DataInterfaceMenu(
            'users',
            npgettext('global', 'User', 'Users', 2),
            'user',
            $this->getRightForModel('\App\Models\User'),
            '/view/users',
            '',
          ),
          new DataInterfaceMenu(
            'groups',
            npgettext('global', 'Group', 'Groups', 2),
            'users',
            $this->getRightForModel('\App\Models\Group'),
            '/view/groups',
            '',
          ),
          new DataInterfaceMenu(
            'profiles',
            npgettext('global', 'Profile', 'Profiles', 2),
            'user check',
            $this->getRightForModel('\App\Models\Profile'),
            '/view/profiles',
            '',
          ),
          new DataInterfaceMenu(
            'authssos',
            pgettext('global', 'Authentication SSO'),
            'id card alternate',
            $this->getRightForModel('\App\Models\Authsso'),
            '/view/authssos',
            '',
          ),
          new DataInterfaceMenu(
            'authldaps',
            pgettext('global', 'Provisionning LDAP'),
            'address book outline',
            $this->getRightForModel('\App\Models\Authldap'),
            '/view/authldaps',
            '',
          ),
          new DataInterfaceMenu(
            'rulesusers',
            pgettext('global', 'Rules for users'),
            'magic',
            $this->getRightForModel('\App\Models\Rules\User'),
            '/view/rules/users',
            '',
          ),
          new DataInterfaceMenu(
            'savedsearches',
            npgettext('global', 'Saved search', 'Saved searches', 2),
            'bookmark',
            $this->getRightForModel('\App\Models\Savedsearch'),
            '/view/savedsearchs',
            '',
          ),
          new DataInterfaceMenu(
            'audits',
            npgettext('global', 'Audit', 'Audits', 2),
            'scroll',
            $this->getRightForModel('\App\Models\Audit'),
            '/view/audits',
            '',
          ),
        ],
      ],
      [
        'name'  => 'alerting',
        'title' => pgettext('menu', 'Alerting'),
        'icon'  => 'bullhorn',
        'sub'   => [
          new DataInterfaceMenu(
            'queuednotifications',
            npgettext('global', 'Notifications queue', 'Notifications queues', 2),
            'list alt',
            $this->getRightForModel('\App\Models\Queuednotification'),
            '/view/queuednotifications',
            '',
          ),
          new DataInterfaceMenu(
            'notificationtemplates',
            npgettext('global', 'Notification', 'Notifications', 2) . ' - ' .
              npgettext('notification', 'Notification template', 'Notification templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Notificationtemplate'),
            '/view/notificationtemplates',
            '',
          ),
          new DataInterfaceMenu(
            'notifications',
            npgettext('global', 'Notification', 'Notifications', 2) . ' - ' .
              npgettext('global', 'Notification', 'Notifications', 2),
            'edit',
            $this->getRightForModel('\App\Models\Notification'),
            '/view/notifications',
            '',
          ),
          new DataInterfaceMenu(
            'alerts',
            npgettext('global', 'Alert', 'Alerts', 2),
            'bell',
            $this->getRightForModel('\App\Models\Alert'),
            '/view/alerts',
            '',
          ),
          new DataInterfaceMenu(
            'rssfeeds',
            npgettext('global', 'RSS feed', 'RSS feed', 2),
            'rss',
            $this->getRightForModel('\App\Models\Rssfeed'),
            '/view/rssfeeds',
            '',
          ),
        ],
      ],
      [
        'name'  => 'system',
        'title' => pgettext('menu', 'System'),
        'icon'  => 'layer group',
        'sub'   => [
          new DataInterfaceMenu(
            'entities',
            npgettext('global', 'Entity', 'Entities', 2),
            'layer group',
            $this->getRightForModel('\App\Models\Entity'),
            '/view/entities',
            '',
          ),
          new DataInterfaceMenu(
            'crontasks',
            npgettext('global', 'Automatic action', 'Automatic actions', 2),
            'edit',
            $this->getRightForModel('\App\Models\Crontask'),
            '/view/crontasks',
            '',
          ),
          // [
          //   'name' => 'Rules',
          //   'endpoint' => '/view/rules',
          //   'icon' => 'book',
          // ],
        ],
      ],
      [
        'name'  => 'knowledgehub',
        'title' => pgettext('menu', 'Knowledge hub'), // or
        'icon'  => 'book',
        'sub'   => [
          new DataInterfaceMenu(
            'knowledgebasearticles',
            npgettext('global', 'Knowledge base article', 'Knowledge base articles', 2),
            'edit',
            $this->getRightForModel('\App\Models\Knowledgebasearticle'),
            '/view/knowledgebasearticles',
            '',
          ),
          new DataInterfaceMenu(
            'documents',
            npgettext('global', 'Document', 'Documents', 2),
            'file',
            $this->getRightForModel('\App\Models\Document'),
            '/view/documents',
            '',
          ),
          new DataInterfaceMenu(
            'projects',
            npgettext('global', 'Project', 'Projects', 2),
            'columns',
            $this->getRightForModel('\App\Models\Project'),
            '/view/projects',
            '',
          ),
          new DataInterfaceMenu(
            'reminders',
            npgettext('global', 'Note', 'Notes', 2),
            'sticky note',
            $this->getRightForModel('\App\Models\Reminder'),
            '/view/reminders',
            '',
          ),
          new DataInterfaceMenu(
            'fieldunicities',
            npgettext('global', 'Fields unicity', 'Fields unicity', 2),
            'edit',
            $this->getRightForModel('\App\Models\Fieldunicity'),
            '/view/fieldunicities',
            '',
          ),
          new DataInterfaceMenu(
            'links',
            npgettext('global', 'External link', 'External links', 2),
            'edit',
            $this->getRightForModel('\App\Models\Link'),
            '/view/links',
            '',
          ),
        ],
      ],
      [
        'name'  => 'dropdowns',
        'title' => npgettext('global', 'Simple item', 'Simple items', 2),
        'icon'  => 'list',
        'sub'   => [
          new DataInterfaceMenu(
            'locations',
            npgettext('global', 'Location', 'Locations', 2),
            'edit',
            $this->getRightForModel('\App\Models\Location'),
            '/view/locations',
            '',
          ),
          new DataInterfaceMenu(
            'states',
            npgettext('global', 'Status of items', 'Statuses of items', 2),
            'edit',
            $this->getRightForModel('\App\Models\State'),
            '/view/states',
            '',
          ),
          new DataInterfaceMenu(
            'manufacturers',
            npgettext('global', 'Manufacturer', 'Manufacturers', 2),
            'edit',
            $this->getRightForModel('\App\Models\Manufacturer'),
            '/view/manufacturers',
            '',
          ),
          new DataInterfaceMenu(
            'blacklists',
            npgettext('global', 'Blacklist', 'Blacklists', 2),
            'edit',
            $this->getRightForModel('\App\Models\Blacklist'),
            '/view/blacklists',
            '',
          ),
          new DataInterfaceMenu(
            'blacklistedmailcontents',
            npgettext('global', 'Blacklisted mail content', 'Blacklisted mail contents', 1),
            'edit',
            $this->getRightForModel('\App\Models\Blacklistedmailcontent'),
            '/view/blacklistedmailcontents',
            '',
          ),
          new DataInterfaceMenu(
            'categories',
            npgettext('global', 'Category', 'Categories', 2),
            'edit',
            $this->getRightForModel('\App\Models\Category'),
            '/view/categories',
            '',
          ),
          new DataInterfaceMenu(
            'tickettemplates',
            npgettext('global', 'Ticket template', 'Ticket templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Tickettemplate'),
            '/view/ticketemplates',
            '',
          ),
          new DataInterfaceMenu(
            'solutiontypes',
            npgettext('global', 'Solution type', 'Solution types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Solutiontype'),
            '/view/solutiontypes',
            '',
          ),
          new DataInterfaceMenu(
            'solutiontemplates',
            npgettext('global', 'Solution template', 'Solution templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Solutiontemplate'),
            '/view/solutiontemplates',
            '',
          ),
          new DataInterfaceMenu(
            'requesttypes',
            npgettext('global', 'Request source', 'Request sources', 2),
            'edit',
            $this->getRightForModel('\App\Models\Requesttype'),
            '/view/requesttypes',
            '',
          ),
          new DataInterfaceMenu(
            'followuptemplates',
            npgettext('global', 'Followup template', 'Followup templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Followuptemplate'),
            '/view/followuptemplates',
            '',
          ),
          new DataInterfaceMenu(
            'projectstates',
            npgettext('global', 'Project state', 'Project states', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projectstate'),
            '/view/projectstates',
            '',
          ),
          new DataInterfaceMenu(
            'projecttypes',
            npgettext('global', 'Project type', 'Project types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projecttype'),
            '/view/projecttypes',
            '',
          ),
          new DataInterfaceMenu(
            'projecttasks',
            npgettext('project', 'Project task', 'Project tasks', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projecttask'),
            '/view/projecttasks',
            '',
          ),
          new DataInterfaceMenu(
            'projecttasktypes',
            npgettext('global', 'Project task type', 'Project task types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projecttasktype'),
            '/view/projecttasktypes',
            '',
          ),
          new DataInterfaceMenu(
            'projecttasktemplates',
            npgettext('global', 'Project task template', 'Project task templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Projecttasktemplate'),
            '/view/projecttasktemplates',
            '',
          ),
          new DataInterfaceMenu(
            'planningeventcategories',
            npgettext('global', 'Event category', 'Event categories', 2),
            'edit',
            $this->getRightForModel('\App\Models\Planningeventcategory'),
            '/view/planningeventcategories',
            '',
          ),
          new DataInterfaceMenu(
            'planningexternaleventtemplates',
            npgettext('global', 'External events template', 'External events templates', 2),
            'edit',
            $this->getRightForModel('\App\Models\Planningexternaleventtemplate'),
            '/view/planningexternaleventtemplates',
            '',
          ),
          new DataInterfaceMenu(
            'computertypes',
            npgettext('global', 'Computer type', 'Computer types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Computertype'),
            '/view/computertypes',
            '',
          ),
          new DataInterfaceMenu(
            'networkequipmenttypes',
            npgettext('global', 'Networking equipment type', 'Networking equipment types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Networkequipmenttype'),
            '/view/networkequipmenttypes',
            '',
          ),
          new DataInterfaceMenu(
            'printertypes',
            npgettext('global', 'Printer type', 'Printer types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Printertype'),
            '/view/printertypes',
            '',
          ),
          new DataInterfaceMenu(
            'monitortypes',
            npgettext('global', 'Monitor type', 'Monitor types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Monitortype'),
            '/view/monitortypes',
            '',
          ),
          new DataInterfaceMenu(
            'peripheraltypes',
            npgettext('global', 'Peripheral type', 'Peripheral types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Peripheraltype'),
            '/view/peripheraltypes',
            '',
          ),
          new DataInterfaceMenu(
            'phonetypes',
            npgettext('global', 'Phone type', 'Phone types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Phonetype'),
            '/view/phonetypes',
            '',
          ),
          new DataInterfaceMenu(
            'softwarelicensetypes',
            npgettext('global', 'License type', 'License types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Softwarelicensetype'),
            '/view/softwarelicensetypes',
            '',
          ),
          new DataInterfaceMenu(
            'cartridgeitemtypes',
            npgettext('global', 'Cartridge type', 'Cartridge types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Cartridgeitemtype'),
            '/view/cartridgeitemtypes',
            '',
          ),
          new DataInterfaceMenu(
            'consumableitemtypes',
            npgettext('global', 'Consumable type', 'Consumable types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Consumableitemtype'),
            '/view/consumableitemtypes',
            '',
          ),
          new DataInterfaceMenu(
            'contracttypes',
            npgettext('contract', 'Contract type', 'Contract types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Contracttype'),
            '/view/contracttypes',
            '',
          ),
          new DataInterfaceMenu(
            'contacttypes',
            npgettext('global', 'Contact type', 'Contact types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Contacttype'),
            '/view/contacttypes',
            '',
          ),
          new DataInterfaceMenu(
            'devicegenerictypes',
            npgettext('global', 'Generic type', 'Generic types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicegenerictype'),
            '/view/devicegenerictypes',
            '',
          ),
          new DataInterfaceMenu(
            'devicesensortypes',
            npgettext('global', 'Sensor type', 'Sensor types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicesensortype'),
            '/view/devicesensortypes',
            '',
          ),
          new DataInterfaceMenu(
            'memorytypes',
            npgettext('global', 'Memory type', 'Memory types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Memorytype'),
            '/view/memorytypes',
            '',
          ),
          new DataInterfaceMenu(
            'suppliertypes',
            npgettext('supplier', 'Third party type', 'Third party types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Suppliertype'),
            '/view/suppliertypes',
            '',
          ),
          new DataInterfaceMenu(
            'interfacetypes',
            npgettext('global', 'Interface type (Hard drive...)', 'Interface types (Hard drive...)', 2),
            'edit',
            $this->getRightForModel('\App\Models\Interfacetype'),
            '/view/interfacetypes',
            '',
          ),
          new DataInterfaceMenu(
            'devicecasetypes',
            npgettext('global', 'Case type', 'Case types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicecasetype'),
            '/view/devicecasetypes',
            '',
          ),
          new DataInterfaceMenu(
            'phonepowersupplies',
            npgettext('global', 'Phone power supply type', 'Phone power supply types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Phonepowersupply'),
            '/view/phonepowersupplies',
            '',
          ),
          new DataInterfaceMenu(
            'filesystems',
            npgettext('global', 'File system', 'File systems', 2),
            'edit',
            $this->getRightForModel('\App\Models\Filesystem'),
            '/view/filesystems',
            '',
          ),
          new DataInterfaceMenu(
            'certificatetypes',
            npgettext('global', 'Certificate type', 'Certificate types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Certificatetype'),
            '/view/certificatetypes',
            '',
          ),
          new DataInterfaceMenu(
            'budgettypes',
            npgettext('global', 'Budget type', 'Budget types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Budgettype'),
            '/view/budgettypes',
            '',
          ),
          new DataInterfaceMenu(
            'devicesimcardtypes',
            npgettext('global', 'SIM card type', 'SIM card types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicesimcardtype'),
            '/view/devicesimcardtypes',
            '',
          ),
          new DataInterfaceMenu(
            'linetypes',
            npgettext('global', 'Line type', 'Line types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Linetype'),
            '/view/linetypes',
            '',
          ),
          new DataInterfaceMenu(
            'racktypes',
            npgettext('global', 'Rack type', 'Rack types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Racktype'),
            '/view/racktypes',
            '',
          ),
          new DataInterfaceMenu(
            'pdutypes',
            npgettext('global', 'PDU type', 'PDU types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Pdutype'),
            '/view/pdutypes',
            '',
          ),
          new DataInterfaceMenu(
            'passivedcequipmenttypes',
            npgettext('global', 'Passive device type', 'Passive device types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Passivedcequipmenttype'),
            '/view/passivedcequipmenttypes',
            '',
          ),
          new DataInterfaceMenu(
            'clustertypes',
            npgettext('global', 'Cluster type', 'Cluster types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Clustertype'),
            '/view/clustertypes',
            '',
          ),
          new DataInterfaceMenu(
            'computermodels',
            npgettext('global', 'Computer model', 'Computer models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Computermodel'),
            '/view/computermodels',
            '',
          ),
          new DataInterfaceMenu(
            'networkequipmentmodels',
            npgettext('global', 'Networking equipment model', 'Networking equipment models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Networkequipmentmodel'),
            '/view/networkequipmentmodels',
            '',
          ),
          new DataInterfaceMenu(
            'printermodels',
            npgettext('global', 'Printer model', 'Printer models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Printermodel'),
            '/view/printermodels',
            '',
          ),
          new DataInterfaceMenu(
            'monitormodels',
            npgettext('global', 'Monitor model', 'Monitor models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Monitormodel'),
            '/view/monitormodels',
            '',
          ),
          new DataInterfaceMenu(
            'peripheralmodels',
            npgettext('global', 'Peripheral model', 'Peripheral models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Peripheralmodel'),
            '/view/peripheralmodels',
            '',
          ),
          new DataInterfaceMenu(
            'phonemodels',
            npgettext('global', 'Phone model', 'Phone models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Phonemodel'),
            '/view/phonemodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicecasemodels',
            npgettext('global', 'Device case model', 'Device case models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicecasemodel'),
            '/view/devicecasemodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicecontrolmodels',
            npgettext('global', 'Device control model', 'Device control models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicecontrolmodel'),
            '/view/devicecontrolmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicedrivemodels',
            npgettext('global', 'Device drive model', 'Device drive models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicedrivemodel'),
            '/view/devicedrivemodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicegenericmodels',
            npgettext('global', 'Device generic model', 'Device generic models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicegenericmodel'),
            '/view/devicegenericmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicegraphiccardmodels',
            npgettext('global', 'Device graphic card model', 'Device graphic card models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicegraphiccardmodel'),
            '/view/devicegraphiccardmodels',
            '',
          ),
          new DataInterfaceMenu(
            'memorymodels',
            npgettext('global', 'Memory model', 'Memory models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Memorymodel'),
            '/view/memorymodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicemotherboardmodels',
            npgettext('global', 'System board model', 'System board models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicemotherboardmodel'),
            '/view/devicemotherboardmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicenetworkcardmodels',
            npgettext('global', 'Network card model', 'Network card models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicenetworkcardmodel'),
            '/view/devicenetworkcardmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicepcimodels',
            npgettext('global', 'Other component model', 'Other component models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicepcimodel'),
            '/view/devicepcimodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicepowersupplymodels',
            npgettext('global', 'Device power supply model', 'Device power supply models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicepowersupplymodel'),
            '/view/devicepowersupplymodels',
            '',
          ),
          new DataInterfaceMenu(
            'deviceprocessormodels',
            npgettext('global', 'Device processor model', 'Device processor models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Deviceprocessormodel'),
            '/view/deviceprocessormodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicesoundcardmodels',
            npgettext('global', 'Device sound card model', 'Device sound card models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicesoundcardmodel'),
            '/view/devicesoundcardmodels',
            '',
          ),
          new DataInterfaceMenu(
            'devicesensormodels',
            npgettext('global', 'Device sensor model', 'Device sensor models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Devicesensormodel'),
            '/view/devicesensormodels',
            '',
          ),
          new DataInterfaceMenu(
            'rackmodels',
            npgettext('global', 'Rack model', 'Rack models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Rackmodel'),
            '/view/rackmodels',
            '',
          ),
          new DataInterfaceMenu(
            'enclosuremodels',
            npgettext('global', 'Enclosure model', 'Enclosure models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Enclosuremodel'),
            '/view/enclosuremodels',
            '',
          ),
          new DataInterfaceMenu(
            'pdumodels',
            npgettext('global', 'PDU model', 'PDU models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Pdumodel'),
            '/view/pdumodels',
            '',
          ),
          new DataInterfaceMenu(
            'passivedcequipmentmodels',
            npgettext('global', 'Passive device model', 'Passive device models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Passivedcequipmentmodel'),
            '/view/passivedcequipmentmodels',
            '',
          ),
          new DataInterfaceMenu(
            'virtualmachinetypes',
            npgettext('global', 'Virtualization system', 'Virtualization systems', 2),
            'edit',
            $this->getRightForModel('\App\Models\Virtualmachinetype'),
            '/view/virtualmachinetypes',
            '',
          ),
          new DataInterfaceMenu(
            'virtualmachinesystems',
            npgettext('global', 'Virtualization model', 'Virtualization models', 2),
            'edit',
            $this->getRightForModel('\App\Models\Virtualmachinesystem'),
            '/view/virtualmachinesystems',
            '',
          ),
          new DataInterfaceMenu(
            'virtualmachinestates',
            npgettext('global', 'State of the virtual machine', 'States of the virtual machine', 2),
            'edit',
            $this->getRightForModel('\App\Models\Virtualmachinestate'),
            '/view/virtualmachinestates',
            '',
          ),
          new DataInterfaceMenu(
            'documentcategories',
            npgettext('global', 'Document heading', 'Document headings', 2),
            'edit',
            $this->getRightForModel('\App\Models\Documentcategory'),
            '/view/documentcategories',
            '',
          ),
          new DataInterfaceMenu(
            'documenttypes',
            npgettext('global', 'Document type', 'Document types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Documenttype'),
            '/view/documenttypes',
            '',
          ),
          new DataInterfaceMenu(
            'businesscriticities',
            npgettext('global', 'Business criticity', 'Business criticities', 2),
            'edit',
            $this->getRightForModel('\App\Models\Businesscriticity'),
            '/view/businesscriticities',
            '',
          ),
          new DataInterfaceMenu(
            'calendars',
            npgettext('global', 'Calendar', 'Calendars', 2),
            'edit',
            $this->getRightForModel('\App\Models\Calendar'),
            '/view/calendars',
            '',
          ),
          new DataInterfaceMenu(
            'holidays',
            npgettext('global', 'Close time', 'Close times', 2),
            'edit',
            $this->getRightForModel('\App\Models\Holiday'),
            '/view/holidays',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemversions',
            npgettext('global', 'Version of the operating system', 'Versions of the operating system', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemversion'),
            '/view/operatingsystemversions',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemservicepacks',
            npgettext('global', 'Service pack', 'Service packs', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemservicepack'),
            '/view/operatingsystemservicepacks',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemarchitectures',
            npgettext('global', 'Operating system architecture', 'Operating system architectures', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemarchitecture'),
            '/view/operatingsystemarchitectures',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemeditions',
            npgettext('global', 'Edition', 'Editions', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemedition'),
            '/view/operatingsystemeditions',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemkernels',
            npgettext('global', 'Kernel', 'Kernels', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemkernel'),
            '/view/operatingsystemkernels',
            '',
          ),
          new DataInterfaceMenu(
            'operatingsystemkernelversions',
            npgettext('global', 'Kernel version', 'Kernel versions', 2),
            'edit',
            $this->getRightForModel('\App\Models\Operatingsystemkernelversion'),
            '/view/operatingsystemkernelversions',
            '',
          ),
          new DataInterfaceMenu(
            'autoupdatesystems',
            npgettext('inventory device', 'Update Source', 'Update Sources', 2),
            'edit',
            $this->getRightForModel('\App\Models\Autoupdatesystem'),
            '/view/autoupdatesystems',
            '',
          ),
          new DataInterfaceMenu(
            'networkinterfaces',
            npgettext('global', 'Network interface', 'Network interfaces', 2),
            'edit',
            $this->getRightForModel('\App\Models\Networkinterface'),
            '/view/networkinterfaces',
            '',
          ),
          new DataInterfaceMenu(
            'netpoints',
            npgettext('inventory device', 'Network outlet', 'Network outlets', 2),
            'edit',
            $this->getRightForModel('\App\Models\Netpoint'),
            '/view/netpoints',
            '',
          ),
          new DataInterfaceMenu(
            'networks',
            npgettext('inventory device', 'Network', 'Networks', 2),
            'edit',
            $this->getRightForModel('\App\Models\Network'),
            '/view/networks',
            '',
          ),
          new DataInterfaceMenu(
            'vlans',
            npgettext('global', 'VLAN', 'VLANs', 2),
            'edit',
            $this->getRightForModel('\App\Models\Vlan'),
            '/view/vlans',
            '',
          ),
          new DataInterfaceMenu(
            'lineoperators',
            npgettext('global', 'Line operator', 'Line operators', 2),
            'edit',
            $this->getRightForModel('\App\Models\Lineoperator'),
            '/view/lineoperators',
            '',
          ),
          new DataInterfaceMenu(
            'domaintypes',
            npgettext('global', 'Domain type', 'Domain types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Domaintype'),
            '/view/domaintypes',
            '',
          ),
          new DataInterfaceMenu(
            'domainrelations',
            npgettext('global', 'Domain relation', 'Domain relations', 2),
            'edit',
            $this->getRightForModel('\App\Models\Domainrelation'),
            '/view/domainrelations',
            '',
          ),
          new DataInterfaceMenu(
            'domainrecordtypes',
            npgettext('global', 'Record type', 'Record types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Domainrecordtype'),
            '/view/domainrecordtypes',
            '',
          ),
          new DataInterfaceMenu(
            'ipnetworks',
            npgettext('global', 'IP network', 'IP networks', 2),
            'edit',
            $this->getRightForModel('\App\Models\Ipnetwork'),
            '/view/ipnetworks',
            '',
          ),
          new DataInterfaceMenu(
            'fqdns',
            npgettext('global', 'Internet domain', 'Internet domains', 2),
            'edit',
            $this->getRightForModel('\App\Models\Fqdn'),
            '/view/fqdns',
            '',
          ),
          new DataInterfaceMenu(
            'wifinetworks',
            npgettext('global', 'Wifi network', 'Wifi networks', 2),
            'edit',
            $this->getRightForModel('\App\Models\Wifinetwork'),
            '/view/wifinetworks',
            '',
          ),
          new DataInterfaceMenu(
            'networknames',
            npgettext('global', 'Network name', 'Network names', 2),
            'edit',
            $this->getRightForModel('\App\Models\Networkname'),
            '/view/networknames',
            '',
          ),
          new DataInterfaceMenu(
            'softwarecategories',
            npgettext('global', 'Software category', 'Software categories', 2),
            'edit',
            $this->getRightForModel('\App\Models\Softwarecategory'),
            '/view/softwarecategories',
            '',
          ),
          new DataInterfaceMenu(
            'usertitles',
            npgettext('global', 'User title', 'User titles', 2),
            'edit',
            $this->getRightForModel('\App\Models\Usertitle'),
            '/view/usertitles',
            '',
          ),
          new DataInterfaceMenu(
            'usercategories',
            npgettext('global', 'User category', 'User categories', 2),
            'edit',
            $this->getRightForModel('\App\Models\Usercategory'),
            '/view/usercategories',
            '',
          ),
          new DataInterfaceMenu(
            'rulerightparameters',
            npgettext('global', 'LDAP criterion', 'LDAP criteria', 2),
            'edit',
            $this->getRightForModel('\App\Models\Rulerightparameter'),
            '/view/rulerightparameters',
            '',
          ),
          new DataInterfaceMenu(
            'fieldblacklists',
            npgettext('global', 'Ignored value for the unicity', 'Ignored values for the unicity', 2),
            'edit',
            $this->getRightForModel('\App\Models\Fieldblacklist'),
            '/view/fieldblacklists',
            '',
          ),
          new DataInterfaceMenu(
            'ssovariables',
            npgettext(
              'global',
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
            npgettext('global', 'Plug', 'Plugs', 2),
            'edit',
            $this->getRightForModel('\App\Models\Plug'),
            '/view/plugs',
            '',
          ),
          new DataInterfaceMenu(
            'appliancetypes',
            npgettext('global', 'Appliance type', 'Appliance types', 2),
            'edit',
            $this->getRightForModel('\App\Models\Appliancetype'),
            '/view/appliancetypes',
            '',
          ),
          new DataInterfaceMenu(
            'applianceenvironments',
            npgettext('global', 'Appliance environment', 'Appliance environments', 2),
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
