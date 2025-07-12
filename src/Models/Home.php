<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Home extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Home::class;
  protected $icon = 'home';

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
    return npgettext('global', 'Home', 'Home', $nb);
  }
}
