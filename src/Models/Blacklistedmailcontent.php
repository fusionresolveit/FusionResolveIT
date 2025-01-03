<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Blacklistedmailcontent extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Blacklistedmailcontent';
  protected $titles = ['Blacklisted mail content', 'Blacklisted mail content'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
