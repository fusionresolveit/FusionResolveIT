<?php

declare(strict_types=1);

namespace App\DataInterface;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Definition>
 */
class DefinitionCollection implements IteratorAggregate
{
  /** @var array<Definition> */
  private $definitions;

  /**
   * @param array<Definition> $definitions
  */
  public function __construct(array $definitions = [])
  {
    $this->definitions = $definitions;
  }

  public function add(Definition $definition): void
  {
    $this->definitions[] = $definition;
  }

  public function getIterator(): Traversable
  {
    return new ArrayIterator($this->definitions);
  }

  public function remove(Definition $item): void
  {
    foreach($this->definitions as $index => $definition)
    {
      if ($definition == $item)
      {
        unset($this->definitions[$index]);
        break;
      }
    }
  }
}
