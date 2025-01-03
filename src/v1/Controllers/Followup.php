<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Followup extends Common
{
  protected $model = '\App\Models\Followup';

  public function postItem(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();
    // TODO if not have $data->followup, error


    $item = new \App\Models\Followup();
    $item->item_type = $data->item_type;
    $item->item_id = $data->item_id;
    $item->content = $data->followup;
    if (property_exists($data, 'private') && $data->private == 'on')
    {
      $item->is_private = true;
    }
    if ($this->canRightReadPrivateItem())
    {
      $item->is_tech = true;
    }
    $item->user_id = $GLOBALS['user_id'];

    $item->save();

    $relatedItem = $data->item_type::find($data->item_id);

    // Manage time
    if (property_exists($data, 'time'))
    {
      $time = (int) $data->time;
      if ($time > 0)
      {
        if ($data->timetype == 'minutes')
        {
          $time = $time * 60;
        }
        if ($data->timetype == 'hours')
        {
          $time = $time * 3600;
        }
        $relatedItem->actiontime = $relatedItem->actiontime + $time;
        $relatedItem->save();
      }
    }
    $relatedItem->touch();
    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The followup has been added successfully');

    header('Location: ' . $data->redirect);
    exit();
  }

  public function canRightReadPrivateItem(): bool
  {
    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->readprivateitems)
    {
      return true;
    }
    return false;
  }
}
