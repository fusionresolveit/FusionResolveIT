<?php

declare(strict_types=1);

namespace App\v1\Controllers\Cli;

use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Migration extends Command
{
  public function __construct()
  {
    parent::__construct('migrate', 'Install / update FusionResolveIT');
  }

  public function execute()
  {
    $color = new Color();
    $writer = new Writer();
    echo $color->comment('=> The database will be installed / updated');
    $writer->write("\n");

    $phinx = new PhinxApplication();
    $phinxCommand = $phinx->find('migrate');
    $phinxConfig = include(__DIR__ . '/../../../../phinx.php');

    $arguments = [
      'command'         => 'migrate',
      '--environment'   => 'production',
    ];
    $input = new ArrayInput($arguments);
    $output = new ConsoleOutput();
    $returnCode = $phinxCommand->run(new ArrayInput($arguments), $output);
    if ($returnCode != 0)
    {
      echo $color->error('The database is not correctly installed');
      return $returnCode;
    }
    echo $color->ok('The database is up to date');
    $writer->write("\n\n");

    $writer->green('FusionResolveIT is up to date.');
    $writer->boldGreen(' Enjoy!');
    $writer->write("\n\n");
  }
}
