<?php

declare(strict_types=1);

namespace App\Models\Rules;

use App\DataInterface\Definition;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rulecriterium extends \App\Models\Common
{
  protected $definition = \App\Models\Definitions\Rulecriterium::class;
  protected $titles = ['Criterium', 'Criteria'];
  protected $icon = 'magic';
  protected $hasEntityField = false;
  public $timestamps = false;

  protected $appends = [
    'patternviewfield',
  ];

  protected $visible = [
    'rule',
    'patternviewfield',
  ];

  public function getCriteriaAttribute(): Definition
  {
    $rule = \App\Models\Rules\Rule::where('id', $this->attributes['rule_id'])->first();
    if (is_null($rule))
    {
      throw new \Exception('Rule not found', 400);
    }
    $modelName = '\App\Models\\' . ucfirst($rule->sub_type);
    if (!class_exists($modelName))
    {
      throw new \Exception('Rule not found', 400);
    }

    $model = new $modelName();
    if (is_subclass_of($model, \App\Models\Common::class))
    {
      // get definitions
      $definitions = $model->getDefinitions(true);
      foreach ($definitions as $def)
      {
        if ($def->name == $this->attributes['criteria'])
        {
          return $def;
        }
      }
    }
    throw new \Exception('Rule criterium not exists', 400);
  }

  public function getPatternAttribute(): mixed
  {
    $pattern = $this->attributes['pattern'];
    $criteria = $this->getCriteriaAttribute();
    if ($criteria->type == 'dropdown' || $criteria->type == 'dropdown_remote')
    {
      if (
          $this->attributes['condition'] == 0 || // Pattern is
          $this->attributes['condition'] == 1 || // Pattern is not
          $this->attributes['condition'] == 11 || // Pattern under
          $this->attributes['condition'] == 12 // Pattern not under
      )
      {
        if (!empty($criteria->values))
        {
          if (!isset($criteria->values[$pattern]))
          {
            return null;
          }
          return [
            'id' => (int) $pattern,
            'value' => $criteria->values[$pattern]['title'],
          ];
        }
        if (!is_null($criteria->itemtype))
        {
          $item = $criteria->itemtype::where('id', $pattern)->first();
          if (is_null($item))
          {
            return null;
          }
          return [
            'id'    => (int) $pattern,
            'value' => $item->name,
          ];
        }
      }
    }
    return $this->attributes['pattern'];
    // TODO problem when condition is IS or ISNOT
  }

  public function getPatternviewfieldAttribute(): Definition
  {
    $criteria = $this->getCriteriaAttribute();
    $criteria->title = 'Pattern';
    $criteria->name = 'pattern';
    $criteria->value = $this->attributes['pattern'];
    if (!is_null($criteria->multiple))
    {
      $criteria->multiple = false;
    }
    if ($criteria->type == 'dropdown' || $criteria->type == 'dropdown_remote')
    {
      $pattern = $this->getPatternAttribute();
      if (is_array($pattern))
      {
        $criteria->value = $pattern['id'];
        $criteria->valuename = $pattern['value'];
      }
    }
    return $criteria;
  }

  /** @return BelongsTo<\App\Models\Rules\Rule, $this> */
  public function rule(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Rules\Rule::class);
  }
}
