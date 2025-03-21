<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
  use SoftDeletes;

  // protected $definition = '\App\Models\Definitions\Log';
  /** @var string[] */
  protected $titles = ['Historical', 'Historical'];

  /** @var string */
  protected $icon = 'history';

  /** @var boolean */
  protected $hasEntityField = false;

  public const CREATED_AT = null;

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
  ];

  protected $fillable = [
    'item_type',
    'item_id',
    'user_name',
    'old_value',
    'new_value',
    'id_search_option',
  ];
}
