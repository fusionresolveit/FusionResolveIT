<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostProfileview extends Post
{
  /** @var ?\App\Models\Profile */
  public $profile;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Profile');

    if (
        Validation::attrNumericVal('profile')->isValid($data) &&
        isset($data->profile)
    )
    {
      $profile = \App\Models\Profile::where('id', $data->profile)->first();
      if (!is_null($profile))
      {
        $this->filledFields[] = 'profile';
        $this->profile = $profile;
      }
      elseif (intval($data->profile) == 0)
      {
        $emptyProfile = new \App\Models\Profile();
        $emptyProfile->id = 0;
        $this->filledFields[] = 'profile';
        $this->profile = $emptyProfile;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
  }

  /**
   * @return array{profile: \App\Models\Profile|null}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    return [
      'profile' => $this->profile,
    ];
  }
}
