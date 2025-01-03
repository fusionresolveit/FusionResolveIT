<?php

declare(strict_types=1);

namespace App\Models\Rules;

class Rulecriterium extends \App\Models\Common
{
  protected $definition = '\App\Models\Definitions\Rulecriterium';
  protected $titles = ['Criterium', 'Criteria'];
  protected $icon = 'magic';
  protected $hasEntityField = false;
  public $timestamps = false;

  protected $appends = [
    'patternviewfield',
  ];

  protected $visible = [
    'patternviewfield',
  ];

  protected $fillable = [
    'rule_id',
    'criteria',
    'condition',
    'pattern',
  ];

  public function getCriteriaAttribute(): array
  {
    /** @var \App\Models\Rules\Rule|null */
    $rule = \App\Models\Rules\Rule::find($this->attributes['rule_id']);
    $modelName = '\App\Models\\' . ltrim($rule->sub_type, 'Rule');

    $model = new $modelName();
    // get definitions
    $definitions = $model->getDefinitions();
    foreach ($definitions as $def)
    {
      if ($def['name'] == $this->attributes['criteria'])
      {
        return $def;
      }
    }
    return [];
  }

  public function getPatternAttribute(): mixed
  {
    $pattern = $this->attributes['pattern'];
    $criteria = $this->getCriteriaAttribute();
    if ($criteria['type'] == 'dropdown' || $criteria['type'] == 'dropdown_remote')
    {
      if (
          $this->attributes['condition'] == \App\v1\Controllers\Rules\Common::PATTERN_IS ||
          $this->attributes['condition'] == \App\v1\Controllers\Rules\Common::PATTERN_IS_NOT ||
          $this->attributes['condition'] == \App\v1\Controllers\Rules\Common::PATTERN_UNDER ||
          $this->attributes['condition'] == \App\v1\Controllers\Rules\Common::PATTERN_NOT_UNDER
      )
      {
        if (isset($criteria['values']))
        {
          if (!isset($criteria['values'][$pattern]))
          {
            return null;
          }
          return [
            'id' => (int) $pattern,
            'value' => $criteria['values'][$pattern]['title'],
          ];
        }
        $item = $criteria['itemtype']::find($pattern);
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
    return $this->attributes['pattern'];
    // TODO problem when condition is IS or ISNOT
  }

  public function getPatternviewfieldAttribute(): array
  {
    $criteria = $this->getCriteriaAttribute();
    $criteria['title'] = 'Pattern';
    $criteria['name'] = 'pattern';
    $criteria['value'] = $this->attributes['pattern'];
    if (isset($criteria['multiple']))
    {
      unset($criteria['multiple']);
    }
    if ($criteria['type'] == 'dropdown' || $criteria['type'] == 'dropdown_remote')
    {
      $pattern = $this->getPatternAttribute();
      if (is_array($pattern))
      {
        $criteria['value'] = $pattern['id'];
        $criteria['valuename'] = $pattern['value'];
      }
    }
    return $criteria;
  }
}
