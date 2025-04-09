<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Question
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'comment' => $translator->translate('Description'),
      'fieldtype' => $translator->translatePlural('Type', 'Types', 1),
      'is_required' => $translator->translatePlural('Mandatory field', 'Mandatory fields', 1),
      'show_empty' => $translator->translate('Show empty'),
      'default_values' => sprintf(
        '%1$s <small>(%2$s)</small>',
        $translator->translate('Default values'),
        $translator->translate('One per line')
      ),
      'values' => sprintf(
        '%1$s <small>(%2$s)</small>',
        $translator->translatePlural('Value', 'Values', 2),
        $translator->translate('One per line')
      ),
      'range_min' => sprintf('%1$s (%2$s)', $translator->translate('Size'), $translator->translate('Min')),
      'range_max' => sprintf('%1$s (%2$s)', $translator->translate('Size'), $translator->translate('Max')),
      'regex' => sprintf(
        '%1$s <small><a href="http://php.net/manual/reference.pcre.pattern.syntax.php" ' .
          'target="_blank">(%2$s)</a></small>',
        $translator->translate('Additional validation'),
        $translator->translate('Regular expression')
      ),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(2, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      3,
      $t['fieldtype'],
      'dropdown',
      'fieldtype',
      dbname: 'fieldtype',
      values: self::getFieldtype(),
      fillable: true
    ));
    $defColl->add(new Def(4, $t['is_required'], 'boolean', 'is_required', fillable: true));
    $defColl->add(new Def(5, $t['show_empty'], 'boolean', 'show_empty', fillable: true));
    $defColl->add(new Def(6, $t['default_values'], 'textarea', 'default_values', fillable: true));
    $defColl->add(new Def(7, $t['values'], 'textarea', 'values', fillable: true));
    $defColl->add(new Def(8, $t['range_min'], 'input', 'range_min', fillable: true));
    $defColl->add(new Def(9, $t['range_max'], 'input', 'range_max', fillable: true));
    $defColl->add(new Def(10, $t['regex'], 'input', 'regex', fillable: true));
    $defColl->add(new Def(16, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(15, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<string, mixed>
   */
  public static function getFieldtype(): array
  {
    global $translator;
    return [
      'checkbox' => [
        'title' => $translator->translate('Checkboxes'),
      ],
      'radio' => [
        'title' => $translator->translate('Radios'),
      ],
      'hidden' => [
        'title' => $translator->translatePlural('Hidden field', 'Hidden fields', 1),
      ],
      'email' => [
        'title' => $translator->translatePlural('Email', 'Emails', 1),
      ],
      'date' => [
        'title' => $translator->translatePlural('Date', 'Dates', 1),
      ],
      'description' => [
        'title' => $translator->translate('Description'),
      ],
      'integer' => [
        'title' => $translator->translate('Integer'),
      ],
      'file' => [
        'title' => $translator->translate('File'),
      ],
      'float' => [
        'title' => $translator->translate('Float'),       // regexp
      ],
      'time' => [
        'title' => $translator->translate('hour' . "\004" . 'Time'),
      ],
      'dropdown' => [
        'title' => $translator->translatePlural('Dropdown', 'Dropdowns', 1),   // intitules (dropdown)
      ],
      'glpiselect' => [
        'title' => $translator->translatePlural('GLPI object', 'GLPI objects', 1),
      ],
      'select' => [
        'title' => $translator->translate('Select'),
      ],
      'multiselect' => [
        'title' => $translator->translate('Multiselect'),
      ],
      'text' => [
        'title' => $translator->translate('Text'),
      ],
      'urgency' => [
        'title' => $translator->translate('Urgency'),
      ],
      'textarea' => [
        'title' => $translator->translate('Textarea'),
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Question', 'Questions', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Section', 'Sections', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/sections',
      ],
      [
        'title' => $translator->translatePlural('Form', 'Forms', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/forms',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
