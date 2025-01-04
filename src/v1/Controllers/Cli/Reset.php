<?php

declare(strict_types=1);

namespace App\v1\Controllers\Cli;

use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Reset extends Command
{
  public function __construct()
  {
    parent::__construct('reset', 'Reset the database');
  }

  public function execute()
  {
    $color = new Color();
    $writer = new Writer();
    $writer->comment('=> The database will be reset', true);

    // Check if lock file present
    $databaseLock = file_exists(__DIR__ . '/../../../../database.lock');
    if ($databaseLock)
    {
      echo $color->error('The database reset will not run because the database lock is present,' .
        ' check the documentation');
      $writer->write("\n\n");
      return;
    }

    $capsule = new Capsule();

    $dbConfig = include(__DIR__ . '/../../../../phinx.php');

    $myDatabase = $dbConfig['environments'][$dbConfig['environments']['default_environment']];
    $configdb = [
      'driver'    => $myDatabase['adapter'],
      'host'      => $myDatabase['host'],
      'database'  => $myDatabase['name'],
      'username'  => $myDatabase['user'],
      'password'  => $myDatabase['pass'],
      'charset'   => $myDatabase['charset'],
      'collation' => $myDatabase['collation'],
    ];
    $capsule->addConnection($configdb);
    $capsule->setEventDispatcher(new Dispatcher(new Container()));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    $writer->green(' -> Droping the tables ');

    try {
      Capsule::schema()->dropAllTables();
      $writer->boldGreen('OK', true);
    } catch (\Exception $e) {
      $writer->boldRed('Failed', true);
      $writer->error($e->getMessage(), true);
      return -1;
    }

    $install_command = new Migration();
    return $install_command->execute();
  }
}
