<?php

declare(strict_types=1);

namespace App\DataInterface;

class Definition
{
  /** @var int */
  public $id;

  /** @var string */
  public $title;

  /** @var 'input'|'inputpassword'|'textarea'|'email'|'boolean'|'date'|'datetime'|'dropdown'|'dropdown_remote' */
  public $type;

  /** @var string */
  public $name;

  /** @var string|null */
  public $dbname;

  /** @var string|null */
  public $itemtype;

  /** @var bool|null */
  public $multiple;

  /** @var array<mixed> */
  public $pivot;

  /** @var array<mixed> */
  public $values;

  /** @var bool */
  public $readonly;

  /** @var string|null */
  public $displaygroup;

  /** @var bool|null */
  public $fillable;

  /** @var bool|null */
  public $display;

  /** @var array<string> */
  public $relationfields;

  /** @var array<string> */
  public $usein;

  /** @var int|string|bool|null */
  public $value;

  /** @var string|null */
  public $valuename;

  /** @var bool|null */
  public $autocomplete;

  /** @var bool|null */
  public $isPivot;

  /**
   * @param 'input'|'inputpassword'|'textarea'|'email'|'boolean'|'date'|'datetime'|'dropdown'|'dropdown_remote' $type
   * @param array<mixed> $pivot
   * @param array<mixed> $values
   * @param array<string> $relationfields
   * @param array<string> $usein
   */
  public function __construct(
    int $id,
    string $title,
    $type,
    string $name,
    string|null $dbname = null,
    string|null $itemtype = null,
    bool|null $multiple = null,
    array $pivot = [],
    array $values = [],
    bool $readonly = false,
    string|null $displaygroup = null,
    bool|null $fillable = null,
    bool|null $display = null,
    array $relationfields = [],
    array $usein = [],
    bool|null $autocomplete = null,
    bool|null $isPivot = false,
  )
  {
    $this->id = $id;
    $this->title = $title;
    $this->type = $type;
    $this->name = $name;
    $this->dbname = $dbname;
    $this->itemtype = $itemtype;
    $this->multiple = $multiple;
    $this->pivot = $pivot;
    $this->values = $values;
    $this->readonly = $readonly;
    $this->displaygroup = $displaygroup;
    $this->fillable = $fillable;
    $this->display = $display;
    $this->relationfields = $relationfields;
    $this->usein = $usein;
    $this->autocomplete = $autocomplete;
    $this->isPivot = $isPivot;
  }
}
