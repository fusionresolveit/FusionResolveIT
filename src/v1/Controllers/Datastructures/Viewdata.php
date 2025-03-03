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
    global $basePath, $translator;

    $this->header = new stdClass();
    $this->data = (object)[];
    $this->relatedPages = [];
    $this->translation = (object)[];
    $this->information = (object)[];
    $this->message = [];

    $this->basePath = $basePath;

    $this->initHeaderData($item, $request);
    $this->initInformationData();

    $this->addTranslation('savebutton', $translator->translate('Save'));
    $this->addTranslation('newbutton', $translator->translate('New'));

    $session = new \SlimSession\Helper();
    if ($session->exists('message'))
    {
      $this->message = $session->get('message');
      $session->delete('message');
    }
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
