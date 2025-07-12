<?php

declare(strict_types=1);

namespace App;

use ResourceBundle;

final class Translation
{
  /**
   * @var array<mixed>
   */
  public array $languages = [];

  /**
   * @var array<mixed>
   */
  public array $localesDefinitions = [];

  /**
   * @var string
   */
  private string $loadedLang = 'en-GB';

  /**
   * @var boolean
   */
  private bool $isAutoDetected = true;

  public function __construct()
  {
    $localeJsonContent = file_get_contents(__DIR__ . '/../locale/locales.json');
    if ($localeJsonContent === false)
    {
      throw new \Exception('Error when read locales.json file', 500);
    }
    $this->localesDefinitions = json_decode($localeJsonContent);
    $this->loadLanguageBasedOnLocales();
  }

  /**
   * Get the system locales availables and mis not defined in $this->localesDefinitions
   */
  private function loadLanguageBasedOnLocales(): void
  {
    $this->languages = [];
    // get locales installed on the operating system
    $locales = ResourceBundle::getLocales('');
    if ($locales === false)
    {
      throw new \Exception('Unable to read system locales', 500);
    }
    $localesDefinitions = [];
    foreach ($this->localesDefinitions as $lang)
    {
      $localesDefinitions[(string) str_replace('-', '_', $lang->locale)] = $lang;
    }

    foreach ($locales as $locale)
    {
      if (isset($localesDefinitions[$locale]))
      {
        $this->languages[] = $localesDefinitions[$locale];
      }
    }
  }

  public function loadLanguage(string|null $lang = null): void
  {
    if (is_null($lang))
    {
      $lang = $this->getAutodetectedLang();

      if (isset($GLOBALS['user_id']))
      {
        $user = \App\Models\User::where('id', $GLOBALS['user_id'])->first();
        if (is_null($user))
        {
          {
            throw new \Exception('Id not found', 404);
          }
        }
        if (!is_null($user->language))
        {
          $lang = $user->language;
          $this->isAutoDetected = false;
        }
      }
      elseif (isset($_COOKIE['lang']))
      {
        $found = false;
        foreach ($this->languages as $language)
        {
          if ($language->locale == $_COOKIE['lang'])
          {
            $found = true;
            break;
          }
        }
        if (!$found)
        {
          throw new \Exception('Cookie lang corrompu', 500);
        }
        $lang = $_COOKIE['lang'];
        $this->isAutoDetected = false;
      }
    }

    $genFolder = file_get_contents(__DIR__ . '/../locale/foldername');

    $this->loadedLang = $lang;
    $lang = str_replace('-', '_', $lang);

    $lang = $lang . '.UTF-8';
    putenv("LANG=" . $lang);

    setlocale(LC_ALL, $lang);
    $domain = "messages";
    bindtextdomain($domain, __DIR__ . '/../locale/' . $genFolder . '/');
    bind_textdomain_codeset($domain, 'UTF-8');
    textdomain($domain);
  }

  /**
   * Return preferred language (from HTTP headers, fallback to default FusionResolveIT lang).
   *
   * @return string
   */
  private function getAutodetectedLang(): string
  {
    // Extract accepted languages from headers
    $detectedLangs = [];
    $values = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    foreach ($values as $value)
    {
      $parts = explode(';q=', trim($value));
      // $language = str_replace('-', '_', $parts[0]);
      $language = $parts[0];

      $qfactor  = $parts[1] ?? 1; //q-factor defaults to 1
      $detectedLangs[$language] = $qfactor;
    }
    arsort($detectedLangs); // sort by qfactor

    foreach (array_keys($detectedLangs) as $language)
    {
      foreach ($this->localesDefinitions as $lang)
      {
        if ($language == $lang->locale)
        {
          $this->isAutoDetected = true;
          return $language;
        }
      }
    }
    return 'en-GB';
  }

  /**
   * Get values for language select dropdown
   *
   * @return array<string, string>
   */
  public function getDropdownValues(): array
  {
    $data = [
      'auto' => pgettext('global', 'Auto-detected'),
    ];
    foreach ($this->languages as $lang)
    {
      $data[$lang->locale] = strtolower($lang->country->name);
    }
    asort($data);
    return $data;
  }

  /**
   * Get the currently loaded lang, used for display
   */
  public function getCurrentLoadedLangForDisplay(): string
  {
    $list = $this->getDropdownValues();
    if ($this->isAutoDetected)
    {
      return $list['auto'] . ' (' .  $list[$this->loadedLang] . ' [' . $this->loadedLang . '])';
    }
    return $list[$this->loadedLang] . ' [' . $this->loadedLang . ']';
  }
}
