<?php

namespace App\Models\Rules;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rule extends \App\Models\Common
{
  protected $table = 'rules';
  protected $definition = null;
  protected $titles = ['Rule', 'Rules'];
  protected $icon = 'magic';
  protected $hasEntityField = false;

  protected $fillable = [
    'name',
    'sub_type',
    'match',
    'is_active',
  ];
}
