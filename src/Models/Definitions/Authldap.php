<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Authldap
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'               => pgettext('global', 'Name'),
      'active'             => pgettext('global', 'Active'),
      'host'               => pgettext('LDAP parameter', 'Server'),
      'port'               => npgettext('LDAP parameter', 'Port', 'Ports', 1),
      'default'            => pgettext('LDAP parameter', 'Default server'),
      'basedn'             => pgettext('LDAP parameter', 'BaseDN'),
      'rootdn'             => pgettext('LDAP parameter', 'RootDN'),
      'rootdn_passwd'      => pgettext('LDAP parameter', 'RootDN password'),
      'condition'          => pgettext('LDAP parameter', 'Connection filter'),
      'login_field'        => pgettext('LDAP parameter', 'Login field'),
      'realname_field'     => pgettext('user parameter', 'Surname'),
      'firstname_field'    => pgettext('user parameter', 'First name'),
      'phone_field'        => pgettext('user parameter', 'Phone'),
      'phone2_field'       => pgettext('user parameter', 'Phone 2'),
      'mobile_field'       => pgettext('user parameter', 'Mobile phone'),
      'title_field'        => pgettext('user parameter', 'Title'),
      'category_field'     => pgettext('user parameter', 'Category'),
      'comment'            => npgettext('global', 'Comment', 'Comments', 2),
      'email1_field'       => npgettext('user parameter', 'Email', 'Emails', 1),
      'email2_field'       => npgettext('user parameter', 'Email', 'Emails', 1) .
                              ' 2',
      'email3_field'       => npgettext('user parameter', 'Email', 'Emails', 1) .
                              ' 3',
      'email4_field'       => npgettext('user parameter', 'Email', 'Emails', 1) .
                              ' 4',
      'use_dn'             => pgettext('LDAP parameter', 'Use DN in the search'),
      'language_field'     => pgettext('user parameter', 'Language'),
      'group_field'        => pgettext('LDAP parameter', 'User attribute containing its groups'),
      'group_condition'    => pgettext('LDAP parameter', 'Filter to search in groups'),
      'group_member_field' => pgettext('LDAP parameter', 'Group attribute containing its users'),
      'sync_field'         => pgettext('LDAP parameter', 'Synchronization field'),
      'responsible_field'  => pgettext('user parameter', 'Responsible'),
      'inventory_domain'   => pgettext('LDAP parameter', 'Domain name used by inventory tool'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(30, $t['active'], 'boolean', 'is_active', fillable: true));
    $defColl->add(new Def(3, $t['host'], 'input', 'host', fillable: true));
    $defColl->add(new Def(4, $t['port'], 'input', 'port', fillable: true));
    $defColl->add(new Def(7, $t['default'], 'boolean', 'is_default', fillable: true));
    $defColl->add(new Def(5, $t['basedn'], 'input', 'basedn', fillable: true));
    $defColl->add(new Def(40, $t['rootdn'], 'input', 'rootdn', fillable: true));
    $defColl->add(new Def(41, $t['rootdn_passwd'], 'inputpassword', 'rootdn_passwd', fillable: true));
    $defColl->add(new Def(6, $t['condition'], 'textarea', 'condition', fillable: true));
    $defColl->add(new Def(8, $t['login_field'], 'input', 'login_field', fillable: true));
    $defColl->add(new Def(9, $t['realname_field'], 'input', 'realname_field', fillable: true));
    $defColl->add(new Def(10, $t['firstname_field'], 'input', 'firstname_field', fillable: true));
    $defColl->add(new Def(11, $t['phone_field'], 'input', 'phone_field', fillable: true));
    $defColl->add(new Def(12, $t['phone2_field'], 'input', 'phone2_field', fillable: true));
    $defColl->add(new Def(13, $t['mobile_field'], 'input', 'mobile_field', fillable: true));
    $defColl->add(new Def(14, $t['title_field'], 'input', 'title_field', fillable: true));
    $defColl->add(new Def(15, $t['category_field'], 'input', 'category_field', fillable: true));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(17, $t['email1_field'], 'input', 'email1_field', fillable: true));
    $defColl->add(new Def(25, $t['email2_field'], 'input', 'email2_field', fillable: true));
    $defColl->add(new Def(26, $t['email3_field'], 'input', 'email3_field', fillable: true));
    $defColl->add(new Def(27, $t['email4_field'], 'input', 'email4_field', fillable: true));
    $defColl->add(new Def(18, $t['use_dn'], 'boolean', 'use_dn', fillable: true));
    $defColl->add(new Def(20, $t['language_field'], 'input', 'language_field', fillable: true));
    $defColl->add(new Def(21, $t['group_field'], 'input', 'group_field', fillable: true));
    $defColl->add(new Def(22, $t['group_condition'], 'textarea', 'group_condition', fillable: true));
    $defColl->add(new Def(23, $t['group_member_field'], 'input', 'group_member_field', fillable: true));
    $defColl->add(new Def(28, $t['sync_field'], 'input', 'sync_field', fillable: true));
    $defColl->add(new Def(29, $t['responsible_field'], 'input', 'responsible_field', fillable: true));
    $defColl->add(new Def(31, $t['inventory_domain'], 'input', 'inventory_domain', fillable: true));

    return $defColl;

    // [
    //   'id'            => 24,
    //   'title'         => 'Search type',
    //   'type'          => 'specific', ??? TODO
    //   'name'          => 'group_search_type',
    //   'fillable' => true,
    // ],
  }
}
