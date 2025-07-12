<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Mailcollector
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('email', 'Email address'),
      'errors' => pgettext('email', 'Connection errors'),
      'is_active' => pgettext('global', 'Active'),
      'accepted' => pgettext('email', 'Accepted mail archive folder (optional)'),
      'refused' => pgettext('email', 'Refused mail archive folder (optional)'),
      'use_mail_date' => pgettext('email', 'Use mail date, instead of collect one'),
      'requester_field' => pgettext('email', 'Use Reply-To as requester (when available)'),
      'add_cc_to_observer' => pgettext('email', 'Add CC users as observer'),
      'collect_only_unread' => pgettext('email', 'Collect only unread mail'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'is_oauth' => pgettext('email', 'Use Oauth'),
      'oauth_provider' => pgettext('email', 'Provider'),
      'oauth_applicationid' => pgettext('email', 'ApplicationID'),
      'oauth_directoryid' => pgettext('email', 'DirectoryID (Only for Microsoft Azure)'),
      'oauth_applicationsecret' => pgettext('email', 'ApplicationSecret'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(22, $t['errors'], 'input', 'errors', readonly: true));
    $defColl->add(new Def(2, $t['is_active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(20, $t['accepted'], 'input', 'accepted', fillable: true));
    $defColl->add(new Def(21, $t['refused'], 'input', 'refused', fillable: true));
    $defColl->add(new Def(201, $t['use_mail_date'], 'boolean', 'use_mail_date', fillable: true));
    $defColl->add(new Def(202, $t['requester_field'], 'boolean', 'requester_field', fillable: true));
    $defColl->add(new Def(203, $t['add_cc_to_observer'], 'boolean', 'add_cc_to_observer', fillable: true));
    $defColl->add(new Def(204, $t['collect_only_unread'], 'boolean', 'collect_only_unread', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(301, $t['is_oauth'], 'boolean', 'is_oauth', fillable: true));
    $defColl->add(new Def(
      302,
      $t['oauth_provider'],
      'dropdown',
      'oauth_provider',
      dbname: 'oauth_provider',
      values: self::getProvidersArray(),
      fillable: true
    ));
    $defColl->add(new Def(303, $t['oauth_applicationid'], 'input', 'oauth_applicationid', fillable: true));
    $defColl->add(new Def(304, $t['oauth_directoryid'], 'input', 'oauth_directoryid', fillable: true));
    $defColl->add(new Def(
      305,
      $t['oauth_applicationsecret'],
      'inputpassword',
      'oauth_applicationsecret',
      fillable: true
    ));

    return $defColl;
    // [
    //   'id'    => 5,
    //   'title' => 'Maximum size of each file imported by the mails receiver',
    //   'type'  => 'dropdown',
    //   'name'  => 'filesize_max',
    //   'dbname'  => 'filesize_max',
    //   'values' => self::showMaxFilesize(),
    //   'fillable' => true,
    // ],

/*
    $tab[] = [
        'id'                 => '3',
        'table'              => $this->getTable(),
        'field'              => 'host',
        'name'               => __('Connection string'),
        'massiveaction'      => false,
        'datatype'           => 'string'
    ];

    $tab[] = [
        'id'                 => '4',
        'table'              => $this->getTable(),
        'field'              => 'login',
        'name'               => __('Login'),
        'massiveaction'      => false,
        'datatype'           => 'string',
        'autocomplete'       => true,
    ];

*/
  }

  /**
   * @return array<mixed>
   */
  public static function showMaxFilesize(): array
  {

    $tab[0]['title'] = pgettext('email', 'No import');
    for ($index = 1; $index < 100; $index++)
    {
      $tab[$index * 1048576]['title'] = sprintf(pgettext('global', '%s Mio'), $index);
    }
    return $tab;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Receiver', 'Receivers', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Action', 'Actions', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getProvidersArray(): array
  {
    return [
      null => [
        'title' => pgettext('global', 'None'),
      ],
      'azure' => [
        'title' => 'Microsoft Azure',
      ],
      'google' => [
        'title' => 'Google',
      ],
    ];
  }
}
