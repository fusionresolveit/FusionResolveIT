<?php

declare(strict_types=1);

namespace App\Models\Rules;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;

class Ruleaction extends \App\Models\Common
{
  protected $definition = \App\Models\Definitions\Ruleaction::class;
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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('rule', 'Action', 'Actions', $nb);
  }

  public function getFieldviewfieldAttribute(): Definition
  {
    $rule = \App\Models\Rules\Rule::where('id', $this->attributes['rule_id'])->first();
    if (is_null($rule))
    {
      throw new \Exception('Id not found', 404);
    }

    $modelName = '\App\Models\\' . $rule->sub_type;

    $model = new $modelName();
    if (!is_subclass_of($model, \App\Models\Common::class))
    {
      throw new \Exception('Error in rule action', 500);
    }

    // get definitions
    $definitions = $model->getDefinitions(true);
    foreach ($definitions as $def)
    {
      if ($def->name == $this->field)
      {
        return $def;
      }
    }
    throw new \Exception('Error in rule action', 500);
  }

  public function getValueviewfieldAttribute(): Definition
  {
    $field = $this->getFieldviewfieldAttribute();

    $field->name = 'value';
    $field->value = $this->attributes['value'];
    $field->multiple = null;
    if (!is_null($this->value) && ($field->type == 'dropdown' || $field->type == 'dropdown_remote'))
    {
      if (count($field->values) > 0)
      {
        $field->name = $this->value;
        $field->valuename = $field->values[$this->value]['title'];
      }
      elseif (is_numeric($this->value) && !is_null($field->itemtype))
      {
        $item = $field->itemtype::where('id', $this->value)->first();
        if (is_null($item))
        {
          throw new \Exception('Error in rule action', 500);
        }
        $field->name = $item->id;
        $field->valuename = $item->name;
      }
    }
    return $field;
  }
}
