<?php

declare(strict_types=1);

namespace App\v1\Controllers\Cli;

use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Translation extends Command
{
  public function __construct()
  {
    parent::__construct('translation', 'Generate the translation files (for developers / translators mainly)');
  }

  public function execute(): int
  {
    $color = new Color();
    $writer = new Writer();
    $writer->comment('=> The translation files will be generated', true);

    // search in locale folder
    $prefix = __DIR__ . '/../../../../locale/';
    $items = scandir($prefix);
    foreach ($items as $item) {
      if (is_dir($prefix . $item))
      {
        if (str_starts_with($item, 'gen_'))
        {
          // delete all folders starts with 'gen_'
          $this->rrmdir($prefix . $item);
        }
      }
    }

    // generate new destination folder
    $newFolder = uniqid('gen_');
    $prefix .= $newFolder . '/';

    $prefixSource = __DIR__ . '/../../../../locale/source/';
    $items = scandir($prefixSource);
    foreach ($items as $item) {
      if ($item != '.' && $item != '..' && $item != 'messages.pot')
      {
        $newForlderOfLang = $prefix . $item . '/LC_MESSAGES';
        mkdir($newForlderOfLang, 0777, true);
        exec('msgfmt ' . $prefixSource . $item . '/messages.po -o ' . $newForlderOfLang . '/messages.mo');
        $writer->green('Lang ' . $item . ' compiled.');
        $writer->write("\n");
      }
    }

    // write this name into file 'foldername'
    file_put_contents(__DIR__ . '/../../../../locale/foldername', $newFolder);

    $writer->boldGreen('The translation files are all generated.');
    $writer->write("\n\n");
    return 0;
  }

  private function rrmdir(string $dir): void
  {
    if (is_dir($dir))
    {
      $objects = scandir($dir);
      foreach ($objects as $object)
      {
        if ($object != '.' && $object != '..')
        {
          if (filetype($dir . '/' . $object) == "dir")
          {
            $this->rrmdir($dir . '/' . $object);
          } else {
            unlink($dir . '/' . $object);
          }
        }
      }
      rmdir($dir);
    }
  }
}
