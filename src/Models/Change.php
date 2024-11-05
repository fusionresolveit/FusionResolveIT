<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Change extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Change';
  protected $titles = ['Change', 'Changes'];
  protected $icon = 'clipboard check';

  protected $appends = [
    'itilcategorie',
    'usersidlastupdater',
    'usersidrecipient',
    'entity',
    'notes',
  ];

  protected $visible = [
    'itilcategorie',
    'usersidlastupdater',
    'usersidrecipient',
    'entity',
    'notes',
    'knowbaseitems',
  ];

  protected $with = [
    'itilcategorie:id,name',
    'usersidlastupdater:id,name',
    'usersidrecipient:id,name',
    'entity:id,name',
    'notes:id',
    'knowbaseitems:id,name',
  ];

  public function itilcategorie(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Category');
  }

  public function usersidlastupdater(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_lastupdater');
  }

  public function usersidrecipient(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_recipient');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function notes(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Notepad',
      'item',
    );
  }

  public function knowbaseitems(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Knowbaseitem',
      'item',
      'knowbaseitem_item'
    )->withPivot(
      'knowbaseitem_id',
    );
  }
}
