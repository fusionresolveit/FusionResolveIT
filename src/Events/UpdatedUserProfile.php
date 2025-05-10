<?php

declare(strict_types=1);

namespace App\Events;

final class UpdatedUserProfile
{
  /**
   * @template C of \App\Models\Common
   * @param C $model
   */
  public static function run($model): void
  {
    if (get_class($model) != \App\Models\User::class)
    {
      return;
    }

    // Get profile_id and entity_id of user
    // Check if profile of user exists with these values
    // if not, add them

    if ($model->profile_id === 0 || $model->entity_id === 0)
    {
      return;
    }

    $found = false;
    foreach ($model->profiles as $profile)
    {
      if (
          $profile->getAttribute('profile_id') === $model->profile_id &&
          $profile->getAttribute('entity_id') == $model->entity_id
      )
      {
        $found = true;
      }
    }

    if ($found)
    {
      return;
    }

    $model->profiles()->attach($model->profile_id, ['entity_id' => $model->entity_id]);
  }
}
