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
  public function testFollowupDelete(): void
  {
    // Create ticket
    $ticket = \App\Models\Ticket::create([
      'name' => 'test cascade delete',
    ]);

    // create followup
    $followup = \App\Models\Followup::create([
      'content'   => 'test followup',
      'item_id'   => $ticket->id,
      'item_type' => \App\Models\Ticket::class,
    ]);

    // check followup is in database
    $followup = \App\Models\Followup::where('content', 'test followup')->first();
    $this->assertNotNull($followup);

    // delete ticket
    $ticket->refresh();
    $ticket->forceDelete();

    // check followup is deleted
    $followup = \App\Models\Followup::where('content', 'test followup')->first();
    $this->assertNull($followup);
  }
}
