<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificatetype extends Common
{
  protected $definition = '\App\Models\Definitions\Certificatetype';
  protected $titles = ['Certificate type', 'Certificate types'];
  protected $icon = 'edit';
}