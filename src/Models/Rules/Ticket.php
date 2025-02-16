<?php

declare(strict_types=1);

namespace App\Models\Rules;

class Ticket extends \App\Models\Rules\Rule
{
  protected $titles = ['Business rules for tickets', 'Business rules for tickets'];

  // For default values
  protected $attributes = [
    'sub_type' => 'RuleTicket',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::addGlobalScope('ticketfilter', function ($builder)
    {
      $builder->where('sub_type', 'RuleTicket');
    });
  }
}
