<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Category';
  protected $titles = ['Category', 'Categories'];
  protected $icon = 'edit';
  protected $tree = true;

  protected $appends = [
    // 'completename',
  ];

  protected $visible = [
    'category',
    'users',
    'groups',
    'knowbaseitemcategories',
    'tickettemplatesDemand',
    'tickettemplatesIncident',
    'changetemplates',
    'problemtemplates',
    'entity',
    'completename',
  ];

  protected $with = [
    'category:id,name',
    'users:id,name,firstname,lastname',
    'groups:id,name,completename',
    'knowbaseitemcategories:id,name',
    'tickettemplatesDemand:id,name',
    'tickettemplatesIncident:id,name',
    'changetemplates:id,name',
    'problemtemplates:id,name',
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
  public function users(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id');
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function groups(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id');
  }

  /** @return BelongsTo<\App\Models\Knowbaseitemcategory, $this> */
  public function knowbaseitemcategories(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Knowbaseitemcategory::class, 'knowbaseitemcategory_id');
  }

  /** @return BelongsTo<\App\Models\Tickettemplate, $this> */
  public function tickettemplatesDemand(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Tickettemplate::class, 'tickettemplate_id_demand');
  }

  /** @return BelongsTo<\App\Models\Tickettemplate, $this> */
  public function tickettemplatesIncident(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Tickettemplate::class, 'tickettemplate_id_incident');
  }

  /** @return BelongsTo<\App\Models\Changetemplate, $this> */
  public function changetemplates(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Changetemplate::class, 'changetemplate_id');
  }

  /** @return BelongsTo<\App\Models\Problemtemplate, $this> */
  public function problemtemplates(): BelongsTo
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
