<?php

declare(strict_types=1);

$pluralStrings = [];
$singleStrings = [];
$newFolder = uniqid('gen_');

extractGettext(__DIR__ . '/../../src');
loadMessagesPotFile();
testStrings();

/**
 * Extract gettext from source code
 */
function extractGettext($dir)
{
  $items = scandir($dir);

  foreach ($items as $item)
  {
  if ($item == '.' || $item == '..')
    {
      continue;
    }
    else if (is_dir($dir . '/' . $item))
    {
      // it's folder
      extractGettext($dir . '/' . $item);
    } else {
      // it's file
      parseFile($dir . '/' . $item);
    }
  }
}


/**
 * parse file to extract gettext
 */
function parseFile($file) {
  global $pluralStrings, $singleStrings;

  $lines = file($file, FILE_SKIP_EMPTY_LINES);
  $recomposeGettext = '';
  $recomposeNb = 0;
  

  foreach ($lines as $line) {
    if ($recomposeNb > 0)
    {
      $recomposeGettext .= trim($line) . ' ';
      $recomposeNb--;
      if ($recomposeNb > 0)
      {
        continue;
      } else {
        $line = $recomposeGettext;
      }
    }
    // for the case we have multiple gettext on 1 line, always separated by ' . '
    $splLines = explode(' . ', $line);
    foreach ($splLines as $splLine)
    {
      if (strstr($splLine, 'npgettext'))
      {
        // plural
        preg_match('/(npgettext\()\n$/', $splLine, $matches);
        if (count($matches) == 2)
        {
          $recomposeGettext = $matches[1];
          $recomposeNb = 3;
          continue;
        }

        $matches = [];
        preg_match('/npgettext\((?:[\'"]+)([\w ]+)(?:[\'"]+), \'([\w ,\&\/\!\+\?\:\.\$\%\-\(\)"]+)\', \'([\w ,\&\/\!\+\?\:\.\$\%\-\(\)"]+)\',/', $splLine, $matches);
        if (count($matches) == 4)
        {
          if (!isset($pluralStrings[$matches[1]]))
          {
            $pluralStrings[$matches[1]] = [];
          }
          $pluralStrings[$matches[1]][$matches[2]] = $matches[3];
        } else {
          $matches = [];
          preg_match('/npgettext\((?:[\'"]+)([\w ]+)(?:[\'"]+), "([\w ,\&\/\!\+\?\:\.\$\%\-\(\)\']+)", "([\w ,\&\/\!\+\?\:\.\$\%\-\(\)\']+)",/', $splLine, $matches);
          if (count($matches) == 4)
          {
            if (!isset($pluralStrings[$matches[1]]))
            {
              $pluralStrings[$matches[1]] = [];
            }
            $pluralStrings[$matches[1]][$matches[2]] = $matches[3];
          } else {
            throw new Exception('npgettext not right in file ' . $file . ' for line: ' . $line, 1);
          }
        }
      } elseif (strstr($splLine, 'pgettext'))
      {
        // single
        preg_match('/(pgettext\()\n$/', $splLine, $matches);
        if (count($matches) == 2)
        {
          $recomposeGettext = $matches[1];
          $recomposeNb = 2;
          continue;
        }

        $matches = [];
        preg_match('/pgettext\((?:[\'"]+)([\w ]+)(?:[\'"]+), \'([\w ,\&\/\!\+\?\:\.\$\%\-\(\)"]+)\'\)/', $splLine, $matches);
        if (count($matches) == 3)
        {
          if (!isset($singleStrings[$matches[1]]))
          {
            $singleStrings[$matches[1]] = [];
          }
          $singleStrings[$matches[1]][$matches[2]] = 1;
        } else {
          $matches = [];
          preg_match('/pgettext\((?:[\'"]+)([\w ]+)(?:[\'"]+), "([\w ,\&\/\!\+\?\:\.\$\%\-\(\)\']+)"\)/', $splLine, $matches);
          if (count($matches) == 3)
          {
            if (!isset($singleStrings[$matches[1]]))
            {
              $singleStrings[$matches[1]] = [];
            }
            $singleStrings[$matches[1]][$matches[2]] = 1;
          } else {
            throw new Exception('pgettext not right in file ' . $file . ' for line: ' . $line, 1);
          }
        }
      }
    }
  }
}


/**
 * Load the messages.pot file (source of translation)
 */
function loadMessagesPotFile()
{
  global $newFolder;

  mkdir('/tmp/' . $newFolder);
  mkdir('/tmp/' . $newFolder . '/en_GB');
  mkdir('/tmp/' . $newFolder . '/en_GB/LC_MESSAGES');

  // Fill data in po file
  $lines = file(__DIR__ . '/../../locale/source/messages.pot');
  $newLines = "";
  foreach ($lines as $line_num => $line)
  {
    if ($line_num < 15)
    {
      $newLines .= $line;
    } else {
      $newLines .= str_replace('""', '"xx"', $line);
    }
    $newLines .= "\n";
  }
  $ret = file_put_contents('/tmp/' . $newFolder . '/en_GB/LC_MESSAGES/messages.po', $newLines);
  if ($ret === false)
  {
    echo 'The po file in tmp folder has not be copied';
    echo "\n";
    exit(1);
  }

  $output = null;
  $retval = null;
  exec('msgfmt -o /tmp/' . $newFolder . '/en_GB/LC_MESSAGES/messages.mo /tmp/' . $newFolder . '/en_GB/LC_MESSAGES/messages.po', $output, $retval);
  echo "msgfmt command returned with status $retval and output:\n";
  print_r($output);
    
  $lang = 'en_GB.UTF-8';
  putenv("LANG=" . $lang);
  setlocale(LC_ALL, $lang);
  $domain = "messages";
  $ret = bindtextdomain($domain, '/tmp/' . $newFolder . '/');
  echo 'bindtextdomain: ' . $ret;
  echo "\n";
  $ret = bind_textdomain_codeset($domain, 'UTF-8');
  echo 'bind_textdomain_codeset: ' . $ret;
  echo "\n";
  $ret = textdomain($domain);
  echo 'textdomain: ' . $ret;
  echo "\n";

  $currentLocale = setlocale(LC_ALL, 0);
  echo 'Current locale defined: ' . $currentLocale;   
  echo "\n";
  if ($currentLocale !== 'en_GB.UTF-8')
  {
  exit(1);
  }
}

function testStrings()
{
  global $pluralStrings, $singleStrings, $newFolder;

  $isError = false;

  // plural
  foreach ($pluralStrings as $context => $values)
  {
    foreach ($values as $singular => $plural)
    {
      $context_singular = "$context\004$singular";
      $context_plural = "$context\004$plural";

      $translation = ngettext($context_singular, $context_plural, 2);

      // If the translation was not found...
      if ($translation === $context_singular || $translation === $context_plural) {
        echo 'ERROR: The plural context `' . $context . '` + values: `' . $singular . '`, `' . $plural . '` is missing in messages.pot file';
        echo "\n";
        $isError = true;
      }
    }
  }

  // single
  foreach ($singleStrings as $context => $values)
  {
    foreach (array_keys($values) as $message)
    {
      $context_message = "$context\004$message";

      $translation = gettext($context_message);

      // If the translation was not found...
      if ($translation === $context_message) {
        echo 'ERROR: The singular context `' . $context . '` + value: `' . $message . '` is missing in messages.pot file';
        echo "\n";
        $isError = true;
      }
    }
  }
  // delete files / folder
  unlink('/tmp/' . $newFolder . '/en_GB/LC_MESSAGES/messages.po');
  unlink('/tmp/' . $newFolder . '/en_GB/LC_MESSAGES/messages.mo');
  rmdir('/tmp/' . $newFolder . '/en_GB/LC_MESSAGES');
  rmdir('/tmp/' . $newFolder . '/en_GB');
  rmdir('/tmp/' . $newFolder);

  if ($isError)
  {
    exit(1);
  }
}

