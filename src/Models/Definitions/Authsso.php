<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Authsso
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'              => pgettext('global', 'Name'),
      'comment'           => npgettext('global', 'Comment', 'Comments', 2),
      'is_active'         => pgettext('global', 'Active'),
      'provider'          => pgettext('SSO parameter', 'Provider'),
      'callbackid'        => pgettext('SSO parameter', 'callback id'),
      'applicationid'     => pgettext('SSO parameter', 'application id'),
      'applicationsecret' => pgettext('SSO parameter', 'application secret'),
      'applicationpublic' => pgettext('SSO parameter', 'application public'),
      'directoryid'       => pgettext('SSO parameter', 'directory id'),
      'baseurl'           => pgettext('SSO parameter', 'baseurl'),
      'realm'             => pgettext('SSO parameter', 'realm'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true, required: true));
    $defColl->add(new Def(3, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(4, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(
      5,
      $t['provider'],
      'dropdown',
      'provider',
      values: self::getProviderArray(),
      fillable: true,
      required: true,
    ));
    $defColl->add(new Def(6, $t['callbackid'], 'input', 'callbackid'));
    $defColl->add(new Def(7, $t['applicationid'], 'input', 'applicationid', fillable: true));
    $defColl->add(new Def(8, $t['applicationsecret'], 'inputpassword', 'applicationsecret', fillable: true));
    $defColl->add(new Def(9, $t['applicationpublic'], 'input', 'applicationpublic', fillable: true));
    $defColl->add(new Def(10, $t['directoryid'], 'input', 'directoryid', fillable: true));
    $defColl->add(new Def(11, $t['baseurl'], 'input', 'baseurl', fillable: true));
    $defColl->add(new Def(12, $t['realm'], 'input', 'realm', fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getProviderArray(): array
  {
    return [
      'facebook' => [
        'title' => 'Facebook',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
          'options',
        ],
        'default_scope' => [
          'email',
        ],
        'default_options' => [
          'email',
          'name',
          'picture.width(99999)',
        ],
        'suboption' => 'identity.fields',
      ],
      'twitter' => [
        'title' => 'Twitter',
        'fields' => [
          'applicationId',
          'applicationSecret',
        ],
      ],
      'google' => [
        'title' => 'Google',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
          'options',
        ],
        'default_scope' => [
          'https://www.googleapis.com/auth/userinfo.email',
          'https://www.googleapis.com/auth/userinfo.profile'
        ],
        'default_options' => [
          'hd' => 'domain.tld',
        ],
        'suboption' => 'auth.parameters',
      ],
      'paypal' => [
        'title' => 'Paypal',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'profile',
          'email',
          'address',
          'phone',
          'https://uri.paypal.com/services/paypalattributes'
        ],
      ],
      'vk' => [
        'title' => 'VK',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'options',
        ],
        'default_options' => [
          'sex',
          'screen_name',
          'photo_max_orig',
        ],
        'suboption' => 'identity.fields',
      ],
      'github' => [
        'title' => 'Github',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
          'options',
        ],
        'default_scope' => [
          'user',
          'email',
        ],
        'default_options' => [
          'fetch_emails' => true
        ],
      ],
      'instagram' => [
        'title' => 'Instagram',
        'fields' => [
          'applicationId',
          'applicationSecret',
        ],
      ],
      'slack' => [
        'title' => 'Slack',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'identity.basic',
          'identity.email',
          'identity.team',
          'identity.avatar',
        ],
      ],
      'twitch' => [
        'title' => 'Twitch',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'user_read',
        ],
      ],
      'px500' => [
        'title' => 'px500',
        'fields' => [
          'applicationId',
          'applicationSecret',
        ],
      ],
      'bitbucket' => [
        'title' => 'Bitbucket',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'account',
        ],
      ],
      'amazon' => [
        'title' => 'Amazon',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'profile',
        ],
      ],
      'gitlab' => [
        'title' => 'Gitlab',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'read_user',
        ],
      ],
      'vimeo' => [
        'title' => 'Vimeo',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
        ],
      ],
      'digital-ocean' => [
        'title' => 'Digital Ocean',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
        ],
      ],
      'yandex' => [
        'title' => 'Yandex',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
        ],
      ],
      'mail-ru' => [
        'title' => 'Mail RU',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
        ],
      ],
      'odnoklassniki' => [
        'title' => 'odnoklassniki',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'applicationPublic',
          'scope',
        ],
        'default_scope' => [
          'GET_EMAIL'
        ],
      ],
      'steam' => [
        'title' => 'Steam',
        'fields' => [
          'applicationId',
        ],
      ],
      'tumblr' => [
        'title' => 'Tumblr',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
        ],
      ],
      'pixelpin' => [
        'title' => 'Pixelpin',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'email'
        ],
      ],
      'discord' => [
        'title' => 'Discord',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'identify',
          'email'
        ],
      ],
      'microsoft' => [
        'title' => 'Microsoft',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'wl.basic',
          'wl.birthday',
          'wl.emails'
        ],
      ],
      'azure-ad' => [
        'title' => 'Azure AD',
        'fields' => [
          'applicationId',
          'directoryId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'openid',
          'profile',
          'email'
        ],
      ],
      'smashcast' => [
        'title' => 'Smashcast',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
        ],
      ],
      'steein' => [
        'title' => 'Steein',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'users',
          'email'
        ],
      ],
      'reddit' => [
        'title' => 'Reddit',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'identity'
        ],
      ],
      'linkedin' => [
        'title' => 'Linkedin',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
          'options',
        ],
        'default_scope' => [
          'r_liteprofile',
          'r_emailaddress',
        ],
        'default_options' => [
          'fetch_emails' => true,
        ],
      ],
      'yahoo' => [
        'title' => 'Yahoo',
        'fields' => [
          'applicationId',
          'applicationSecret',
        ],
      ],
      'wordpress' => [
        'title' => 'Wordpress',
        'fields' => [
          'applicationId',
          'applicationSecret',
        ],
      ],
      'trello' => [
        'title' => 'Trello',
        'fields' => [
          'applicationId',
          'applicationSecret',
          'scope',
          'options',
        ],
        'default_scope' => [
          'read',
        ],
        'default_options' => [
          'name' => 'My Awesome App',
          'expiration' => '1day',
        ],
      ],
      'keycloak' => [
        'title' => 'Keycloak',
        'fields' => [
          'baseUrl',
          'realm',
          'applicationId',
          'applicationSecret',
          'scope',
        ],
        'default_scope' => [
          'email',
          'profile',
        ],
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('sso', 'SSO', 'SSOs', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('sso', 'Scopes'),
        'icon' => 'list',
        'link' => '',
      ],
      [
        'title' => pgettext('sso', 'Options'),
        'icon' => 'filter',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
