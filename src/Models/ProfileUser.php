<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileUser extends Common
{
  protected $definition = '\App\Models\Definitions\ProfileUser';
  protected $titles = ['Profile User', 'Profiles Users'];
  protected $icon = 'user check';
  protected $table = 'profile_user';
  protected $hasEntityField = false;
}
