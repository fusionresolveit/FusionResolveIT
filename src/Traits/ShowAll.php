<?php

declare(strict_types=1);

namespace App\Traits;

use App\DataInterface\DefinitionCollection;
use App\DataInterface\Definition;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait ShowAll
{
  /**
   * @param array<string, string> $args
   */
  public function showAll(Request $request, Response $response, array $args): Response
  {
    $params = $request->getQueryParams();
    $view = Twig::fromRequest($request);

    $item = $this->instanciateModel();

    $page = 1;
    if (!$this->canRightRead())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $url = $this->getUrlWithoutQuery($request);
    if (isset($params['page']) && is_numeric($params['page']))
    {
      $page = (int) $params['page'];
    }

    $fields = [];
    $items = [];
    $prefs = \App\Models\Displaypreference::getForTypeUser($this->model, $GLOBALS['user_id']);
    $itemDef = $item->getDefinitions();
    $where = $this->manageFilters($itemDef, $params);

    // Manage trashed
    if (isset($params['trash']))
    {
      $item = $item->onlyTrashed();
    }

    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', $this->model)
      ->first();
    if (!is_null($profileright))
    {
      // columns
      $newItemDef = [];
      foreach ($itemDef as $field)
      {
        if (is_null($field->display) || $field->display === false)
        {
          continue;
        }

        if (in_array($field->id, $prefs))
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

      // Apply params
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

      if (get_class($item) == \App\Models\Ticket::class)
      {
        if ($profileright->readmyitems && !$profileright->read)
        {
          $item = $item->where('user_id_recipient', $GLOBALS['user_id']);
        }
        $item = $item->orderBy('id', 'desc');
      }

      if ($this->instanciateModel()->isEntity())
      {
        $item = $item->with('entity')->whereHas('entity', function ($query)
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

      $fields = $this->prepareValues($newItemDef, $items, $url);

      $fields['paging'] = [
        'total'     => $cnt,
        'pages'     => ceil($cnt / $limit),
        'current'   => $page,
        'linkpage'  => $url . '?page=',
      ];
    }

    $item = $this->instanciateModel();
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);
    $viewData->addHeaderTitle('Fusion Resolve IT - ' . $item->getTitle(2));
    if (isset($params['trash']))
    {
      $viewData->addHeaderTrashed();
      $viewData->addHeaderColor('red');
    }
    $viewData->addData('url', $url);

    $viewData->addData('fields', $fields);

    $viewData->addData('definition', $item->getDefinitions());
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    return $view->render($response, 'search.html.twig', (array)$viewData);
  }

  /**
   * @template TModel of \Illuminate\Database\Eloquent\Model
   * @param array<Definition> $itemDef
   * @param \Illuminate\Database\Eloquent\Collection<int, TModel> $data
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
            if ($field->multiple === true)
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
