<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Mailcollector
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Email address'),
      'errors' => $translator->translate('Connection errors'),
      'is_active' => $translator->translate('Active'),
      'accepted' => $translator->translate('Accepted mail archive folder (optional)'),
      'refused' => $translator->translate('Refused mail archive folder (optional)'),
      'use_mail_date' => $translator->translate('Use mail date, instead of collect one'),
      'requester_field' => $translator->translate('Use Reply-To as requester (when available)'),
      'add_cc_to_observer' => $translator->translate('Add CC users as observer'),
      'collect_only_unread' => $translator->translate('Collect only unread mail'),
      'comment' => $translator->translate('Comments'),
      'is_oauth' => $translator->translate('Use Oauth'),
      'oauth_provider' => $translator->translate('Provider'),
      'oauth_applicationid' => $translator->translate('ApplicationID'),
      'oauth_directoryid' => $translator->translate('DirectoryID (Only for Microsoft Azure)'),
      'oauth_applicationsecret' => $translator->translate('ApplicationSecret'),
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
    //   'title' => $translator->translate('Maximum size of each file imported by the mails receiver'),
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
    global $translator;


    $tab[0]['title'] = $translator->translate('No import');
    for ($index = 1; $index < 100; $index++)
    {
      $tab[$index * 1048576]['title'] = sprintf($translator->translate('%s Mio'), $index);
    }
    return $tab;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Receiver', 'Receivers', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Action', 'Actions', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
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
    global $translator;

    return [
      null => [
        'title' => $translator->translate('None'),
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
