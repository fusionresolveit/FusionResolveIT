<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Category';
  protected $titles = ['Category', 'Categories'];
  protected $icon = 'edit';

  protected $appends = [
    // 'category',
    // 'users',
    // 'groups',
    // 'knowbaseitemcategories',
    // 'tickettemplatesDemand',
    // 'tickettemplatesIncident',
    // 'changetemplates',
    // 'problemtemplates',
    'entity',
  ];

  protected $visible = [
    'category',
    'users',
    'groups',
    'knowbaseitemcategories',
    // 'tickettemplatesDemand',
    // 'tickettemplatesIncident',
    // 'changetemplates',
    // 'problemtemplates',
    'entity',
  ];

  protected $with = [
    'category:id,name',
    'users:id,name',
    'groups:id,name',
    'knowbaseitemcategories:id,name',
    // 'tickettemplatesDemand:id,name',
    // 'tickettemplatesIncident:id,name',
    // 'changetemplates:id,name',
    // 'problemtemplates:id,name',
    'entity:id,name',
  ];

  public function category(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Category');
  }

  public function users(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id');
  }

  public function groups(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id');
  }

  public function knowbaseitemcategories(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Knowbaseitemcategory', 'knowbaseitemcategory_id');
  }

  public function tickettemplatesDemand(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Tickettemplate', 'tickettemplate_id_demand');
  }

  public function tickettemplatesIncident(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Tickettemplate', 'tickettemplate_id_incident');
  }

  public function changetemplates(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Changetemplate', 'changetemplate_id');
  }

  public function problemtemplates(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Problemtemplate', 'problemtemplate_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
