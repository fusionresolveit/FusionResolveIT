<?php

declare(strict_types=1);

namespace App\v1\Controllers;

final class Log extends Common
{
  public static function addEntry(
    string $modelname,
    int $id,
    string $message,
    string $new_value,
    string|null $old_value = null,
    int $idSearchOption = 0,
    bool $set_by_rule = false
  ): void
  {
    $log = new \App\Models\Log();
    // $log->userid = $GLOBALS['user_id'];
    $user = \App\Models\User::where('id', $GLOBALS['user_id'])->first();
    // Store the name in case the user account deleted later
    if (!is_null($user))
    {
      $log->user_name = $user->completename . ' (' . $GLOBALS['user_id'] . ')';
    }
    $log->item_type = $modelname;
    $log->item_id = $id;
    if (!is_null($old_value))
    {
      $log->old_value = $old_value;
    }
    $log->new_value = $new_value;
    $log->id_search_option = $idSearchOption;
    $log->save();
  }
}
