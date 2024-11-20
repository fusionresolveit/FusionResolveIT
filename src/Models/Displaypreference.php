<?php

namespace App\Models;

class Displaypreference extends Common
{
  public $timestamps = false;
  protected $hasEntityField = false;
  protected $titles = ['Manage columns', 'Manage columns'];
  protected $icon = 'columns';

  protected $fillable = [
    'itemtype',
    'num',
    'rank',
    'user_id',
  ];

  /**
   * Get display preference for a user for an itemtype
   *
   * @param string  $itemtype  itemtype
   * @param integer $user_id   user ID
   *
   * @return array
  **/
  public static function getForTypeUser($itemtype, $user_id)
  {
    $items = \App\Models\Displaypreference::where('itemtype', $itemtype)
      ->where('user_id', $user_id)
      ->orderBy('rank', 'asc')
      ->get();

    if (count($items) == 0)
    {
      $items = \App\Models\Displaypreference::where('itemtype', $itemtype)
        ->where('user_id', 0)
        ->orderBy('rank', 'asc')
        ->get();
    }

    $default_prefs = [];
    foreach ($items as $myItem)
    {
      $default_prefs[] = $myItem->num;
    }
    return $default_prefs;
  }
}
