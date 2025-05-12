<?php

declare(strict_types=1);

namespace App;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * @implements ArrayAccess<string, string>
 * @implements Iterator<int|string, string>
 */
class CsrfStorage implements ArrayAccess, Iterator, Countable
{
  /** @var non-negative-int */
  private $position = 0;

  /**
   * @var \SlimSession\Helper|mixed
   */
  private $session;

  public function __construct()
  {
    $this->position = 0;
    $this->session = new \SlimSession\Helper();
  }

  public function offsetSet(mixed $offset, mixed $value): void
  {
    if (is_string($offset))
    {
      $this->session->set($offset, $value);
    }
  }

  public function offsetExists($offset): bool
  {
    return $this->session->exists($offset);
  }

  public function offsetUnset($offset): void
  {
    $this->session->delete('my_key');
  }

  public function offsetGet($offset): mixed
  {
    return $this->session->get($offset, '');
  }

  public function current(): mixed
  {
    $data = $this->session->get('csrf_list', []);
    return $data[$this->position];
  }

  public function key(): mixed
  {
    return $this->position;
  }

  public function next(): void
  {
    ++$this->position;
  }

  public function rewind(): void
  {
    $this->position = 0;
  }

  public function valid(): bool
  {
    $data = $this->session->get('csrf_list', []);
    return isset($data[$this->position]);
  }

  public function count(): int
  {
    return count($this->session->get('csrf_list', []));
  }
}
