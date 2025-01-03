<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Ssovariable extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Ssovariable';
  protected $titles = [
    'Field storage of the login in the HTTP request',
    'Fields storage of the login in the HTTP request'
  ];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
