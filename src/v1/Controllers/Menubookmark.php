<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;
use stdClass;

final class Menubookmark extends Common
{
  protected $model = '\App\Models\Menubookmark';
  protected $rootUrl2 = '/menubookmarks/';
  protected $choose = 'menubookmarks';

  public function newItem(Request $request, Response $response, $args): Response
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
          $this->model::create($data);
        }
      }
    }

    return $response
      ->withHeader('Location', $request->getHeaderLine('Referer'));
  }

  public function deleteItem(Request $request, Response $response, $args): Response
  {
    $menu = \App\Models\Menubookmark::find($args['id']);
    if (!is_null($menu))
    {
      if ($menu->user_id == $GLOBALS['user_id'])
      {
        $menu->delete();
      }
    }
    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER']);
  }

  protected function canRightCreate(): bool
  {
    return true;
  }
}
