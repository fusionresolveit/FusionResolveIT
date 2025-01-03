<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Mailcollector extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Mailcollector';
  protected $titles = ['Receiver', 'Receivers'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
