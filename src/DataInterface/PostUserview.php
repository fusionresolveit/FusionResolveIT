<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostUserview extends Post
{
  /** @var ?\App\Models\User */
  public $user;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\User');

    $this->user = $this->setUser($data);
  }

  /**
   * @return array{user: \App\Models\User|null}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    return [
      'user' => $this->user,
    ];
  }
}
