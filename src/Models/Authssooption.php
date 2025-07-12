<?php

declare(strict_types=1);

namespace App\Models;

class Authssooption extends Common
{
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
    return npgettext('sso', 'SSO option', 'SSO options', $nb);
  }
}
