<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostLoginChangeProfileEntity
{
  /** @var int */
  public $profileId;

  /** @var int */
  public $entityId;

  /** @var bool */
  public $recursive;

  public function __construct(object $data)
  {
    if (
        Validation::attrNumericVal('changeProfile')->isValid($data) &&
        isset($data->changeProfile)
    )
    {
      $this->profileId = intval($data->changeProfile);
    } else {
      throw new \Exception('Wrong data request', 400);
    }

    if (
        Validation::attrNumericVal('changeEntity')->isValid($data) &&
        isset($data->changeEntity)
    )
    {
      $this->entityId = intval($data->changeEntity);
    } else {
      throw new \Exception('Wrong data request', 400);
    }

    if (
        Validation::attrIsArray('changeEntityRecursive')->isValid($data) &&
        isset($data->changeEntityRecursive) &&
        in_array('on', $data->changeEntityRecursive)
    )
    {
      $this->recursive = true;
    } else {
      $this->recursive = false;
    }
  }
}
