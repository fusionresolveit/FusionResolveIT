<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Category::class;
  protected $titles = ['Category', 'Categories'];
  protected $icon = 'edit';
  protected $tree = true;

  protected $appends = [
    // 'completename',
  ];

  protected $visible = [
    'category',
    'user',
    'group',
    'knowbaseitemcategory',
    'tickettemplateDemand',
    'tickettemplateIncident',
    'changetemplate',
    'problemtemplate',
    'entity',
    'completename',
  ];

  protected $with = [
    'category:id,name',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'knowbaseitemcategory:id,name',
    'tickettemplateDemand:id,name',
    'tickettemplateIncident:id,name',
    'changetemplate:id,name',
    'problemtemplate:id,name',
    'entity:id,name,completename',
  ];

  public function getCompletenameAttribute(): string
  {
    $names = [];
    if ($this->treepath != null)
    {
      $itemsId = str_split($this->treepath, 5);
      array_pop($itemsId);
      foreach ($itemsId as $key => $value)
      {
        $itemsId[$key] = (int) $value;
      }
      $items = \App\Models\Category::whereIn('id', $itemsId)->orderBy('treepath')->get();
      foreach ($items as $item)
      {
        $names[] = $item->name;
      }
    }
    $names[] = $this->name;
    return implode(' > ', $names);
  }

  /** @return BelongsTo<\App\Models\Category, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Category::class);
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

  /** @return BelongsTo<\App\Models\Knowbaseitemcategory, $this> */
  public function knowbaseitemcategory(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Knowbaseitemcategory::class, 'knowbaseitemcategory_id');
  }

  /** @return BelongsTo<\App\Models\Tickettemplate, $this> */
  public function tickettemplateDemand(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Tickettemplate::class, 'tickettemplate_id_demand');
  }

  /** @return BelongsTo<\App\Models\Tickettemplate, $this> */
  public function tickettemplateIncident(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Tickettemplate::class, 'tickettemplate_id_incident');
  }

  /** @return BelongsTo<\App\Models\Changetemplate, $this> */
  public function changetemplate(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Changetemplate::class, 'changetemplate_id');
  }

  /** @return BelongsTo<\App\Models\Problemtemplate, $this> */
  public function problemtemplate(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Problemtemplate::class, 'problemtemplate_id');
  }

  /** @return BelongsTo<\App\Models\Tickettemplate, $this> */
  public function templaterequest(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Tickettemplate::class, 'tickettemplates_id_demand');
  }

  /** @return BelongsTo<\App\Models\Tickettemplate, $this> */
  public function templateincident(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Tickettemplate::class, 'tickettemplates_id_incident');
  }

  /** @return BelongsTo<\App\Models\Changetemplate, $this> */
  public function templatechange(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Changetemplate::class, 'changetemplates_id');
  }

  /** @return BelongsTo<\App\Models\Problemtemplate, $this> */
  public function templateproblem(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Problemtemplate::class, 'problemtemplates_id');
  }
}
