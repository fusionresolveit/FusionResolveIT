<?php

declare(strict_types=1);

namespace App\Models\Rules;

class Ticket extends \App\Models\Rules\Rule
{
  // For default values
  protected $attributes = [
    'sub_type' => 'ticket',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::addGlobalScope('ticketfilter', function ($builder)
    {
      $builder->where('sub_type', 'ticket');
    });
  }

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Business rule for tickets', 'Business rules for tickets', $nb);
  }
}
