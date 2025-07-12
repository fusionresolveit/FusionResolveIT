<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;

class Authssoscope extends Common
{
  use GetDropdownValues;

  protected $icon = 'id card alternate';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('sso', 'SSO scope', 'SSO scopes', $nb);
  }
}
