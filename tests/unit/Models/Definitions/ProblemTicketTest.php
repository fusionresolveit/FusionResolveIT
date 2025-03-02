<?php

declare(strict_types=1);

namespace Tests\unit\Models\Definitions;

/**
 * @covers \App\Models\Definitions\ProblemTicket
 * @uses \App\DataInterface\Definition
 * @uses \App\DataInterface\DefinitionCollection
 * @uses \App\v1\Controllers\Common
 * @uses \App\v1\Controllers\Dropdown
 */
final class ProblemTicketTest extends Common
{
  protected $className = 'ProblemTicket';
}
