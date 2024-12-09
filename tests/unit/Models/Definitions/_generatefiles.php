<?php

// This file is used to create missing files for Definition tests.
// Due to many files, more easier to generate them with this script

$dir = __DIR__ . '/../../../../src/Models/Definitions';
$files = scandir($dir);

foreach ($files as $file)
{
  if (!strstr($file, '.php'))
  {
    continue;
  }
  $className = str_replace('.php', '', $file);
  // If test file not exists, we generate it
  if (!file_exists($className . 'Test.php'))
  {
    $content = "<?php\n";
    $content .= "\n";
    $content .= "declare(strict_types=1);\n";
    $content .= "\n";
    $content .= "namespace Tests\unit\Models\Definitions;\n";
    $content .= "\n";
    $content .= "/**\n";
    $content .= " * @covers \App\Models\Definitions\\" . $className . "\n";
    $content .= " */\n";
    $content .= "final class " . $className . "Test extends Common\n";
    $content .= "{\n";
    $content .= "  protected \$className = '" . $className . "';\n";
    $content .= "}\n";
    file_put_contents($className . "Test.php", $content);
  }
}
