<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Authldap
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name'      => $translator->translate('Name'),
      'active'      => $translator->translate('Active'),
      'host'      => $translator->translate('Server'),
      'port' => $translator->translatePlural('Port', 'Ports', 1),
      'default' => $translator->translate('Default server'),
      'basedn' => $translator->translate('BaseDN'),
      'rootdn' => $translator->translate('RootDN'),
      'rootdn_passwd' => $translator->translate('RootDN password'),
      'condition' => $translator->translate('Connection filter'),
      'login_field' => $translator->translate('Login field'),
      'realname_field' => $translator->translate('Surname'),
      'firstname_field' => $translator->translate('First name'),
      'phone_field' => 'Phone',
      'phone2_field' => $translator->translate('Phone 2'),
      'mobile_field' => $translator->translate('Mobile phone'),
      'title_field' => $translator->translate('Title'),
      'category_field' => $translator->translate('Category'),
      'comment' => $translator->translate('Comments'),
      'email1_field' => $translator->translatePlural('Email', 'Emails', 1),
      'email2_field' => $translator->translatePlural('Email', 'Emails', 1) . ' 2',
      'email3_field' => $translator->translatePlural('Email', 'Emails', 1) . ' 3',
      'email4_field' => $translator->translatePlural('Email', 'Emails', 1) . ' 4',
      'use_dn' => $translator->translate('Use DN in the search'),
      'language_field' => $translator->translate('Language'),
      'group_field' => $translator->translate('User attribute containing its groups'),
      'group_condition' => $translator->translate('Filter to search in groups'),
      'group_member_field' => $translator->translate('Group attribute containing its users'),
      'sync_field' => $translator->translate('Synchronization field'),
      'responsible_field' => $translator->translate('Responsible'),
      'inventory_domain' => $translator->translate('Domain name used by inventory tool'),
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
    //   'title'         => $translator->translate('Search type'),
    //   'type'          => 'specific', ??? TODO
    //   'name'          => 'group_search_type',
    //   'fillable' => true,
    // ],
  }
}
