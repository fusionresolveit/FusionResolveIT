<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait GetDropdownValues
{
  /**
   * @return array<int, mixed>
   */
  public function getDropdownValues(string|null $filter = null): array
  {
    $item = $this;
    $className = '\\' . get_class($this);
    $trees = [];
    if ($this->isTree())
    {
      $item = $item->orderBy('treepath');
    }

    $item->orderBy('name');
    if (!is_null($filter) && !empty($filter))
    {
      $item = $item->where('name', 'LIKE', '%' . $filter . '%');
      if (is_numeric($filter))
      {
        $item = $item->orWhere('id', 'LIKE', '%' . $filter . '%');
      }
    }

    $items = $item->take(50)->get();
    $data = [];
    foreach ($items as $myItem)
    {
      $name = '';
      if (isset($myItem->attributes['name']))
      {
        $name = $myItem->getAttribute('name');
        if ($name == '')
        {
          $name = $myItem->getAttribute('id');
        }
        elseif (is_numeric($filter))
        {
          $name .= ' - ' . $myItem->getAttribute('id');
        }
      }
      $class = '';
      if ($this->isTree())
      {
        $trees[$myItem->getAttribute('treepath')] = true;
        // $parents = $this->getParentLevelsForTree($myItem->treepath, $trees);

        $itemsId = str_split($myItem->getAttribute('treepath'), 5);
        array_pop($itemsId);
        foreach ($itemsId as $id)
        {
          $parentItem = $className::where('id', (int) $id)->first();
          if (is_null($parentItem))
          {
            throw new \Exception('Parent not found', 400);
          }
          if (!isset($trees[$parentItem->getAttribute('treepath')]))
          {
            $nb = strlen($parentItem->getAttribute('treepath')) / 5;
            $class = ' treelvl' . $nb;
            $data[] = [
              "name"  => $parentItem->name,
              "value" => $parentItem->id,
              "class" => 'item' . $class,
            ];
            $trees[$parentItem->getAttribute('treepath')] = true;
          }
        }
        $nb = strlen($myItem->getAttribute('treepath')) / 5;
        $class = ' treelvl' . $nb;
      }
      $data[] = [
        "name"  => $name,
        "value" => $myItem->getAttribute('id'),
        "class" => 'item' . $class,
      ];
    }
    return $data;
  }
}
