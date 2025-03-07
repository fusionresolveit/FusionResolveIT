<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelPivotEvents\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model;
use App\DataInterface\DefinitionCollection;

abstract class Common extends Model
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
    'updating' => \App\Events\TreepathUpdating::class,
    'created'  => \App\Events\TreepathCreated::class,
  ];

  /**
   * @param array<mixed> $attributes
   */
  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);

    // Set fillable variable
    if (!is_null($this->definition) && empty($this->fillable))
    {
      $definitions = $this->getDefinitions(true);
      foreach ($definitions as $definition)
      {
        if ($definition->fillable)
        {
          if (!is_null($definition->dbname))
          {
            $this->fillable[] = $definition->dbname;
          } else {
            $this->fillable[] = $definition->name;
          }
        }
      }
    }
  }

  // protected static function booted(): void
  // {
  //   parent::booted();

  //   static::updated(function ($model)
  //   {
  //     if (get_class($model) != 'App\Models\Log')
  //     {
  //       $model->changesOnUpdated();
  //     }
  //   });

  //   static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes)
  //   {
  //     $model->changesOnPivotUpdated($relationName, $pivotIds, 'add');
  //   });

  //   static::pivotDetached(function ($model, $relationName, $pivotIds)
  //   {
  //     $model->changesOnPivotUpdated($relationName, $pivotIds, 'delete');
  //   });
  // }

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    global $translator;

    return $translator->translatePlural($this->titles[0], $this->titles[1], $nb);
  }

  public function getIcon(): string
  {
    return $this->icon;
  }

  /**
   * Get definition fields of model
   *
   * @param $bypassRights  boolean  Set true is not want manage rights (only on some features like notifications)
   * @param $usein  string=search|form|notification|rule  Force to get only definition for this part of the app
   */
  public function getDefinitions(bool $bypassRights = false, string|null $usein = null): DefinitionCollection
  {
    if (is_null($this->definition))
    {
      return new DefinitionCollection();
    }

    $definitions = $this->definition::getDefinition();
    if (get_class($this) == \App\Models\Profileright::class) // || get_class($this) == 'App\\Models\\Profile')
    {
      return $definitions;
    }

    // manage usein
    if (!is_null($usein))
    {
      $newDefinitions = new DefinitionCollection();
      foreach ($definitions as $def)
      {
        if (!isset($def['usein']))
        {
          $newDefinitions->add($def);
        }
        elseif (isset($def['usein'][$usein]))
        {
          $newDefinitions->add($def);
        }
      }
      $definitions = $newDefinitions;
    }

    if ($bypassRights)
    {
      return $definitions;
    }
    $canOnlyReadItem = $this->canOnlyReadItem();

    $profileright = \App\Models\Profileright::where('profile_id', $GLOBALS['profile_id'])
      ->where('model', get_class($this))
      ->first();
    if (is_null($profileright))
    {
      return new DefinitionCollection();
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
        if (isset($ids[$def->id]))
        {
          if ($def->display)
          {
            $def->display = $ids[$def->id]['read'];
          }
          if (!$ids[$def->id]['write'] || $canOnlyReadItem)
          {
            $def->readonly = true;
          }
        }
      }
      return $definitions;
    }
    if ($profileright->read || $profileright->readmyitems || $profileright->readmygroupitems)
    {
      foreach ($definitions as &$def)
      {
        if (is_null($def->display))
        {
          $def->display = true;
        }
        if (!$profileright->update || $canOnlyReadItem)
        {
          $def->readonly = true;
        }
      }
      return $definitions;
    }
    return new DefinitionCollection();
  }

  /**
   * @return array<int, mixed>
   */
  public function getRelatedPages(string $rootUrl): array
  {
    global $translator;

    if (is_null($this->definition) || !method_exists($this->definition, 'getRelatedPages'))
    {
      return [];
    }
    $pages = $this->definition::getRelatedPages($rootUrl);
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

  public function getSpecificFunction(string $functionName): DefinitionCollection|false
  {
    if (
        is_null($this->definition) ||
        !class_exists($this->definition) ||
        !method_exists($this->definition, $functionName)
    )
    {
      return new DefinitionCollection();
    }
    $item = new $this->definition();
    $ret = $item->$functionName();
    if ($ret instanceof DefinitionCollection)
    {
      return $ret;
    }
    return new DefinitionCollection();
  }

  /**
   * @template C of \App\Models\Common
   * @param C|null $myItem
   *
   * Get form data for this item
   */
  public function getFormData($myItem, DefinitionCollection|false $otherDefs = false): DefinitionCollection
  {
    if ($myItem == null)
    {
      throw new \Exception('Error get form data', 500);
    }
    if ($otherDefs !== false)
    {
      $def = $otherDefs;
    } else {
      $def = $myItem->getDefinitions();
    }

    foreach ($def as $idx => &$field)
    {
      // Special case for entity, must not displayed in forms
      if ($field->name == 'entity' && get_class($myItem) !== 'App\Models\Entity')
      {
        $def->remove($field);
        continue;
      }
      if (!is_null($field->display) && $field->display == false)
      {
        $def->remove($field);
        continue;
      }
      $myItemFieldValue = '';
      if ($field->isPivot)
      {
        if ($field->type == 'dropdown_remote' && !is_null($field->dbname) && !is_null($field->itemtype))
        {
          $modelName = $field->itemtype;
          $pivotItem = $modelName::where('id', $myItem->getRelationValue('pivot')->{$field->dbname})->first();
          if (!is_null($pivotItem))
          {
            $myItemFieldValue = $pivotItem;
          }
        } else {
          $myItemFieldValue = $myItem->getRelationValue('pivot')->{$field->name};
        }
      } else {
        $myItemFieldValue = $myItem->{$field->name};
      }

      if ($field->type == 'dropdown_remote')
      {
        if (is_null($myItemFieldValue) || $myItemFieldValue == false)
        {
          $field->value = 0;
          $field->valuename = '';
        }
        elseif (!is_null($field->multiple) && $field->multiple)
        {
          // if ($field['name'] == 'requester')
          // {
          // print_r($myItem->{$field['name']});
          // }
          // TODO manage multiple select
          $values = [];
          $valuenames = [];
          foreach ($myItemFieldValue as $val)
          {
            $values[] = $val->id;
            $valuenames[] = $val->name;
          }
          $field->value = implode(',', $values);
          $field->valuename = implode(',', $valuenames);
        } else {
          if ($field->name == 'id')
          {
            $field->value = $myItem->getAttribute('id');
            $field->valuename = $myItem->getAttribute('name');
          } else {
            $field->value = $myItemFieldValue->id;
            $field->valuename = $myItemFieldValue->name;
          }
        }
      }
      elseif ($field->type == 'textarea')
      {
        if (is_null($myItemFieldValue))
        {
          $field->value = '';
        } else {
          // We convert html to markdown
          $field->value = \App\v1\Controllers\Toolbox::convertHtmlToMarkdown($myItemFieldValue);
        }
      } else {
        $field->value = $myItemFieldValue;
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
        if ($definition->name == $key)
        {
          $idSearchOption = $definition->id;
          break;
        }
        elseif (!is_null($definition->dbname) && $definition->dbname == $key)
        {
          $idSearchOption = $definition->id;
          break;
        }
      }
      \App\v1\Controllers\Log::addEntry(
        get_class($this),
        $this->getAttribute('id'),
        '{username} changed ' . $key . ' to "{new_value}"',
        $newValue,
        $oldValue,
        $idSearchOption,
      );
    }
  }

  /**
   * Add in changes when update fields
   *
   * @param array<int> $pivotIds
   */
  public function changesOnPivotUpdated(string $name, array $pivotIds, string $type = 'add'): void
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
    //     $myItem = $item->where('id', $id)->first();
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
    //     $myItem = $item->where('id', $id)->first();
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
