<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

use Exception;
use SimpleXMLElement;

final class Common extends \App\v1\Controllers\Common
{
  public static function xmlToObj(string $value): SimpleXMLElement
  {
    $dataObj = @simplexml_load_string($value);
    if ($dataObj === false)
    {
      throw new Exception('XML not well formed', 400);
    }
    return $dataObj;
  }

  public static function getArrayData(object|array $value): array
  {
    if (gettype($value) === 'array')
    {
      return $value;
    }
    return [$value];
  }

  /**
   * Remove non printable characters & clean multiple spaces
   */
  public static function cleanString(string $value): string
  {
    $value = filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
    $value = preg_replace('!\s+!', ' ', $value);
    $value = str_replace('®', '', $value);
    return trim($value);
  }
}
