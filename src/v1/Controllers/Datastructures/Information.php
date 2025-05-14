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
   * @param array<string, mixed>  $info
   */
  public function addInformation($type, $info): void
  {
    $data = [
      'value' => $info['value'],
      'link'  => $info['link'],
    ];
    if (isset($info['button']))
    {
      $data['button'] = $info['button'];
    }

    $this->information->{$type}[$info['key']] = $data;
  }
}
