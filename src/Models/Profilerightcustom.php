<?php

declare(strict_types=1);

namespace App\Models;

class Profilerightcustom extends Common
{
  protected $definition = '\App\Models\Definitions\Profilerightcustom';
  protected $titles = ['Profile', 'Profiles'];
  protected $icon = 'user check';
  protected $hasEntityField = false;

  protected $fillable = [
    'profileright_id',
    'definitionfield_id',
    'read',
    'write',
  ];
}
