<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostGroupview extends Post
{
  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Group');

    $this->group = $this->setGroup($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{group: \App\Models\Group|null, is_recursive: bool}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    if (is_null($this->is_recursive))
    {
      $this->is_recursive = false;
    }
    return [
      'group'        => $this->group,
      'is_recursive' => $this->is_recursive,
    ];
  }
}
