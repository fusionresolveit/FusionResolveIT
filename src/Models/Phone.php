<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Phone extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowledgebasearticles;
  use \App\Traits\Relationships\Reservations;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Phone::class;
  protected $icon = 'phone';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'tickets',
    'problems',
    'changes',
    'infocom',
    'contracts',
    'notes',
    'knowledgebasearticles',
    'reservations',
    'domains',
    'appliances',
    'softwareversions',
    'operatingsystems',
    'memoryslots',
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
    'devicedrives',
    'volumes',
    'connections',
    'certificates',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'model',
    'phonepowersupply',
    'state',
    'manufacturer',
    'user',
    'group',
    'network',
    'grouptech',
    'usertech',
    'location',
    'entity',
    'domains',
    'appliances',
    'notes',
    'knowledgebasearticles',
    'documents',
    'contracts',
    'softwareversions',
    'operatingsystems',
    'tickets',
    'problems',
    'changes',
    'memories',
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
    'devicedrives',
    'volumes',
    'connections',
    'infocom',
    'reservations',
    'certificates',
  ];

  protected $with = [
    'type:id,name',
    'model:id,name',
    'phonepowersupply:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'network:id,name',
    'grouptech:id,name,completename',
    'usertech:id,name,firstname,lastname',
    'location:id,name',
    'entity:id,name,completename',
    'domains:id,name',
    'appliances:id,name',
    'notes:id',
    'knowledgebasearticles:id,name',
    'documents:id,name',
    'contracts:id,name',
    'softwareversions:id,name',
    'operatingsystems:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'memoryslots',
    'firmware:id,name',
    'processors:id,name',
    'storages:id,name',
    'batteries:id,name',
    'soundcards:id,name',
    'controllers:id,name',
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
    'volumes:id,name',
    'connections:id,name',
    'infocom',
    'reservations',
    'certificates:id,name',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Phone', 'Phones', $nb);
  }

  public static function boot(): void
  {
    parent::boot();

    static::pivotAttaching(function ($model, $relationName, $pivotIds, $pivotIdsAttributes)
    {
      new \App\Events\PivotAttaching($relationName, $pivotIds);
    });
  }

  /** @return BelongsTo<\App\Models\Phonetype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Phonetype::class, 'phonetype_id');
  }

  /** @return BelongsTo<\App\Models\Phonemodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Phonemodel::class, 'phonemodel_id');
  }

  /** @return BelongsTo<\App\Models\Phonepowersupply, $this> */
  public function phonepowersupply(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Phonepowersupply::class);
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

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function group(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class);
  }

  /** @return BelongsTo<\App\Models\Network, $this> */
  public function network(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Network::class);
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function grouptech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usertech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
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

  /** @return MorphToMany<\App\Models\Softwareversion, $this> */
  public function softwareversions(): MorphToMany
  {
    return $this->morphToMany(\App\Models\Softwareversion::class, 'item', 'item_softwareversion');
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

  /** @return MorphMany<\App\Models\Memoryslot, $this> */
  public function memoryslots(): MorphMany
  {
    return $this->morphMany(
      \App\Models\Memoryslot::class,
      'item',
    );
  }

  /** @return BelongsTo<\App\Models\Firmware, $this> */
  public function firmware(): BelongsTo
  {
    return $this->BelongsTo(\App\Models\Firmware::class);
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

  /** @return MorphToMany<\App\Models\Storage, $this> */
  public function storages(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Storage::class,
      'item',
      'item_storage'
    )->withPivot(
      'storage_id',
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

  /** @return HasMany<\App\Models\Itemdisk, $this> */
  public function volumes(): HasMany
  {
    return $this->hasMany(\App\Models\Itemdisk::class, 'item_id')->where('item_type', get_class($this));
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function connections(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Computer::class,
      'item',
      'computer_item'
    )->withPivot(
      'computer_id',
      'is_dynamic',
    );
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
}
