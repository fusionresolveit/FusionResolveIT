<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fieldblacklist extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Fieldblacklist::class;
  protected $titles = ['Ignored value for the unicity', 'Ignored values for the unicity'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];
}
