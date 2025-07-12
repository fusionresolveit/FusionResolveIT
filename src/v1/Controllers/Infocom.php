<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostInfocom;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Infocom extends Common
{
  /**
   * @param array<string, string> $args
   */
  public function saveItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostInfocom((object) $request->getParsedBody());

    if (!$this->canRightCreate())
    {
      // throw new \Exception('Unauthorized access', 401);
    }

    if (!is_null($data->item_type) && !is_null($data->item_id))
    {
      $infocom = \App\Models\Infocom::
          where('item_id', $data->item_id)
        ->where('item_type', $data->item_type)
        ->first();
      if (is_null($infocom))
      {
        // create it
        $infocom = new \App\Models\Infocom();
        if (!\App\v1\Controllers\Profile::canRightReadItem($infocom))
        {
          // throw new \Exception('Unauthorized access', 401);
        }
        $infocom->create($data->exportToArray());
        \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
        \App\v1\Controllers\Notification::prepareNotification($infocom, 'create');
      } else {
        if (!\App\v1\Controllers\Profile::canRightReadItem($infocom))
        {
          // throw new \Exception('Unauthorized access', 401);
        }

        $infocom->update($data->exportToArray());
        \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
        \App\v1\Controllers\Notification::prepareNotification($infocom, 'update');
      }
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
