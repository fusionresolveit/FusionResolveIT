<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\Subs\Associateditem;
use App\Traits\Subs\Document as SubsDocument;
use App\Traits\Subs\History;
use App\Traits\Subs\Note;

final class Document extends Common
{
  // Display
  use ShowItem;
  use ShowAll;

  // Sub
  use Note;
  use SubsDocument;
  use History;
  use Associateditem;

  protected $model = \App\Models\Document::class;
  protected $rootUrl2 = '/documents/';

  protected function instanciateModel(): \App\Models\Document
  {
    return new \App\Models\Document();
  }

  /**
   * @return array{
   *          'associatedAppliances': \App\Models\Appliance,
   *          'associatedBudgets': \App\Models\Budget,
   *          'associatedCartridgeitems': \App\Models\Cartridgeitem,
   *          'associatedCertificates': \App\Models\Certificate,
   *          'associatedClusters': \App\Models\Cluster,
   *          'associatedComputers': \App\Models\Computer,
   *          'associatedConsumableitems': \App\Models\Consumableitem,
   *          'associatedContacts': \App\Models\Contact,
   *          'associatedContracts': \App\Models\Contract,
   *          'associatedDcrooms': \App\Models\Dcroom,
   *          'associatedDevicebatteries': \App\Models\Devicebattery,
   *          'associatedDevicecases': \App\Models\Devicecase,
   *          'associatedDevicecontrols': \App\Models\Devicecontrol,
   *          'associatedDevicedrives': \App\Models\Devicedrive,
   *          'associatedDevicefirmwares': \App\Models\Devicefirmware,
   *          'associatedDevicegenerics': \App\Models\Devicegeneric,
   *          'associatedDevicegraphiccards': \App\Models\Devicegraphiccard,
   *          'associatedDeviceharddrives': \App\Models\Deviceharddrive,
   *          'associatedMemorymodules': \App\Models\Memorymodule,
   *          'associatedDevicemotherboards': \App\Models\Devicemotherboard,
   *          'associatedDevicenetworkcards': \App\Models\Devicenetworkcard,
   *          'associatedDevicepcis': \App\Models\Devicepci,
   *          'associatedDevicepowersupplies': \App\Models\Devicepowersupply,
   *          'associatedDeviceprocessors': \App\Models\Deviceprocessor,
   *          'associatedDevicesensors': \App\Models\Devicesensor,
   *          'associatedDevicesimcards': \App\Models\Devicesimcard,
   *          'associatedDevicesoundcards': \App\Models\Devicesoundcard,
   *          'associatedDomains': \App\Models\Domain,
   *          'associatedEnclosures': \App\Models\Enclosure,
   *          'associatedEntities': \App\Models\Entity,
   *          'associatedLines': \App\Models\Line,
   *          'associatedLocations': \App\Models\Location,
   *          'associatedMonitors': \App\Models\Monitor,
   *          'associatedNetworkequipments': \App\Models\Networkequipment,
   *          'associatedPassivedcequipments': \App\Models\Passivedcequipment,
   *          'associatedPdus': \App\Models\Pdu,
   *          'associatedPeripherals': \App\Models\Peripheral,
   *          'associatedPhones': \App\Models\Phone,
   *          'associatedPrinters': \App\Models\Printer,
   *          'associatedProjects': \App\Models\Project,
   *          'associatedProjecttasks': \App\Models\Projecttask,
   *          'associatedProjecttasktemplates': \App\Models\Projecttasktemplate,
   *          'associatedRacks': \App\Models\Rack,
   *          'associatedReminders': \App\Models\Reminder,
   *          'associatedSoftwares': \App\Models\Software,
   *          'associatedSoftwarelicenses': \App\Models\Softwarelicense,
   *          'associatedSuppliers': \App\Models\Supplier,
   *          'associatedUsers': \App\Models\User,
   *         }
   */
  protected function modelsForSubAssociateditem()
  {
    return [
      'associatedAppliances' => new \App\Models\Appliance(),
      'associatedBudgets' => new \App\Models\Budget(),
      'associatedCartridgeitems' => new \App\Models\Cartridgeitem(),
      'associatedCertificates' => new \App\Models\Certificate(),
      'associatedClusters' => new \App\Models\Cluster(),
      'associatedComputers' => new \App\Models\Computer(),
      'associatedConsumableitems' => new \App\Models\Consumableitem(),
      'associatedContacts' => new \App\Models\Contact(),
      'associatedContracts' => new \App\Models\Contract(),
      'associatedDcrooms' => new \App\Models\Dcroom(),
      'associatedDevicebatteries' => new \App\Models\Devicebattery(),
      'associatedDevicecases' => new \App\Models\Devicecase(),
      'associatedDevicecontrols' => new \App\Models\Devicecontrol(),
      'associatedDevicedrives' => new \App\Models\Devicedrive(),
      'associatedDevicefirmwares' => new \App\Models\Devicefirmware(),
      'associatedDevicegenerics' => new \App\Models\Devicegeneric(),
      'associatedDevicegraphiccards' => new \App\Models\Devicegraphiccard(),
      'associatedDeviceharddrives' => new \App\Models\Deviceharddrive(),
      'associatedMemorymodules' => new \App\Models\Memorymodule(),
      'associatedDevicemotherboards' => new \App\Models\Devicemotherboard(),
      'associatedDevicenetworkcards' => new \App\Models\Devicenetworkcard(),
      'associatedDevicepcis' => new \App\Models\Devicepci(),
      'associatedDevicepowersupplies' => new \App\Models\Devicepowersupply(),
      'associatedDeviceprocessors' => new \App\Models\Deviceprocessor(),
      'associatedDevicesensors' => new \App\Models\Devicesensor(),
      'associatedDevicesimcards' => new \App\Models\Devicesimcard(),
      'associatedDevicesoundcards' => new \App\Models\Devicesoundcard(),
      'associatedDomains' => new \App\Models\Domain(),
      'associatedEnclosures' => new \App\Models\Enclosure(),
      'associatedEntities' => new \App\Models\Entity(),
      'associatedLines' => new \App\Models\Line(),
      'associatedLocations' => new \App\Models\Location(),
      'associatedMonitors' => new \App\Models\Monitor(),
      'associatedNetworkequipments' => new \App\Models\Networkequipment(),
      'associatedPassivedcequipments' => new \App\Models\Passivedcequipment(),
      'associatedPdus' => new \App\Models\Pdu(),
      'associatedPeripherals' => new \App\Models\Peripheral(),
      'associatedPhones' => new \App\Models\Phone(),
      'associatedPrinters' => new \App\Models\Printer(),
      'associatedProjects' => new \App\Models\Project(),
      'associatedProjecttasks' => new \App\Models\Projecttask(),
      'associatedProjecttasktemplates' => new \App\Models\Projecttasktemplate(),
      'associatedRacks' => new \App\Models\Rack(),
      'associatedReminders' => new \App\Models\Reminder(),
      'associatedSoftwares' => new \App\Models\Software(),
      'associatedSoftwarelicenses' => new \App\Models\Softwarelicense(),
      'associatedSuppliers' => new \App\Models\Supplier(),
      'associatedUsers' => new \App\Models\User(),
    ];
  }
}
