<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Applianceenvironment extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Applianceenvironment';
  protected $titles = ['Appliance environment', 'Appliance environments'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
