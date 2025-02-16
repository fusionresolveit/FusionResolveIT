<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blacklistedmailcontent extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Blacklistedmailcontent::class;
  protected $titles = ['Blacklisted mail content', 'Blacklisted mail content'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
