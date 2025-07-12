<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Question
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'comment' => pgettext('global', 'Description'),
      'fieldtype' =>  npgettext('global', 'Type', 'Types', 1),
      'is_required' => npgettext('form', 'Mandatory field', 'Mandatory fields', 1),
      'show_empty' => pgettext('form', 'Show empty'),
      'default_values' => sprintf(
        '%1$s <small>(%2$s)</small>',
        pgettext('form', 'Default values'),
        pgettext('form', 'One per line')
      ),
      'values' => sprintf(
        '%1$s <small>(%2$s)</small>',
        npgettext('form', 'Value', 'Values', 2),
        pgettext('form', 'One per line')
      ),
      'range_min' => sprintf('%1$s (%2$s)', pgettext('global', 'Size'), pgettext('global', 'Min')),
      'range_max' => sprintf('%1$s (%2$s)', pgettext('global', 'Size'), pgettext('global', 'Max')),
      'regex' => sprintf(
        '%1$s <small><a href="http://php.net/manual/reference.pcre.pattern.syntax.php" ' .
          'target="_blank">(%2$s)</a></small>',
        pgettext('form', 'Additional validation'),
        pgettext('form', 'Regular expression')
      ),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
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
    return [
      'checkbox' => [
        'title' => pgettext('form', 'Checkboxes'),
      ],
      'radio' => [
        'title' => pgettext('form', 'Radios'),
      ],
      'hidden' => [
        'title' => npgettext('form', 'Hidden field', 'Hidden fields', 1),
      ],
      'email' => [
        'title' => npgettext('global', 'Email', 'Emails', 1),
      ],
      'date' => [
        'title' => npgettext('global', 'Date', 'Dates', 1),
      ],
      'description' => [
        'title' => pgettext('global', 'Description'),
      ],
      'integer' => [
        'title' => pgettext('form', 'Integer'),
      ],
      'file' => [
        'title' => pgettext('document', 'File'),
      ],
      'float' => [
        'title' => pgettext('form', 'Float'),
      ],
      'time' => [
        'title' => pgettext('form', 'Time'),
      ],
      'dropdown' => [
        'title' => npgettext('form', 'Dropdown', 'Dropdowns', 1),   // intitules (dropdown)
      ],
      'glpiselect' => [
        'title' => npgettext('form', 'App object', 'App objects', 1),
      ],
      'select' => [
        'title' => pgettext('form', 'Select'),
      ],
      'multiselect' => [
        'title' => pgettext('form', 'Multiselect'),
      ],
      'text' => [
        'title' => pgettext('form', 'Text'),
      ],
      'urgency' => [
        'title' => pgettext('ITIL', 'Urgency'),
      ],
      'textarea' => [
        'title' => pgettext('form', 'Textarea'),
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Question', 'Questions', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Section', 'Sections', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/sections',
      ],
      [
        'title' => npgettext('global', 'Form', 'Forms', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/forms',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
