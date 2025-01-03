<?php

declare(strict_types=1);

namespace App\Models\Definitions;

class OauthimapApplication
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 1,
        'title' => $translator->translate('Name'),
        'type'  => 'input',
        'name'  => 'name',
        'fillable' => true,
      ],
      [
        'id'    => 205,
        'title' => $translator->translate('Active'),
        'type'  => 'boolean',
        'name'  => 'is_active',
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Oauth provider'),
        'type'  => 'input',
        'name'  => 'provider',
        'fillable' => true,
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('Client ID'),
        'type'  => 'input',
        'name'  => 'client_id',
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('Tenant ID'),
        'type'  => 'input',
        'name'  => 'tenant_id',
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
        'fillable' => true,
      ],
    ];
  }

  public static function getRelatedPages($rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Oauth IMAP application', 'Oauth IMAP applications', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
