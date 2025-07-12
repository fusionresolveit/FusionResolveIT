<?php

declare(strict_types=1);

namespace App\v1\Controllers\Datastructures;

use Psr\Http\Message\ServerRequestInterface as Request;
use stdClass;

class Viewdata
{
  use Header;
  use Translation;
  use Information;

  // Attributes
  public stdClass $header;
  public object $data;

  /** @var array<mixed> */
  public array $relatedPages;
  public object $translation;
  public object $information;

  /** @var array<mixed> */
  public array $message;
  public string $basePath;

  public function __construct(\App\Models\Common $item, Request $request)
  {
    global $basePath;

    $lang = new \App\Translation();
    $lang->loadLanguage();

    $this->header = new stdClass();
    $this->data = (object)[];
    $this->relatedPages = [];
    $this->translation = (object)[];
    $this->information = (object)[];
    $this->message = [];

    $this->basePath = $basePath;

    $this->initHeaderData($item, $request);
    $this->initInformationData();

    $this->addTranslation('savebutton', pgettext('button', 'Save'));
    $this->addTranslation('newbutton', pgettext('button', 'New'));
    $this->addTranslation('columnsbutton', pgettext('button', 'Columns'));
    $this->addTranslation('searchbutton', pgettext('button', 'Search'));
    $this->addTranslation('viewbutton', pgettext('button', 'View'));
    $this->addTranslation('restorebutton', pgettext('button', 'Restore'));
    $this->addTranslation('deletebutton', pgettext('button', 'Delete'));
    $this->addTranslation('softdeletebutton', pgettext('button', 'Soft delete'));

    $this->addTranslation('menutitle', pgettext('menu', 'Menu'));
    $this->addTranslation('menubookmarks', pgettext('menu', 'Bookmarks'));
    $this->addTranslation('menuentity', npgettext('global', 'Entity', 'Entities', 1));

    $this->addTranslation('messagetitle', pgettext('session message', 'Action'));
    $session = new \SlimSession\Helper();
    if ($session->exists('message'))
    {
      $this->message = $session->get('message');
      $session->delete('message');
    }

    $this->addData('lang', $lang->getDropdownValues());
    $this->addData('currentLang', $lang->getCurrentLoadedLangForDisplay());
  }

  public function addData(string $key, mixed $value): void
  {
    $this->data->{$key} = $value;
  }

  /**
   * @param array<mixed> $data
   */
  public function addRelatedPages(array $data): void
  {
    $this->relatedPages = $data;
  }

  // public function addMessage($message)
  // {
  //   $this->message[] = $message;
  // }
}
