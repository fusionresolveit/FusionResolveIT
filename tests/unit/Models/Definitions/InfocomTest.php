<?php

declare(strict_types=1);

namespace Tests\unit\Models\Definitions;

/**
 * @covers \App\Models\Definitions\Infocom
 * @uses \App\DataInterface\Definition
 * @uses \App\DataInterface\DefinitionCollection
 * @uses \App\v1\Controllers\Common
 * @uses \App\v1\Controllers\Dropdown
 */
final class InfocomTest extends Common
{
  protected $className = 'Infocom';
}
