<?php

declare(strict_types=1);

namespace App\v1\Controllers\Cli;

use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Securitylogin extends Command
{
  public function __construct()
  {
    parent::__construct('securitylogin', 'Reset the security login attempt on account');

    $this
      ->option('--id', 'id of account')
      ->option('--login', 'login name of the account')
      ->usage(
        '--id 42 <eol/>' .
        '--login admin<eol/>'
      );
  }

  public function execute(string|null $id, string|null $login): int
  {
    $color = new Color();
    $writer = new Writer();
    $writer->comment('=> Reset the security login attempt on account', true);

    if (is_null($id) && is_null($login))
    {
      echo $color->error('id or login required');
      $writer->write("\n\n");
      return -1;
    }

    if (!is_null($id))
    {
      $user = \App\Models\User::where('id', $id)->first();
      if (!is_null($user))
      {
        $user->setAttribute('security_attempt', 0);
        $user->setAttribute('security_last_attempt', null);
        $user->save();
      } else {
        echo $color->error('Account not exists');
        $writer->write("\n\n");
        return -1;
      }
    }
    else
    {
      $user = \App\Models\User::where('name', $login)->first();
      if (!is_null($user))
      {
        $user->setAttribute('security_attempt', 0);
        $user->setAttribute('security_last_attempt', null);
        $user->save();
      } else {
        echo $color->error('Account not exists');
        $writer->write("\n\n");
        return -1;
      }
    }
    echo $color->ok('Reset on account completed');
    $writer->write("\n\n");

    return 0;
  }
}
