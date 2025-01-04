<?php

declare(strict_types=1);

namespace App\v1\Controllers\Cli;

use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Rollback extends Command
{
  public function __construct()
  {
    parent::__construct('rollback', 'Rollback database migration');

    $this
      ->option('-a --all', 'Rollback to the beginning')
      ->option('-t --target', 'Rollback to the target number (number in the filename)')
      ->usage(
        '--all ## rollback the migration to the beginning<eol/>' .
        '--target 20241017094936 ## rollback the migration to the migration file' .
          ' 20241017094936_authsso_migration.php<eol/>'
      );
  }

  public function execute($target)
  {
    $color = new Color();
    $writer = new Writer();
    echo $color->comment('=> The database migration will be rollback');
    $writer->write("\n");

    $phinx = new PhinxApplication();
    $phinxCommand = $phinx->find('migrate');

    $arguments = [
      'command'         => 'roolback',
      '--environment'   => 'production',
      '--target'        => '0',
    ];
    if (!is_null($target))
    {
      $arguments['--target'] = $target;
    }
    $input = new ArrayInput($arguments);
    $output = new ConsoleOutput();

    // Check if lock file present
    $databaseLock = file_exists(__DIR__ . '/../../../../database.lock');
    if ($databaseLock)
    {
      echo $color->error('The rollback will not run because the database lock is present, check the documentation');
      $writer->write("\n\n");
      return;
    }

    $returnCode = $phinxCommand->run(new ArrayInput($arguments), $output);
    if ($returnCode != 0)
    {
      echo $color->error('There is a problem in rollback, please open an issue on ' .
        'https://github.com/fusionresolveit/FusionResolveIT');
      $writer->write("\n\n");
      return $returnCode;
    }
    echo $color->ok('The database has been rollback');
    $writer->write("\n\n");

    echo $color->warn('With the rollback, the application can\'t works');
    $writer->write("\n\n");
  }
}
