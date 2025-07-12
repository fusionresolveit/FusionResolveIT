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

  /** @var string */
  protected $icon = 'history';

  /** @var boolean */
  protected $hasEntityField = false;

  public const CREATED_AT = null;

  protected $appends = [
    'viewmessage',
  ];

  protected $visible = [
    'viewmessage',
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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Historical', 'Historicals', $nb);
  }

  /**
   * Set message for the view (UI)
   */
  public function getViewmessageAttribute(): string
  {
    $oldValue = $this->attributes['old_value'];
    if (is_null($oldValue))
    {
      $oldValue = pgettext('history', 'empty value');
    }

    return sprintf(
      pgettext('history', 'Changed %s%s%s from %s%s%s to %s%s%s'),
      '<i>',
      '[field]', // data.titles[item.id_search_option]
      '</i>',
      '<span class="bold ui teal text">',
      $oldValue,
      '</span>',
      '<span class="bold ui blue text">',
      $this->attributes['new_value'],
      '</span>',
    );
  }
}
