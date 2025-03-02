<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Document::class;
  protected $titles = ['Document', 'Documents'];
  protected $icon = 'file';

  protected $appends = [];

  protected $visible = [
    'categorie',
    'entity',
    'notes',
    'documents',
  ];

  protected $with = [
    'categorie:id,name',
    'entity:id,name,completename',
    'notes:id',
    'documents:id,name',
  ];

  /** @return BelongsTo<\App\Models\Documentcategory, $this> */
  public function categorie(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Documentcategory::class, 'documentcategory_id');
  }

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function associatedAppliances(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Appliance::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Budget, $this> */
  public function associatedBudgets(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Budget::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Cartridgeitem, $this> */
  public function associatedCartridgeitems(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Cartridgeitem::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Certificate, $this> */
  public function associatedCertificates(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Certificate::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Cluster, $this> */
  public function associatedClusters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Cluster::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function associatedComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Consumableitem, $this> */
  public function associatedConsumableitems(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Consumableitem::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Contact, $this> */
  public function associatedContacts(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Contact::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Contract, $this> */
  public function associatedContracts(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Contract::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Dcroom, $this> */
  public function associatedDcrooms(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Dcroom::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicebattery, $this> */
  public function associatedDevicebatteries(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicebattery::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicecase, $this> */
  public function associatedDevicecases(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicecase::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicecontrol, $this> */
  public function associatedDevicecontrols(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicecontrol::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicedrive, $this> */
  public function associatedDevicedrives(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicedrive::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicefirmware, $this> */
  public function associatedDevicefirmwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicefirmware::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicegeneric, $this> */
  public function associatedDevicegenerics(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicegeneric::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicegraphiccard, $this> */
  public function associatedDevicegraphiccards(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicegraphiccard::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Deviceharddrive, $this> */
  public function associatedDeviceharddrives(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Deviceharddrive::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicememory, $this> */
  public function associatedDevicememories(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicememory::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicemotherboard, $this> */
  public function associatedDevicemotherboards(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicemotherboard::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicenetworkcard, $this> */
  public function associatedDevicenetworkcards(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicenetworkcard::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicepci, $this> */
  public function associatedDevicepcis(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicepci::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicepowersupply, $this> */
  public function associatedDevicepowersupplies(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicepowersupply::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Deviceprocessor, $this> */
  public function associatedDeviceprocessors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Deviceprocessor::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicesensor, $this> */
  public function associatedDevicesensors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicesensor::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicesimcard, $this> */
  public function associatedDevicesimcards(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicesimcard::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Devicesoundcard, $this> */
  public function associatedDevicesoundcards(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Devicesoundcard::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function associatedDomains(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Domain::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Enclosure, $this> */
  public function associatedEnclosures(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Enclosure::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Entity, $this> */
  public function associatedEntities(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Entity::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Line, $this> */
  public function associatedLines(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Line::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Location, $this> */
  public function associatedLocations(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Location::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Monitor, $this> */
  public function associatedMonitors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Monitor::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function associatedNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Passivedcequipment, $this> */
  public function associatedPassivedcequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Passivedcequipment::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Pdu, $this> */
  public function associatedPdus(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Pdu::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function associatedPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function associatedPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function associatedPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Project, $this> */
  public function associatedProjects(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Project::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Projecttask, $this> */
  public function associatedProjecttasks(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Projecttask::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Projecttasktemplate, $this> */
  public function associatedProjecttasktemplates(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Projecttasktemplate::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Rack, $this> */
  public function associatedRacks(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Rack::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Reminder, $this> */
  public function associatedReminders(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Reminder::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Software, $this> */
  public function associatedSoftwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Software::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Softwarelicense, $this> */
  public function associatedSoftwarelicenses(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Softwarelicense::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\Supplier, $this> */
  public function associatedSuppliers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Supplier::class, 'item', 'document_item');
  }

  /** @return MorphToMany<\App\Models\User, $this> */
  public function associatedUsers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\User::class, 'item', 'document_item');
  }
}
