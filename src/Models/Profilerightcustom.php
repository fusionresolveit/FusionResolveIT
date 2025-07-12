<?php

declare(strict_types=1);

namespace App\Models;

class Profilerightcustom extends Common
{
  protected $definition = \App\Models\Definitions\Profilerightcustom::class;
  protected $icon = 'user check';
  protected $hasEntityField = false;

  protected $fillable = [
    'profileright_id',
    'definitionfield_id',
    'read',
    'write',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Profile', 'Profiles', $nb);
  }
}
