<?php

declare(strict_types=1);

namespace App\Models;

class Itilproject extends Common
{
  protected $definition = '\App\Models\Definitions\Itilproject';
  protected $titles = ['ITIL Project', 'ITIL Project'];
  protected $icon = 'edit';
  protected $table = 'itil_project';
  protected $hasEntityField = false;
}
