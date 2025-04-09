<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostEntityview extends Post
{
  /** @var ?\App\Models\Entity */
  public $entity;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Entity');

    $this->entity = $this->setEntity($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{entity: \App\Models\Entity|null, is_recursive: bool}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    if (is_null($this->is_recursive))
    {
      $this->is_recursive = false;
    }
    return [
      'entity'       => $this->entity,
      'is_recursive' => $this->is_recursive,
    ];
  }
}
