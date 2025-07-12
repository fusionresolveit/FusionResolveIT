<?php

declare(strict_types=1);

namespace App\Models;

class Notificationtemplatetranslation extends Common
{
  protected $definition = \App\Models\Definitions\Notificationtemplatetranslation::class;
  protected $icon = 'edit';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Notification template translation', 'Notification template translations', $nb);
  }
}
