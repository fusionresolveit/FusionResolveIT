<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Computer extends Common
{
  use PivotEventTrait;
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;
  use \App\Traits\Relationships\Reservations;

  protected $definition = '\App\Models\Definitions\Computer';
  protected $titles = ['Computer', 'Computers'];
  protected $icon = 'laptop';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'model',
    'state',
    'manufacturer',
    'network',
    'groupstech',
    'userstech',
    'user',
    'group',
    'location',
    'autoupdatesystem',
    'softwareversions',
    'uuid',
    'entity',
    'certificates',
    'domains',
    'appliances',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'powersupplies',
    'sensors',
    'devicepcis',
    'devicegenerics',
    'devicenetworkcards',
    'devicesimcards',
    'devicemotherboards',
    'devicecases',
    'devicegraphiccards',
    'devicedrives',
    'infocom',
    'reservations',
  ];

  protected $with = [
    'type:id,name',
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'network:id,name',
    'groupstech:id,name,completename',
    'userstech:id,name,firstname,lastname',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'location:id,name',
    'autoupdatesystem:id,name',
    'softwareversions:id,name',
    'operatingsystems:id,name',
    'memories:id,name',
    'firmwares:id,name',
    'processors:id,name',
    'harddrives:id,name',
    'batteries:id,name',
    'soundcards:id,name',
    'controllers:id,name',
    'volumes:id,name',
    'virtualization:id,name',
    'certificates:id,name',
    'entity:id,name,completename',
    'domains:id,name',
    'appliances:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'powersupplies:id,name',
    'sensors:id,name',
    'devicepcis:id,name',
    'devicegenerics:id,name',
    'devicenetworkcards:id,name',
    'devicesimcards:id,name',
    'devicemotherboards:id,name',
    'devicecases:id,name',
    'devicegraphiccards:id,name',
    'devicedrives:id,name',
    'infocom',
    'reservations',
  ];

  protected static function booted(): void
  {
    parent::boot();

    static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes)
    {
      if ($relationName == 'softwareversions')
      {
        $softwareversions = \App\Models\Softwareversion::with('software:name')->whereIn('id', $pivotIds)->get();
        foreach($softwareversions as $softwareversion)
        {
          $software = $softwareversion->software()->first();
          \App\v1\Controllers\Log::addEntry(
            $model,
            'version ' . $softwareversion->name . ' of software ' . $software->name . ' installed',
            $softwareversion->name . ' (' . $softwareversion->id . ')',
          );
        }
      }
    });
  }


  /** @return BelongsTo<\App\Models\Computertype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Computertype::class, 'computertype_id');
  }

  /** @return BelongsTo<\App\Models\Computermodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Computermodel::class, 'computermodel_id');
  }

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class);
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Network, $this> */
  public function network(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Network::class);
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function groupstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function userstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id');
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function group(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id');
  }

  /** @return BelongsTo<\App\Models\Autoupdatesystem, $this> */
  public function autoupdatesystem(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Autoupdatesystem::class);
  }

  /** @return MorphToMany<\App\Models\Softwareversion, $this> */
  public function softwareversions(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Softwareversion::class,
      'item',
      'item_softwareversion'
    )->withPivot('date_install');
  }

  /** @return MorphToMany<\App\Models\Operatingsystem, $this> */
  public function operatingsystems(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Operatingsystem::class,
      'item',
      'item_operatingsystem'
    )->withPivot(
      'operatingsystemversion_id',
      'operatingsystemservicepack_id',
      'operatingsystemarchitecture_id',
      'operatingsystemkernelversion_id',
      'operatingsystemedition_id',
      'license_number',
      'licenseid',
      'installationdate',
      'winowner',
      'wincompany',
      'oscomment',
      'hostid'
    );
  }

  /** @return MorphToMany<\App\Models\Devicememory, $this> */
  public function memories(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicememory::class,
      'item',
      'item_devicememory'
    )->withPivot(
      'devicememory_id',
      'size',
      'serial',
      'busID',
      'location_id',
      'otherserial',
      'state_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicefirmware, $this> */
  public function firmwares(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicefirmware::class,
      'item',
      'item_devicefirmware'
    )->withPivot(
      'devicefirmware_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Deviceprocessor, $this> */
  public function processors(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Deviceprocessor::class,
      'item',
      'item_deviceprocessor'
    )->withPivot(
      'deviceprocessor_id',
      'frequency',
      'nbcores',
      'nbthreads',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Deviceharddrive, $this> */
  public function harddrives(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Deviceharddrive::class,
      'item',
      'item_deviceharddrive'
    )->withPivot(
      'deviceharddrive_id',
      'capacity',
      'serial',
      'location_id',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicebattery, $this> */
  public function batteries(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicebattery::class,
      'item',
      'item_devicebattery'
    )->withPivot(
      'devicebattery_id',
      'manufacturing_date',
      'serial',
      'location_id',
      'otherserial',
      'state_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicesoundcard, $this> */
  public function soundcards(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicesoundcard::class,
      'item',
      'item_devicesoundcard'
    )->withPivot(
      'devicesoundcard_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicecontrol, $this> */
  public function controllers(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicecontrol::class,
      'item',
      'item_devicecontrol'
    )->withPivot(
      'devicecontrol_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  /** @return HasMany<\App\Models\Computervirtualmachine, $this> */
  public function virtualization(): HasMany
  {
    return $this->hasMany(\App\Models\Computervirtualmachine::class);
  }

  /** @return HasMany<\App\Models\Itemdisk, $this> */
  public function volumes(): HasMany
  {
    return $this->hasMany(\App\Models\Itemdisk::class, 'item_id')->where('item_type', get_class($this));
  }

  /** @return MorphToMany<\App\Models\Certificate, $this> */
  public function certificates(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Certificate::class,
      'item',
      'certificate_item'
    )->withPivot(
      'certificate_id',
    );
  }

  /** @return HasMany<\App\Models\Computerantivirus, $this> */
  public function antiviruses(): HasMany
  {
    return $this->hasMany(\App\Models\Computerantivirus::class);
  }

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function domains(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Domain::class,
      'item',
      'domain_item'
    )->withPivot(
      'domainrelation_id',
    );
  }

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function appliances(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Appliance::class,
      'item',
      'appliance_item'
    );
  }

  /** @return MorphToMany<\App\Models\Devicepowersupply, $this> */
  public function powersupplies(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicepowersupply::class,
      'item',
      'item_devicepowersupply'
    )->withPivot(
      'devicepowersupply_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicesensor, $this> */
  public function sensors(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicesensor::class,
      'item',
      'item_devicesensor'
    )->withPivot(
      'devicesensor_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicepci, $this> */
  public function devicepcis(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicepci::class,
      'item',
      'item_devicepci'
    )->withPivot(
      'devicepci_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicegeneric, $this> */
  public function devicegenerics(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicegeneric::class,
      'item',
      'item_devicegeneric'
    )->withPivot(
      'devicegeneric_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicenetworkcard, $this> */
  public function devicenetworkcards(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicenetworkcard::class,
      'item',
      'item_devicenetworkcard'
    )->withPivot(
      'devicenetworkcard_id',
      'location_id',
      'mac',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicesimcard, $this> */
  public function devicesimcards(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicesimcard::class,
      'item',
      'item_devicesimcard'
    )->withPivot(
      'devicesimcard_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'msin',
      'user_id',
      'group_id',
      'line_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicemotherboard, $this> */
  public function devicemotherboards(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicemotherboard::class,
      'item',
      'item_devicemotherboard'
    )->withPivot(
      'devicemotherboard_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicecase, $this> */
  public function devicecases(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicecase::class,
      'item',
      'item_devicecase'
    )->withPivot(
      'devicecase_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicegraphiccard, $this> */
  public function devicegraphiccards(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicegraphiccard::class,
      'item',
      'item_devicegraphiccard'
    )->withPivot(
      'devicegraphiccard_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'memory',
      'id',
    );
  }

  /** @return MorphToMany<\App\Models\Devicedrive, $this> */
  public function devicedrives(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Devicedrive::class,
      'item',
      'item_devicedrive'
    )->withPivot(
      'devicedrive_id',
      'location_id',
      'serial',
      'otherserial',
      'state_id',
      'busID',
      'id',
    );
  }
}
