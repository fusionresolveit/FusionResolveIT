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
    $action,
    $message,
    $model,
    $itemId = 0,
    $httpcode = 200,
    $subaction = null
  )
  {
    $audit = new \App\Models\Audit();
    if (isset($GLOBALS['user_id']) && !is_null($GLOBALS['user_id']))
    {
      $audit->userid = $GLOBALS['user_id'];
      $user = \App\Models\User::where('id', $GLOBALS['user_id'])->first();
      // Store the name in case the user account deleted later
      if (!is_null($user))
      {
        $audit->username = $user->completename;
      }
    }
    $audit->ip = $request->getServerParams()['REMOTE_ADDR'];
    $audit->endpoint = $request->getUri()->getPath();
    $audit->httpmethod = $request->getMethod();
    $audit->httpcode = $httpcode;
    $audit->action = $action;
    $audit->subaction = $subaction;
    $audit->item_type = $model;
    $audit->item_id = $itemId;
    $audit->message = $message;
    $audit->save();
  }
}
