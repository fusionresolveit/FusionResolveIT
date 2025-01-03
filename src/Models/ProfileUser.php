<?php

declare(strict_types=1);

namespace App\Models;

class ProfileUser extends Common
{
  protected $definition = '\App\Models\Definitions\ProfileUser';
  protected $titles = ['Profile User', 'Profiles Users'];
  protected $icon = 'user check';
  protected $table = 'profile_user';
  protected $hasEntityField = false;
}
