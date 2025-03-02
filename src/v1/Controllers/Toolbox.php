<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use League\HTMLToMarkdown\HtmlConverter;
use League\CommonMark\CommonMarkConverter;

final class Toolbox
{
  /**
   * Clean integer string value (strip all chars not - and spaces )
   *
   * @since versin 0.83.5
   *
   * @param string  $integer  integer string
   *
   * @return string  clean integer
   **/
  public static function cleanInteger($integer)
  {
    $newValue = preg_replace("/[^0-9-]/", "", $integer);
    if (is_null($newValue))
    {
      return '';
    }
    return $newValue;
  }

  public static function getRootPath(Request $request): string
  {
    $routeContext = RouteContext::fromRequest($request);
    return $routeContext->getBasePath();
  }

  /**
   * strtolower function for utf8 string
   *
   * @param string|null $str
   *
   * @return string|null  lower case string
  **/
  public static function strtolower(string|null $str)
  {
    if (is_null($str))
    {
      return null;
    }
    return mb_strtolower($str, "UTF-8");
  }

  /**
   * Convert HTML text into markdown
   */
  public static function convertHtmlToMarkdown(string $text): string
  {
    $converter = new HtmlConverter();
    $converter->getConfig()->setOption('strip_tags', true);

    return $converter->convert(html_entity_decode($text));
  }

  /**
   * Convert markdown text into HTML
   */
  public static function convertMarkdownToHtml(string|null $text): string
  {
    if (is_null($text))
    {
      return '';
    }
    $converter = new CommonMarkConverter([
      'html_input' => 'strip',
      'allow_unsafe_links' => false,
    ]);
    return $converter->convert($text)->getContent();
  }

  /**
   * used in migration
   */
  public static function fixDate(string $myDate): string|null
  {
    if (
        $myDate == '0000-00-00 00:00:00' ||
        $myDate == '0000-00-00'
    )
    {
      return null;
    }
    return $myDate;
  }

  /**
   * Add message to the session
   * @param $message string
   * @param $type string=success|error
   */
  public static function addSessionMessage(string $message, string $type = 'success'): void
  {
    // add message to session
    $session = new \SlimSession\Helper();
    if (!isset($session['message']))
    {
      $session['message'] = [];
    }
    $session->merge(
      'message',
      [
        [
          'message' => $message,
          'type'    => $type,
        ]
      ]
    );
  }
}
