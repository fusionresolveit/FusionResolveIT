<?php

declare(strict_types=1);

namespace App\Models\Definitions;

class Authldap
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'            => 1,
        'title'         => $translator->translate('Name'),
        'type'          => 'input',
        'name'          => 'name',
        'fillable' => true,
      ],
      [
        'id'    => 30,
        'title' => $translator->translate('Active'),
        'type'  => 'boolean',
        'name'  => 'is_active',
        'fillable' => true,
      ],
      [
        'id'            => 3,
        'title'         => $translator->translate('Server'),
        'type'          => 'input',
        'name'          => 'host',
        'fillable' => true,
      ],
      [
        'id'            => 4,
        'title'         => $translator->translatePlural('Port', 'Ports', 1),
        'type'          => 'input',
        'name'          => 'port',
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('Default server'),
        'type'  => 'boolean',
        'name'  => 'is_default',
        'fillable' => true,
      ],
      [
        'id'            => 5,
        'title'         => $translator->translate('BaseDN'),
        'type'          => 'input',
        'name'          => 'basedn',
        'fillable' => true,
      ],
      [
        'id'            => 40,
        'title'         => $translator->translate('RootDN'),
        'type'          => 'input',
        'name'          => 'rootdn',
        'fillable' => true,
      ],
      [
        'id'            => 41,
        'title'         => $translator->translate('RootDN password'),
        'type'          => 'inputpassword',
        'name'          => 'rootdn_passwd',
        'fillable' => true,
      ],
      [
        'id'            => 6,
        'title'         => $translator->translate('Connection filter'),
        'type'          => 'textarea',
        'name'          => 'condition',
        'fillable' => true,
      ],
      [
        'id'            => 8,
        'title'         => $translator->translate('Login field'),
        'type'          => 'input',
        'name'          => 'login_field',
        'fillable' => true,
      ],
      [
        'id'            => 9,
        'title'         => $translator->translate('Surname'),
        'type'          => 'input',
        'name'          => 'realname_field',
        'fillable' => true,
      ],
      [
        'id'            => 10,
        'title'         => $translator->translate('First name'),
        'type'          => 'input',
        'name'          => 'firstname_field',
        'fillable' => true,
      ],
      [
        'id'            => 11,
        'title'         => 'Phone',
        'type'          => 'input',
        'name'          => 'phone_field',
        'fillable' => true,
      ],
      [
        'id'            => 12,
        'title'         => $translator->translate('Phone 2'),
        'type'          => 'input',
        'name'          => 'phone2_field',
        'fillable' => true,
      ],
      [
        'id'            => 13,
        'title'         => $translator->translate('Mobile phone'),
        'type'          => 'input',
        'name'          => 'mobile_field',
        'fillable' => true,
      ],
      [
        'id'            => 14,
        'title'         => $translator->translate('Title'),
        'type'          => 'input',
        'name'          => 'title_field',
        'fillable' => true,
      ],
      [
        'id'            => 15,
        'title'         => $translator->translate('Category'),
        'type'          => 'input',
        'name'          => 'category_field',
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
        'fillable' => true,
      ],
      [
        'id'            => 17,
        'title'         => $translator->translatePlural('Email', 'Emails', 1),
        'type'          => 'input',
        'name'          => 'email1_field',
        'fillable' => true,
      ],
      [
        'id'            => 25,
        'title'         => $translator->translatePlural('Email', 'Emails', 1) . ' 2',
        'type'          => 'input',
        'name'          => 'email2_field',
        'fillable' => true,
      ],
      [
        'id'            => 26,
        'title'         => $translator->translatePlural('Email', 'Emails', 1) . ' 3',
        'type'          => 'input',
        'name'          => 'email3_field',
        'fillable' => true,
      ],
      [
        'id'            => 27,
        'title'         => $translator->translatePlural('Email', 'Emails', 1) . ' 4',
        'type'          => 'input',
        'name'          => 'email4_field',
        'fillable' => true,
      ],
      [
        'id'    => 18,
        'title' => $translator->translate('Use DN in the search'),
        'type'  => 'boolean',
        'name'  => 'use_dn',
        'fillable' => true,
      ],
      [
        'id'            => 20,
        'title'         => $translator->translate('Language'),
        'type'          => 'input',
        'name'          => 'language_field',
        'fillable' => true,
      ],
      [
        'id'            => 21,
        'title'         => $translator->translate('User attribute containing its groups'),
        'type'          => 'input',
        'name'          => 'group_field',
        'fillable' => true,
      ],
      [
        'id'            => 22,
        'title'         => $translator->translate('Filter to search in groups'),
        'type'          => 'textarea',
        'name'          => 'group_condition',
        'fillable' => true,
      ],
      [
        'id'            => 23,
        'title'         => $translator->translate('Group attribute containing its users'),
        'type'          => 'input',
        'name'          => 'group_member_field',
        'fillable' => true,
      ],
      // [
      //   'id'            => 24,
      //   'title'         => $translator->translate('Search type'),
      //   'type'          => 'specific', ??? TODO
      //   'name'          => 'group_search_type',
      //   'fillable' => true,
      // ],
      [
        'id'            => 28,
        'title'         => $translator->translate('Synchronization field'),
        'type'          => 'input',
        'name'          => 'sync_field',
        'fillable' => true,
      ],
      [
        'id'            => 29,
        'title'         => $translator->translate('Responsible'),
        'type'          => 'input',
        'name'          => 'responsible_field',
        'fillable' => true,
      ],
      [
        'id'            => 31,
        'title'         => $translator->translate('Domain name used by inventory tool'),
        'type'          => 'input',
        'name'          => 'inventory_domain',
        'fillable' => true,
      ],
    ];
  }
}
