<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;
use Illuminate\Database\Eloquent\Builder;

final class Search extends Common
{
  /**
   * @template C of \App\Models\Common
   * @param C $item
   * @param array<mixed> $filters
   * @return array<mixed>
   */
  public function getData($item, string $uri, int $page = 1, array $filters = []): array
  {
    $itemtype = get_class($item);
    $prefs = \App\Models\Displaypreference::getForTypeUser($itemtype, $GLOBALS['user_id']);
    $itemDef = $item->getDefinitions();
    $where = $this->manageFilters($itemDef, $filters);

    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', $itemtype)
      ->first();
    if (is_null($profileright))
    {
      return [];
    }

    // echo "<pre>";
    // print_r($itemDef);
    // echo "</pre>";
    // die();

    // columns
    $newItemDef = [];
    foreach ($itemDef as $field)
    {
      if (is_null($field->display) || $field->display === false)
      {
        continue;
      }
      if (isset($prefs[$field->id]))
      {
        $newItemDef[] = $field;
      }
      // add name field if not in preferences
      if ($field->name == 'name' && !isset($prefs[$field->id]))
      {
        $newItemDef[] = $field;
      }
      // add name field if not in preferences
      if ($field->name == 'completename' && !isset($prefs[$field->id]))
      {
        $newItemDef[] = $field;
      }
      // Same for entity
      if ($GLOBALS['entity_recursive'] && $field->name == 'entity' && !isset($prefs[$field->id]))
      {
        $newItemDef[] = $field;
      }
    }
    $newItemDef = $this->orderColumns($newItemDef, $prefs);

    $start = 0;
    $limit = 15;
    $start = ($page - 1) * $limit;

    // Apply filters
    foreach ($where as $key => $value)
    {
      if (is_array($value))
      {
        $item = $item->where($key, $value[0], $value[1]);
      }
      else
      {
        $item = $item->where($key, $value);
      }
    }
    // echo "<pre>";
    // print_r($item);
    // echo "</pre>";
    // die();

    if (get_class($item) == \App\Models\Ticket::class)
    {
      if ($profileright->readmyitems && !$profileright->read)
      {
        $item = $item->where('user_id_recipient', $GLOBALS['user_id']);
      }
      $item = $item->orderBy('id', 'desc');
    }

    if (method_exists($item, 'entity'))
    {
      $item = $item->with('entity')->whereHas('entity', function (Builder $query)
      {
        if ($GLOBALS['entity_recursive'])
        {
          // @phpstan-ignore argument.type
          $query->where('treepath', 'LIKE', $GLOBALS['entity_treepath'] . '%');
        }
        else
        {
          // @phpstan-ignore argument.type
          $query->where('treepath', $GLOBALS['entity_treepath']);
        }
      });
    }

    $cnt = $item->count();

    $items = $item->offset($start)->take($limit)->get();

    // echo "<pre>";
    // print_r($items);
    // echo "</pre>";
    // die();

    $itemDbData = $this->prepareValues($newItemDef, $items, $uri);

    // echo "<pre>";
    // print_r($itemDbData);
    // echo "</pre>";
    // die();

    $itemDbData['paging'] = [
      'total'     => $cnt,
      'pages'     => ceil($cnt / $limit),
      'current'   => $page,
      'linkpage'  => $uri . '?page=',
    ];
    return $itemDbData;
  }

  /**
   * @template C of \App\Models\Common
   * @param array<Definition> $itemDef
   * @param \Illuminate\Database\Eloquent\Collection<int, C> $data
   * @return array<mixed>
   */
  private function prepareValues(array $itemDef, $data, string $uri): array
  {
    $header = ['id'];
    foreach ($itemDef as $field)
    {
      $header[] = $field->title;
    }

    $allData = [];
    foreach ($data as $item)
    {
      $myData = [];
      $myData['id'] = [
        'value' => $item->getAttribute('id'),
        'link'  => $uri . '/' . $item->getAttribute('id'),
      ];

      foreach ($itemDef as $field)
      {
        if ($field->type == 'dropdown_remote')
        {
          if (is_null($item->{$field->name}) || empty($item->{$field->name}))
          {
            $myData[$field->name] = [
              'value' => '',
            ];
          }
          else
          {
            // if (!is_null($field->count))
            // {
            //   $elements = [];
            //   foreach ($item->{$field->name} as $t)
            //   {
            //     $elements[] = $t->{$field->count};
            //   }
            //   $myData[$field->name] = [
            //     'value' => array_sum($elements),
            //   ];
            // }
            // elseif (!is_null($field->multiple))
            if (!is_null($field->multiple))
            {
              $elements = [];
              foreach ($item->{$field->name} as $t)
              {
                $elements[] = $t->name;
              }
              $myData[$field->name] = [
                'value' => implode(', ', $elements),
              ];
            }
            else
            {
              $myData[$field->name] = [
                'value' => $item->{$field->name}->name,
              ];
            }
          }
        }
        elseif ($field->type == 'dropdown')
        {
          if (!is_null($field->values[$item->{$field->name}]))
          {
            $myData[$field->name] = [
              'value' => $field->values[$item->{$field->name}],
            ];
          }
          else
          {
            $myData[$field->name] = [];
          }
        }
        elseif ($field->type == 'boolean')
        {
          $myData[$field->name] = [
            'value' => boolval($item->{$field->name}),
          ];
        }
        else
        {
          $myData[$field->name] = [
            'value' => $item->{$field->name},
          ];
        }
      }
      $allData[] = $myData;
    }
    return [
      'header'      => $header,
      'data'        => $allData,
      'allFields'   => $itemDef,
    ];
  }

  /**
   * @param array<mixed> $params
   * @return array<mixed>
   */
  private function manageFilters(DefinitionCollection $itemDef, array $params): array
  {
    if (isset($params['field']) && is_numeric($params['field']))
    {
      foreach ($itemDef as $field)
      {
        if ($field->id == $params['field'])
        {
          if ($field->type == 'dropdown_remote')
          {
            return [$field->name => $params['value']];
          }
          else
          {
            return [$field->name => ['LIKE', '%' . $params['value'] . '%']];
          }
        }
      }
    }
    return [];
  }

  /**
   * @param array<Definition> $itemDef
   * @param array<mixed> $prefs
   *
   * @return array<Definition>
   */
  private function orderColumns(array $itemDef, array $prefs): array
  {
    $orderedItemDef = [];
    foreach ($prefs as $id)
    {
      foreach ($itemDef as $field)
      {
        if ($field->id == $id)
        {
          $orderedItemDef[] = $field;
          continue;
        }
      }
    }
    return $orderedItemDef;
  }
}
