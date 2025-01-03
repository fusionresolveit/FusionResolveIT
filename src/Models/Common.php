<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelPivotEvents\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Common extends Model
{
  use PivotEventTrait;

  /** @var string|null */
  protected $definition = null;

  /** @var string[] */
  protected $titles = ['not defined', 'not defined'];

  /** @var string */
  protected $icon = '';

  /** @var string|null */
  protected $table = null;

  /** @var boolean */
  protected $hasEntityField = true;

  /** @var boolean */
  protected $tree = false;

  /** @var string[] */
  protected $dispatchesEvents = [
    'creating' => \App\Events\EntityCreating::class,
    'created'  => \App\Events\TreepathCreated::class,
  ];

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);

    // Set fillable variable
    if (!is_null($this->definition) && empty($this->fillable))
    {
      $definitions = $this->getDefinitions(true);
      foreach ($definitions as $definition)
      {
        if (isset($definition['fillable']) && $definition['fillable'])
        {
          if (isset($definition['dbname']))
          {
            $this->fillable[] = $definition['dbname'];
          }
          else
          {
            $this->fillable[] = $definition['name'];
          }
        }
      }
    }
  }

  protected static function booted(): void
  {
    parent::booted();

    static::updated(function ($model)
    {
      if (get_class($model) != 'App\Models\Log')
      {
        $model->changesOnUpdated();
      }
    });

    static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes)
    {
      $model->changesOnPivotUpdated($relationName, $pivotIds, 'add');
    });

    static::pivotDetached(function ($model, $relationName, $pivotIds)
    {
      $model->changesOnPivotUpdated($relationName, $pivotIds, 'delete');
    });
  }

  /**
   * @param $nb int number of elements
   */
  public function getTitle($nb = 1): string
  {
    global $translator;

    return $translator->translatePlural($this->titles[0], $this->titles[1], $nb);
  }

  public function getIcon(): string
  {
    return $this->icon;
  }

  public function getDropdownValues($filter = null): array
  {
    $item = $this;
    $className = '\\' . get_class($this);
    $trees = [];
    if ($this->tree)
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
    foreach ($items as $item)
    {
      $name = '';
      if (property_exists($item, 'name'))
      {
        $name = $item->name;
        if ($item->name == '')
        {
          $name = $item->id;
        }
        elseif (is_numeric($filter))
        {
          $name .= ' - ' . $item->id;
        }
      }
      $class = '';
      if ($this->isTree() && property_exists($item, 'treepath'))
      {
        $trees[$item->treepath] = true;
        // $parents = $this->getParentLevelsForTree($item->treepath, $trees);

        $itemsId = str_split($item->treepath, 5);
        array_pop($itemsId);
        foreach ($itemsId as $id)
        {
          $parentItem = $className::find((int) $id);
          if (!isset($trees[$parentItem->treepath]))
          {
            $nb = strlen($parentItem->treepath) / 5;
            $class = ' treelvl' . $nb;
            $data[] = [
              "name"  => $parentItem->name,
              "value" => $parentItem->id,
              "class" => 'item' . $class,
            ];
            $trees[$parentItem->treepath] = true;
          }
        }
        $nb = strlen($item->treepath) / 5;
        $class = ' treelvl' . $nb;
      }
      $data[] = [
        "name"  => $name,
        "value" => $item->id,
        "class" => 'item' . $class,
      ];
    }
    return $data;
  }

  /**
   * Get definition fields of model
   * @param $bypassRights  boolean  Set true is not want manage rights (only on some features like notifications)
   * @param $usein  string=search|form|notification|rule  Force to get only definition for this part of the app
   */
  public function getDefinitions($bypassRights = false, $usein = null): array
  {
    if (is_null($this->definition))
    {
      return [];
    }

    $definitions = call_user_func($this->definition . '::getDefinition');
    if (get_class($this) == 'App\\Models\\Profileright') // || get_class($this) == 'App\\Models\\Profile')
    {
      return $definitions;
    }

    // manage usein
    if (!is_null($usein))
    {
      $newDefinitions = [];
      foreach ($definitions as $def)
      {
        if (!isset($def['usein']))
        {
          $newDefinitions[] = $def;
        }
        elseif (isset($def['usein'][$usein]))
        {
          $newDefinitions[] = $def;
        }
      }
      $definitions = $newDefinitions;
    }

    if ($bypassRights)
    {
      return $definitions;
    }
    $canOnlyReadItem = $this->canOnlyReadItem();

    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', get_class($this))
      ->first();
    if (is_null($profileright))
    {
      return [];
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        $ids[$custom->definitionfield_id] = [
          'read'  => $custom->read,
          'write' => $custom->write,
        ];
      }
      foreach ($definitions as &$def)
      {
        if (isset($ids[$def["id"]]))
        {
          if (!isset($def['display']) || $def['display'])
          {
            $def['display'] = $ids[$def["id"]]['read'];
          }
          if (!$ids[$def["id"]]['write'] || $canOnlyReadItem)
          {
            $def['readonly'] = 'readonly';
          }
        }
      }
      return $definitions;
    }
    if ($profileright->read || $profileright->readmyitems || $profileright->readmygroupitems)
    {
      foreach ($definitions as &$def)
      {
        if (!isset($def['display']))
        {
          $def['display'] = true;
        }
        if (!$profileright->update || $canOnlyReadItem)
        {
          $def['readonly'] = 'readonly';
        }
      }
      return $definitions;
    }
    return [];
  }

  public function getRelatedPages($rootUrl): array
  {
    global $translator;

    if (is_null($this->definition) || !method_exists($this->definition, 'getRelatedPages'))
    {
      return [];
    }
    $pages = call_user_func($this->definition . '::getRelatedPages', $rootUrl);
    $listUrl = preg_replace('/\/(\d+)$/', '', $rootUrl);
    foreach ($pages as $idx => $page)
    {
      if (isset($page['rightModel']))
      {
        $item = new $page['rightModel']();
        if (!\App\v1\Controllers\Profile::canRightReadItem($item))
        {
          unset($pages[$idx]);
        }
      }
    }

    array_unshift($pages, [
      'title' => $translator->translate('Back to list'),
      'icon' => 'stream',
      'link' => $listUrl,
    ]);

    return $pages;
  }

  public function getSpecificFunction($functionName): array
  {
    if (is_null($this->definition) || !method_exists($this->definition, $functionName))
    {
      return [];
    }
    return call_user_func($this->definition . '::' . $functionName);
  }

  /**
   * Get form data for this item
   *
   * @return array
   */
  public function getFormData($myItem, $otherDefs = false): array
  {
    if ($otherDefs !== false)
    {
      $def = $otherDefs;
    }
    else
    {
      $def = $myItem->getDefinitions();
    }
    if ($myItem == null)
    {
      return $def;
    }

    foreach ($def as $idx => &$field)
    {
      // Special case for entity, must not displayed in forms
      if ($field['name'] == 'entity' && get_class($myItem) !== 'App\Models\Entity')
      {
        unset($def[$idx]);
        continue;
      }
      if (isset($field['display']) && $field['display'] == false)
      {
        unset($def[$idx]);
        continue;
      }
      if ($field['type'] == 'dropdown_remote')
      {
        if (is_null($myItem->{$field['name']}) || $myItem->{$field['name']} == false)
        {
          $field['value'] = 0;
          $field['valuename'] = '';
        }
        elseif (isset($field['multiple']))
        {
          // if ($field['name'] == 'requester')
          // {
          // print_r($myItem->{$field['name']});
          // }
          // TODO manage multiple select
          $values = [];
          $valuenames = [];
          foreach ($myItem->{$field['name']} as $val)
          {
            $values[] = $val->id;
            $valuenames[] = $val->name;
          }
          $field['value'] = implode(',', $values);
          $field['valuename'] = implode(',', $valuenames);
        }
        else
        {
          // var_dump($field['name']);                   // #EB
          // var_dump($myItem->{$field['name']}->id);    // #EB

          $field['value'] = $myItem->{$field['name']}->id;
          $field['valuename'] = $myItem->{$field['name']}->name;
        }
      }
      elseif ($field['type'] == 'textarea')
      {
        if (is_null($myItem->{$field['name']}))
        {
          $field['value'] = '';
        }
        else
        {
          // We convert html to markdown
          $field['value'] = \App\v1\Controllers\Toolbox::convertHtmlToMarkdown($myItem->{$field['name']});
        }
      }
      else
      {
        $field['value'] = $myItem->{$field['name']};
      }
      if (isset($field['readonly']))
      {
        $field['readonly'] = 'readonly';
      }
    }
    return $def;
  }


  /**
   * Add in changes when update fields
   */
  public function changesOnUpdated(): void
  {
    $changes = $this->getChanges();
    $casts = $this->getCasts();

    foreach ($changes as $key => $newValue)
    {
      if (in_array($key, ['created_at', 'updated_at']))
      {
        continue;
      }
      $oldValue = $this->original[$key];
      if (isset($casts[$key]) && $casts[$key] == 'boolean')
      {
        $newValue = (boolval($newValue) ? 'true' : 'false');
        $oldValue = (boolval($oldValue) ? 'true' : 'false');
      }
      // TODO for textarea
      if (
          (!is_null($newValue) && is_string($newValue) && strlen($newValue) >= 255) ||
          (!is_null($oldValue) && is_string($oldValue) && strlen($oldValue) >= 255)
      )
      {
        return;
      }

      // get the id_search_option
      $definitions = $this->getDefinitions();
      $idSearchOption = 0;
      foreach ($definitions as $definition)
      {
        if ($definition['name'] == $key)
        {
          $idSearchOption = $definition['id'];
          break;
        }
        elseif (isset($definition['dbname']) && $definition['dbname'] == $key)
        {
          $idSearchOption = $definition['id'];
          break;
        }
      }
      \App\v1\Controllers\Log::addEntry(
        $this,
        '{username} changed ' . $key . ' to "{new_value}"',
        $newValue,
        $oldValue,
        $idSearchOption,
      );
    }
  }

  /**
   * Add in changes when update fields
   * @param $name string  name of the field (=name in definition)
   */
  public function changesOnPivotUpdated($name, $pivotIds, $type = 'add'): void
  {
    return;
    // get the id_search_option
    // $definitions = $this->getDefinitions();
    // $idSearchOption = 0;
    // $title = '';
    // $item = new stdClass();
    // foreach ($definitions as $definition)
    // {
    //   if ($definition['name'] == $name)
    //   {
    //     $idSearchOption = $definition['id'];
    //     $title = $definition['title'];
    //     $item = new $definition['itemtype']();
    //     break;
    //   }
    // }
    // if ($type == 'add')
    // {
    //   foreach ($pivotIds as $id)
    //   {
    //     $myItem = $item->find($id);
    //     \App\v1\Controllers\Log::addEntry(
    //       $this,
    //       '{username} Add ' . $title . ' to "{new_value}"',
    //       $myItem->name,
    //       null,
    //       $idSearchOption,
    //     );
    //   }
    // }
    // if ($type == 'delete')
    // {
    //   foreach ($pivotIds as $id)
    //   {
    //     $myItem = $item->find($id);
    //     \App\v1\Controllers\Log::addEntry(
    //       $this,
    //       '{username} delete ' . $title . ' to "{new_value}"',
    //       null,
    //       $myItem->name,
    //       $idSearchOption,
    //     );
    //   }
    // }
  }

  public function isEntity(): bool
  {
    if ($this->hasEntityField)
    {
      return true;
    }
    return false;
  }

  public function canOnlyReadItem(): bool
  {
    return false;
  }

  public function isTree(): bool
  {
    return $this->tree;
  }
}
