<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostGroupSubUser extends Post
{
  /** @var ?\App\Models\User */
  public $user;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Group');

    $this->user = $this->setUser($data);

    if (is_null($this->user))
    {
      throw new \Exception('Wrong data request', 400);
    }
  }

  /**
   * @return array{user?: \App\Models\User}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    $vars = get_object_vars($this);
    $user = \App\Models\User::where('id', $this->user)->first();
    if (!is_null($user))
    {
      return ['user' => $user];
    }
    return [];
  }
}
