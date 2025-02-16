<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Rssfeed
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;

    $t = [
      'name' => $translator->translate('Name'),
      'user' => $translator->translate('By'),
      'url' => $translator->translate('URL'),
      'is_active' => $translator->translate('Active'),
      'have_error' => $translator->translate('Error retrieving RSS feed'),
      'max_items' => $translator->translate('Number of items displayed'),
      'comment' => $translator->translate('Comments'),
      'refresh_rate' => $translator->translate('Refresh rate'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      2,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      readonly: true
    ));
    $defColl->add(new Def(3, $t['url'], 'input', 'url', fillable: true));
    $defColl->add(new Def(4, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(6, $t['have_error'], 'boolean', 'have_error', readonly: true));
    $defColl->add(new Def(
      7,
      $t['max_items'],
      'dropdown',
      'max_items',
      dbname: 'max_items',
      values: \App\v1\Controllers\Dropdown::generateNumbers(5, 100, 5, [1 => '1']),
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(
      5,
      $t['refresh_rate'],
      'dropdown',
      'refresh_rate',
      dbname: 'refresh_rate',
      values: self::getTimestampArray(
        [
          'min'                  => $HOUR_TIMESTAMP,
          'max'                  => $DAY_TIMESTAMP,
          'step'                 => $HOUR_TIMESTAMP,
          'display_emptychoice'  => false,
          'toadd'                => [
            5 * $MINUTE_TIMESTAMP,
            15 * $MINUTE_TIMESTAMP,
            30 * $MINUTE_TIMESTAMP,
            45 * $MINUTE_TIMESTAMP
          ]
        ]
      ),
      fillable: true
    ));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;

    /*

    $tab[] = [
        'id'                 => 'common',
        'name'               => __('Characteristics')
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    */
  }

  /**
   * @param array<string, mixed> $options
   *
   * @return array<mixed>
   */
  public static function getTimestampArray(array $options = []): array
  {
    global $translator;

    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;


    $params = [];
    $params['min']                 = 0;
    $params['max']                 = $DAY_TIMESTAMP;
    $params['step']                = 5 * $MINUTE_TIMESTAMP;
    $params['addfirstminutes']     = false;
    $params['toadd']               = [];
    $params['inhours']             = false;

    if (count($options))
    {
      foreach ($options as $key => $val)
      {
        $params[$key] = $val;
      }
    }

    $params['min'] = floor($params['min'] / $params['step']) * $params['step'];

    if ($params['min'] == 0)
    {
      $params['min'] = $params['step'];
    }

    $values = [];

    if ($params['addfirstminutes'])
    {
      $max = max($params['min'], 10 * $MINUTE_TIMESTAMP);
      for ($i = $MINUTE_TIMESTAMP; $i < $max; $i += $MINUTE_TIMESTAMP)
      {
        $values[$i] = '';
      }
    }

    for ($i = $params['min']; $i <= $params['max']; $i += $params['step'])
    {
      $values[$i] = '';
    }

    if (count($params['toadd']))
    {
      foreach ($params['toadd'] as $key)
      {
        $values[$key] = '';
      }
      ksort($values);
    }

    foreach ($values as $i => $val)
    {
      if ($params['inhours'])
      {
        $day  = 0;
        $hour = floor($i / $HOUR_TIMESTAMP);
      }
      else
      {
        $day  = floor($i / $DAY_TIMESTAMP);
        $hour = floor(($i % $DAY_TIMESTAMP) / $HOUR_TIMESTAMP);
      }
      $minute     = floor(($i % $HOUR_TIMESTAMP) / $MINUTE_TIMESTAMP);
      if ($minute === 0.0)
      {
        $minute = '00';
      }
      $values[$i] = '';
      if ($day > 0)
      {
        if (($hour > 0) || ($minute > 0))
        {
          if ($minute < 10)
          {
            $minute = '0' . $minute;
          }

          //TRANS: %1$d is the number of days, %2$d the number of hours,
          //       %3$s the number of minutes : display 1 day 3h15
          $values[$i] = sprintf(
            $translator->translatePlural('%1$d day %2$dh%3$s', '%1$d days %2$dh%3$s', $day),
            $day,
            $hour,
            $minute
          );
        }
        else
        {
          $values[$i] = sprintf($translator->translatePlural('%d day', '%d days', $day), $day);
        }
      }
      elseif ($hour > 0 || $minute > 0)
      {
        if ($minute < 10)
        {
          $minute = '0' . $minute;
        }

        //TRANS: %1$d the number of hours, %2$s the number of minutes : display 3h15
        $values[$i] = sprintf($translator->translate('%1$dh%2$s'), $hour, $minute);
      }
    }

    $tab = [];
    foreach (array_keys($values) as $key)
    {
      $tab[$key]['title'] = $values[$key];
    }
    return $tab;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('RSS feed', 'RSS feed', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Content'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Target', 'Targets', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
