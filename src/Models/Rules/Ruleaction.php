<?php

declare(strict_types=1);

namespace App\Models\Rules;

class Ruleaction extends \App\Models\Common
{
  protected $definition = '\App\Models\Definitions\Ruleaction';
  protected $titles = ['Action', 'Actions'];
  protected $icon = 'magic';
  protected $hasEntityField = false;
  public $timestamps = false;

  protected $appends = [
    'fieldviewfield',
    'valueviewfield',
  ];

  protected $visible = [
    'fieldviewfield',
    'valueviewfield',
  ];

  protected $fillable = [
    'rule_id',
    'action_type',
    'field',
    'value',
  ];

  public function getFieldviewfieldAttribute(): array
  {
    /** @var \App\Models\Rules\Rule|null */
    $rule = \App\Models\Rules\Rule::find($this->attributes['rule_id']);
    $modelName = '\App\Models\\' . ltrim($rule->sub_type, 'Rule');

    $model = new $modelName();
    // get definitions
    $definitions = $model->getDefinitions();
    foreach ($definitions as $def)
    {
      if ($def['name'] == $this->field)
      {
        return $def;
      }
    }
    return [];
  }

  public function getValueviewfieldAttribute(): array
  {
    $field = $this->getFieldviewfieldAttribute();

    $field['name'] = 'value';
    $field['value'] = $this->attributes['value'];
    if (isset($field['multiple']))
    {
      unset($field['multiple']);
    }
    if ($field['type'] == 'dropdown' || $field['type'] == 'dropdown_remote')
    {
      if (isset($field['values']))
      {
        $field['name'] = (int) $this->value;
        $field['valuename'] = $field['values'][$this->value]['title'];
      } else {
        $item = $field['itemtype']::find((int) $this->value);
        $field['name'] = $item->id;
        $field['valuename'] = $item->name;
      }
    }
    return $field;
  }
}
