<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostUserItemSubAuthorization
{
  /** @var int */
  public $profileId;

  /** @var int */
  public $entityId;

  /** @var bool */
  public $recursive;

  /** @var bool */
  public $delete;

  public function __construct(object $data)
  {
    if (
        Validation::attrNumericVal('profile')->isValid($data) &&
        isset($data->profile)
    )
    {
      $this->profileId = intval($data->profile);
    } else {
      throw new \Exception('Wrong data request', 400);
    }

    if (
        Validation::attrNumericVal('entity')->isValid($data) &&
        isset($data->entity)
    )
    {
      $this->entityId = intval($data->entity);
    } else {
      throw new \Exception('Wrong data request', 400);
    }

    if (
        Validation::attrIsArray('is_recursive')->isValid($data) &&
        isset($data->is_recursive) &&
        in_array('on', $data->is_recursive)
    )
    {
      $this->recursive = true;
    } else {
      $this->recursive = false;
    }

    if (
        Validation::attrStrNotempty('delete')->isValid($data) &&
        isset($data->delete)
    )
    {
      $this->delete = true;
    } else {
      $this->delete = false;
    }
  }
}
