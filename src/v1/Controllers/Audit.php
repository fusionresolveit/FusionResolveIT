<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\Traits\ShowItem;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Audit extends Common
{
  // Display
  use ShowItem;

  protected $model = \App\Models\Audit::class;

  protected function instanciateModel(): \App\Models\Audit
  {
    return new \App\Models\Audit();
  }

  public static function addEntry(
    Request $request,
    string $action,
    string $message,
    string|null $model,
    int $itemId = 0,
    int $httpcode = 200,
    string|null $subaction = null
  ): void
  {
    $audit = new \App\Models\Audit();
    if (isset($GLOBALS['user_id']))
    {
      $audit->setAttribute('user_id', $GLOBALS['user_id']);
      $user = \App\Models\User::where('id', $GLOBALS['user_id'])->first();
      // Store the name in case the user account deleted later
      if (!is_null($user))
      {
        $audit->setAttribute('username', $user->completename);
      }
    }
    $audit->setAttribute('ip', $request->getServerParams()['REMOTE_ADDR']);
    $audit->setAttribute('endpoint', $request->getUri()->getPath());
    $audit->setAttribute('httpmethod', $request->getMethod());
    $audit->setAttribute('httpcode', $httpcode);
    $audit->setAttribute('action', $action);
    $audit->setAttribute('subaction', $subaction);
    $audit->setAttribute('item_type', $model);
    $audit->setAttribute('item_id', $itemId);
    $audit->setAttribute('message', $message);
    $audit->save();
  }
}
