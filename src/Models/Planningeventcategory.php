<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Planningeventcategory extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Planningeventcategory';
  protected $titles = ['Event category', 'Event categories'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
