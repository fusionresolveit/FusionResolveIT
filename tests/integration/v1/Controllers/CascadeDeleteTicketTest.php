<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass('\App\Models\Ticket')]
#[CoversClass('\App\Models\Followup')]

final class CascadeDeleteTicketTest extends TestCase
{
  protected function setUp(): void
  {
    // Needed to reset events
    \App\Models\Ticket::boot();
  }

  public function testFollowupDelete(): void
  {
    // Create ticket
    $ticket = \App\Models\Ticket::create([
      'name' => 'test cascade delete',
    ]);

    // create followup
    $followup = \App\Models\Followup::create([
      'content'   => 'test cascade followup',
      'item_id'   => $ticket->id,
      'item_type' => \App\Models\Ticket::class,
    ]);

    // check followup is in database
    $followup = \App\Models\Followup::where('content', 'test cascade followup')->first();
    $this->assertNotNull($followup);

    // delete ticket
    $ticket->refresh();
    $ticket->forceDelete();

    // ticket must be deleted
    $ticket = \App\Models\Ticket::where('name', 'test cascade delete')->first();
    $this->assertNull($ticket);

    // check followup is deleted
    $followup = \App\Models\Followup::where('content', 'test cascade followup')->first();
    $this->assertNull($followup);
  }
}
