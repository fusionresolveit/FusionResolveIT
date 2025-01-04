<?php

declare(strict_types=1);

namespace App\v1\Controllers\Cli;

use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;

class Documentation extends Command
{
  public function __construct()
  {
    parent::__construct('documentation', 'Give the link of the documentation');
  }

  public function execute()
  {
    $color = new Color();
    $writer = new Writer();
    echo $color->comment('This is the documentation link:');
    $writer->write("\n\n");

    $writer->green('https://documentation.fusionresolveit.org');
    $writer->write("\n\n");
  }
}
