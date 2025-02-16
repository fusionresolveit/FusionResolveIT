<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Menubookmark extends Common
{
  protected $model = \App\Models\Menubookmark::class;
  protected $rootUrl2 = '/menubookmarks/';
  protected $choose = 'menubookmarks';

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    // check if endpoint and have access
    if (isset($args['endpoint']))
    {
      $menu = new \App\v1\Controllers\Menu();
      $items = $menu->getMenu($request);
      $found = false;
      foreach ($items as $item)
      {
        foreach ($item['sub'] as $subitem)
        {
          if ($subitem['endpoint'] == $args['endpoint'])
          {
            $found = true;
            break;
          }
        }
      }
      if ($found)
      {
        $item = \App\Models\Menubookmark::
            where('endpoint', $args['endpoint'])
          ->where('user_id', $GLOBALS['user_id'])
          ->first();
        if (is_null($item))
        {
          $data = [
            'endpoint' => $args['endpoint'],
            'user_id'  => $GLOBALS['user_id'],
          ];
          \App\Models\Menubookmark::create($data);
        }
      }
    }

    return $response
      ->withHeader('Location', $request->getHeaderLine('Referer'));
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteItem(Request $request, Response $response, array $args): Response
  {
    $menu = \App\Models\Menubookmark::where('id', $args['id'])->first();
    if (!is_null($menu))
    {
      if ($menu->user_id == $GLOBALS['user_id'])
      {
        $menu->delete();
      }
    }
    return $this->goBack($response);
  }

  protected function canRightCreate(): bool
  {
    return true;
  }
}
