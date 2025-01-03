<?php

declare(strict_types=1);

namespace App\Models;

class Authldap extends Common
{
  protected $definition = '\App\Models\Definitions\Authldap';
  protected $titles = ['LDAP', 'LDAP'];
  protected $icon = 'address book outline';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
  ];
}
