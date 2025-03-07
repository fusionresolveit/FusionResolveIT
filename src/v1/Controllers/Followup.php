<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostFollowup;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Followup extends Common
{
  protected $model = \App\Models\Followup::class;

  /**
   * @param array<string, string> $args
   */
  public function postItem(Request $request, Response $response, array $args): Response
  {
    $requestData = (object) $request->getParsedBody();
    $data = new PostFollowup($requestData);

    $followup = new \App\Models\Followup();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($followup))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $fData = $data->exportToArray();
    if ($this->canRightReadPrivateItem())
    {
      $fData['is_tech'] = true;
    }
    $fData['user_id'] = $GLOBALS['user_id'];

    if (!isset($requestData->item_type) || !isset($requestData->item_id))
    {
      throw new \Exception('Wrong data request', 400);
    }

    $followup = \App\Models\Followup::create($fData);

    // Manage time
    if (property_exists($requestData, 'time') && property_exists($requestData, 'timetype'))
    {
      $relatedItem = $requestData->item_type::where('id', $requestData->item_id)->first();
      if (is_null($relatedItem))
      {
        throw new \Exception('Id not found', 404);
      }
  
      $time = (int) $requestData->time;
      if ($time > 0)
      {
        if ($requestData->timetype == 'minutes')
        {
          $time = $time * 60;
        }
        if ($requestData->timetype == 'hours')
        {
          $time = $time * 3600;
        }
        $relatedItem->actiontime = $relatedItem->actiontime + $time;
        $relatedItem->save();
      }
      $relatedItem->touch();
    }
    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The followup has been added successfully');

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
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
