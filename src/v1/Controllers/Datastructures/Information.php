<?php

declare(strict_types=1);

namespace App\v1\Controllers\Datastructures;

trait Information
{
  public function initInformationData(): void
  {
    $this->information = (object)[
      'top'    => [],
      'bottom' => [],
    ];
  }

  /**
   * Add information into form
   *
   * @param string  $type top|bottom
   * @param string  $key unique key
   * @param string  $value value to display (often translation)
   * @param string  $link the link to the webpage, null to disable it
   */
  public function addInformation($type, $key, $value, $link): void
  {
    $this->information->{$type}[$key] = ['value' => $value, 'link' => $link];
  }
}
