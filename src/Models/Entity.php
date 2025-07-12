<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Entity extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowledgebasearticles;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Entity::class;
  protected $icon = 'layer group';
  protected $hasEntityField = false;
  protected $tree = true;
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'notes',
    'knowledgebasearticles',
  ];

  protected $appends = [
    // 'completename',
  ];

  protected $visible = [
    'notes',
    'knowledgebasearticles',
    'documents',
    'completename',
  ];

  protected $with = [
    'entity:id,name,completename',
    'notes:id',
    'knowledgebasearticles:id,name',
    'documents:id,name',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Entity', 'Entities', $nb);
  }

  public function getCompletenameAttribute(): string
  {
    $names = [];
    if ($this->treepath != null)
    {
      $itemsId = str_split($this->treepath, 5);
      array_pop($itemsId);
      foreach ($itemsId as $key => $value)
      {
        $itemsId[$key] = (int) $value;
      }
      $items = \App\Models\Entity::whereIn('id', $itemsId)->orderBy('treepath')->get();
      foreach ($items as $item)
      {
        $names[] = $item->name;
      }
    }
    $names[] = $this->name;
    return implode(' > ', $names);
  }
}
