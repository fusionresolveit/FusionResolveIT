<?php

declare(strict_types=1);

$localeJsonContent = file_get_contents(__DIR__ . '/locales.json');
if ($localeJsonContent === false)
{
  throw new \Exception('Error when read locales.json file', 500);
}
$localesDefinitions = json_decode($localeJsonContent);
$cleanedLocales = [];
foreach ($localesDefinitions as $localesDefinition)
{
    unset($localesDefinition->language->countries);
    $cleanedLocales[] = $localesDefinition;
}

file_put_contents(__DIR__ . '/../../locale/locales.json', json_encode($cleanedLocales, JSON_PRETTY_PRINT));

