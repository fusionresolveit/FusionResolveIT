<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSolution;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Solution extends Common
{
  protected $model = \App\Models\Solution::class;

  /**
   * @param array<string, string> $args
   */
  public function postItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSolution((object) $request->getParsedBody());

    $item = new \App\Models\Solution();
    if (
        !is_null($data->solution) &&
        !is_null($data->item_id) &&
        !is_null($data->item_type)
    )
    {
      $item->item_type = $data->item_type;
      $item->item_id = $data->item_id;
      $item->content = $data->solution;
      $item->user_id = $GLOBALS['user_id'];
      $item->status = 2;

      $item->save();

      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage('The solution has been added successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function postAccept(Request $request, Response $response, array $args): Response
  {
    $item = \App\Models\Solution::where('id', $args['solutionid'])->first();
    if (is_null($item))
    {
      throw new \Exception('Id not found', 404);
    }
    $item->status = 3;
    $item->date_approval = date('Y-m-d H:i:s');
    $item->user_id_approval = $GLOBALS['user_id'];
    $item->user_name_approval = $GLOBALS['username'];
    $item->save();

    if ($item->item_type == 'App\Models\Ticket')
    {
      $ticket = \App\Models\Ticket::where('id', $item->item_id)->first();
      if (is_null($ticket))
      {
        throw new \Exception('Id not found', 404);
      }

      $ticket->status = 6;
      $ticket->save();
    }
    return $this->goBack($response);
  }

  /**
   * @param array<string, string> $args
   */
  public function postRefuse(Request $request, Response $response, array $args): Response
  {
    $item = \App\Models\Solution::where('id', $args['solutionid'])->first();
    if (is_null($item))
    {
      throw new \Exception('Id not found', 404);
    }
    $item->status = 4;
    $item->date_approval = date('Y-m-d H:i:s');
    $item->user_id_approval = $GLOBALS['user_id'];
    $item->user_name_approval = $GLOBALS['username'];
    $item->save();

    if ($item->item_type == 'App\Models\Ticket')
    {
      $ticket = \App\Models\Ticket::where('id', $item->item_id)->first();
      if (is_null($ticket))
      {
        throw new \Exception('Id not found', 404);
      }

      $ticket->status = 2;
      $ticket->save();
    }
    return $this->goBack($response);
  }
}
