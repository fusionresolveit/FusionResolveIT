<?php

declare(strict_types=1);

namespace App\v1\Controllers\Cli;

use Ahc\Cli\Helper\Terminal;
use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;

use function DI\string;

class Prerequisites extends Command
{
  protected $justGreen = ['second' => ['fg' => \Ahc\Cli\Output\Color::GREEN]];
  protected $justRed = ['second' => ['fg' => \Ahc\Cli\Output\Color::RED]];
  protected $spaces = 80;

  public function __construct()
  {
    parent::__construct('prerequisites', 'Check all prerequisites are ok');
  }

  public function execute()
  {
    $color = new Color();
    $writer = new Writer();
    echo $color->comment('This is the prerequisites:');
    $writer->write("\n\n");

    $terminal = new Terminal();
    $width = $terminal->width();
    $this->spaces = str_repeat(' ', ($width - 80));

    $writer->boldGreen(" Prerequisites:\n");

    if (
        version_compare(PHP_VERSION, '8.2.0', '>=') &&
        version_compare(PHP_VERSION, '8.4.0', '<')
    ) {
      $writer->justify(' PHP Version', PHP_VERSION . $this->spaces, $this->justGreen);
    } else {
      $writer->justify(' PHP Version', PHP_VERSION . $this->spaces, $this->justRed);
      echo $color->comment('  Need version 8.2 or 8.3');
      $writer->write("\n");
    }

    $extensions = [
      'ctype',
      'curl',
      'fileinfo',
      'filter',
      'gd',
      'iconv',
      'imap',
      'intl',
      'json',
      'mbstring',
      'pdo',
      'session',
      'simplexml',
      'sodium',
      'zlib',
    ];
    foreach ($extensions as $name)
    {
      $this->checkPHPExtension($name, $writer);
    }

    // check database
    $dbConfig = include(__DIR__ . '/../../../../phinx.php');

    $myDatabase = $dbConfig['environments'][$dbConfig['environments']['default_environment']];
    if ($myDatabase['adapter'] == 'mysql')
    {
      $capsule = $this->databaseConnect();

      $connection = $capsule->getConnection();
      $version = $connection->select('SELECT VERSION()');
      $spl = explode('-', $version[0]->{'VERSION()'});
      if (!strstr(strtolower($spl[1]), 'mariadb'))
      {
        $writer->justify(' Database engine', $spl[1] . ' not supported' . $this->spaces, $this->justRed);
        echo $color->comment('  Need MariaDB database');
        $writer->write("\n");
      } else {
        $writer->justify(' Database engine', $spl[1] . $this->spaces, $this->justGreen);
        // check version
        $versions = explode('.', $spl[0]);
        $supportedVersions = ['10.5', '10.6', '10.11', '11.4'];
        if (in_array(($versions[0] . '.' . $versions[1]), $supportedVersions))
        {
          $writer->justify(' Database version', $versions[0] . '.' . $versions[1] . $this->spaces, $this->justGreen);
        } else {
          $writer->justify(' Database version', $versions[0] . '.' . $versions[1] . $this->spaces, $this->justRed);
          echo $color->comment('  Need MariaDB version compatible: ' . implode(', ', $supportedVersions));
          $writer->write("\n");
        }
      }
    }
    elseif ($myDatabase['adapter'] == 'pgsql')
    {
      $writer->justify(' Database engine', $myDatabase['adapter'] . $this->spaces, $this->justGreen);
      $supportedVersions = ['13', '14', '15', '16', '17'];

      $capsule = $this->databaseConnect();

      $connection = $capsule->getConnection();
      $version = $connection->select('SELECT VERSION()');

      preg_match('/PostgreSQL (\d+)/', $version[0]->version, $matches, PREG_OFFSET_CAPTURE);
      if (count($matches) >= 2)
      {
        if (in_array($matches[1][0], $supportedVersions))
        {
          $writer->justify(' Database version', $matches[1][0] . $this->spaces, $this->justGreen);
        } else {
          $writer->justify(' Database version', $matches[1][0] . $this->spaces, $this->justRed);
          echo $color->comment('  Need PostgreSQL version compatible: ' . implode(', ', $supportedVersions));
          $writer->write("\n");
        }
      } else {
        // unable to verify the version
        $writer->justify(' Database version', 'unknown' . $this->spaces, $this->justRed);
        echo $color->comment('  Need PostgreSQL version compatible: ' . implode(', ', $supportedVersions));
        $writer->write("\n");
      }
    } else {
      $writer->justify(' Database engine', $myDatabase['adapter'] . ' not supported' . $this->spaces, $this->justRed);
      echo $color->comment('  Need engine mysql or pgsql into phinx.php file');
      $writer->write("\n");
    }
    $writer->write("\n\n");
  }

  private function checkPHPExtension($extensionName, $writer)
  {
    if (extension_loaded($extensionName))
    {
      $writer->justify(' PHP extension' . $extensionName, 'installed' . $this->spaces, $this->justGreen);
    } else {
      $writer->justify(' PHP extension' . $extensionName, 'missing' . $this->spaces, $this->justRed);
    }
  }

  private function databaseConnect()
  {
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
    $capsule = new Capsule();
    $capsule->addConnection($configdb);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
  }
}
