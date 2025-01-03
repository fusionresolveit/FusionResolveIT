<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Ticketrecurrent extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Ticketrecurrent';
  protected $titles = ['Recurrent ticket', 'Recurrent tickets'];
  protected $icon = 'stopwatch';

  protected $appends = [
  ];

  protected $visible = [
    // 'requester',
    // 'requestergroup',
    // 'watcher',
    // 'watchergroup',
    // 'technician',
    // 'techniciangroup',
    // 'usersidlastupdater',
    // 'usersidrecipient',
    // 'itilcategorie',
    'entity',
  ];

  protected $with = [
    // 'requester:id,name,firstname,lastname',
    // 'requestergroup:id,name,completename',
    // 'watcher:id,name,firstname,lastname',
    // 'watchergroup::id,name,completename',
    // 'technician:id,name,firstname,lastname',
    // 'techniciangroup:id,name,completename',
    // 'usersidlastupdater:id,name,firstname,lastname',
    // 'usersidrecipient:id,name,firstname,lastname',
    // 'itilcategorie:id,name',
    'entity:id,name,completename',
  ];

  // public function requester()
  // {
  //   return $this->belongsToMany(\App\Models\User::class, 'glpi_tickets_users', 'tickets_id', 'users_id')
  // ->wherePivot('type', 1);
  // }

  // public function requestergroup()
  // {
  //   return $this->belongsToMany(\App\Models\Group::class, 'glpi_groups_tickets', 'tickets_id', 'groups_id')
  // ->wherePivot('type', 1);
  // }

  // public function watcher()
  // {
  //   return $this->belongsToMany(\App\Models\User::class, 'glpi_tickets_users', 'tickets_id', 'users_id')
  // ->wherePivot('type', 3);
  // }

  // public function watchergroup()
  // {
  //   return $this->belongsToMany(\App\Models\Group::class, 'glpi_groups_tickets', 'tickets_id', 'groups_id')
  // ->wherePivot('type', 3);
  // }

  // public function technician()
  // {
  //   return $this->belongsToMany(\App\Models\User::class, 'glpi_tickets_users', 'tickets_id', 'users_id')
  // ->wherePivot('type', 2);
  // }

  // public function techniciangroup()
  // {
  //   return $this->belongsToMany(\App\Models\Group::class, 'glpi_groups_tickets', 'tickets_id', 'groups_id')
  // ->wherePivot('type', 2);
  // }

  // public function usersidlastupdater(): BelongsTo
  // {
  //   return $this->belongsTo(\App\Models\User::class, 'users_id_lastupdater');
  // }

  // public function usersidrecipient(): BelongsTo
  // {
  //   return $this->belongsTo(\App\Models\User::class, 'users_id_recipient');
  // }

  // public function itilcategorie(): BelongsTo
  // {
  //   return $this->belongsTo(\App\Models\ITILCategory::class, 'itilcategories_id');
  // }
}
