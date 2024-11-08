<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Computerantivirus extends Common
{
  protected $table = 'computerantiviruses';
  protected $definition = '\App\Models\Definitions\Computerantivirus';
  protected $titles = ['Antivirus', 'Antivirus'];
  protected $icon = 'virus slash';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
  ];

  protected $with = [
    'manufacturer:id,name',
  ];

  protected $fillable = [
    'name',
    'computer_id',
  ];

  public function computer(): BelongsTo
  {
    return $this->belongsTo('App\Models\Computer');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }
}
