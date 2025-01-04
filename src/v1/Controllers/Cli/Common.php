<?php

declare(strict_types=1);

namespace App\v1\Controllers\Cli;

use Ahc\Cli\Helper\Terminal;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;

class Common
{
  public function displayLogo()
  {
    $writer = new Writer();

    $logo = "
 ================================================================================
     ______           _             ____                  __           __________
    / ____/_  _______(_)___  ____  / __ \___  _________  / /   _____  /  _/_  __/
   / /_  / / / / ___/ / __ \/ __ \/ /_/ / _ \/ ___/ __ \/ / | / / _ \ / /  / /
  / __/ / /_/ (__  ) / /_/ / / / / _, _/  __(__  ) /_/ / /| |/ /  __// /  / /
 /_/    \__,_/____/_/\____/_/ /_/_/ |_|\___/____/\____/_/ |___/\___/___/ /_/


  FusionResolveIT cli tool

 ================================================================================

";
    $writer->white($logo);

    $terminal = new Terminal();
    $width = $terminal->width();
    $spaces = str_repeat(' ', ($width - 80));

    $justGreen = ['second' => ['fg' => \Ahc\Cli\Output\Color::GREEN]];
    $justRed = ['second' => ['fg' => \Ahc\Cli\Output\Color::RED]];

    $writer->boldGreen(" Environment:\n");
    $writer->justify(' PHP Version', PHP_VERSION . $spaces, $justGreen);
    $writer->justify(' FusionResolveIT Version', '1.0.0' . $spaces, $justGreen);
    // $writer->justify(' Debug mode', 'true' . $spaces, $justRed);
    $writer->white("\n ================================================================================\n");
  }
}
