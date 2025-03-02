<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planningeventcategory extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Planningeventcategory::class;
  protected $titles = ['Event category', 'Event categories'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
