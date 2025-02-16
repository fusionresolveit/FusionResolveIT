<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

use Respect\Validation\Validator;
use Respect\Validation\Rules;

final class Validation
{
  public static function attrStrNotempty(string $key): Validator
  {
    $sub = Validator::create(
      new Rules\StringType(),
      new Rules\NotEmpty(),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function attrStr(string $key): Validator
  {
    $sub = Validator::create(
      new Rules\StringType(),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function attrDate(string $key, string $format = ''): Validator
  {
    if ($format == '')
    {
      $sub = Validator::create(
        new Rules\Date(),
      );
    } else {
      $sub = Validator::create(
        new Rules\Date($format),
      );
    }
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function attrDateTime(string $key): Validator
  {
    $sub = Validator::create(
      new Rules\DateTime(),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  /**
   * @param array<string> $values
   */
  public static function strInArray(array $values): Validator
  {
    return Validator::create(
      new Rules\In($values),
    );
  }

  public static function attrNumericVal(string $key): Validator
  {
    $sub = Validator::create(
      new Rules\NumericVal(),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function attrFloatVal(string $key): Validator
  {
    $sub = Validator::create(
      new Rules\FloatVal(),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function attrIsArray(string $key): Validator
  {
    $sub = Validator::create(
      new Rules\ArrayType(),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function numericVal(): Validator
  {
    return Validator::create(
      new Rules\NumericVal(),
    );
  }
}
