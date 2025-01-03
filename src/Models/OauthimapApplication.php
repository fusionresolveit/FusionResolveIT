<?php

declare(strict_types=1);

namespace App\Models;

class OauthimapApplication extends Common
{
  protected $table = 'glpi_plugin_oauthimap_applications';
  protected $definition = '\App\Models\Definitions\OauthimapApplication';
  protected $titles = ['Oauth IMAP application', 'Oauth IMAP applications'];
  protected $icon = 'edit';
}
