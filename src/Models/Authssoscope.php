<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;

class Authssoscope extends Common
{
  use GetDropdownValues;

  protected $titles = ['SSO scope', 'SSO scopes'];
  protected $icon = 'id card alternate';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
  ];
}
