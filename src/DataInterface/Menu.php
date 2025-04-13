<?php

declare(strict_types=1);

namespace App\DataInterface;

class Menu
{
  /** @var string */
  public $name;

  /** @var string */
  public $title;

  /** @var string */
  public $icon;

  /** @var bool */
  public $display;

  /** @var string */
  public $endpoint;

  /** @var string */
  public $comment;

  public function __construct(
    string $name,
    string $title,
    string $icon,
    bool $display,
    string $endpoint,
    string $comment,
  )
  {
    $this->name = $name;
    $this->title = $title;
    $this->icon = $icon;
    $this->endpoint = $endpoint;
    $this->display = $display;
    $this->comment = $comment;
  }
}
