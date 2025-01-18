<?php

declare(strict_types=1);

namespace App\v1\Controllers\Fusioninventory;

use Respect\Validation\Validator;
use Respect\Validation\Rules;

final class Validation
{
  public static function attrStrNotempty($key)
  {
    $sub = Validator::create(
      new Rules\StringType(),
      new Rules\NotEmpty(),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function attrDate($key, $format)
  {
    $sub = Validator::create(
      new Rules\Date($format),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function attrDateTime($key)
  {
    $sub = Validator::create(
      new Rules\DateTime(),
    );
    return Validator::create(
      new Rules\Attribute($key, $sub),
    );
  }

  public static function strInArray(array $values)
  {
    return Validator::create(
      new Rules\In($values),
    );
  }
}
