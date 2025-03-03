<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Externallink
{
  /**
   * @param array<string, string> $args
   */
  public function showSubExternalLinks(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $computermodelclass = str_ireplace('\\v1\\Controllers\\', '\\Models\\', get_class($this));

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\LinkItemtype();
    $externallinks = $item2::with('links')->where('item_type', $computermodelclass)->get();

    $rootUrl = $this->genereRootUrl($request, '/externallinks');

    $myExternalLinks = [];
    // foreach ($externallinks as $externallink)
    // {
    //   $name = '';
    //   $open_window = 0;
    //   $link = '';
    //   $data = '';
    //   $generate = '';

    //   if ($externallink->links !== null)
    //   {
    //     $name = $externallink->links->name;

    //     $open_window = $externallink->links->open_window;

    //     $link = $externallink->links->link;

    //     $data = $externallink->links->data;

    //     $location_id = '';
    //     $location_name = '';
    //     if ($myItem->location !== null)
    //     {
    //       $location_id = $myItem->location->id;
    //       $location_name = $myItem->location->name;
    //     }

    //     $domains = [];
    //     foreach ($domainitems as $domainitem)
    //     {
    //       if ($domainitem->domain !== null)
    //       {
    //         $domains[] = $domainitem->domain->name;
    //       }
    //     }

    //     $network_name = '';
    //     if ($myItem->network !== null)
    //     {
    //       $network_name = $myItem->network->name;
    //     }

    //     $users = [];
    //     if ($myItem->user !== null)
    //     {
    //       if (isset($myItem->user->name))
    //       {
    //         $users[] = $this->genereUserName($myItem->user->name, $myItem->user->lastname, $myItem->user->firstname);
    //       } else {
    //         foreach ($myItem->user as $user)
    //         {
    //           $users[] = $this->genereUserName($user->name, $user->lastname, $user->firstname);
    //         }
    //       }
    //     }

    //     $groups = [];
    //     if ($myItem->group !== null)
    //     {
    //       if (isset($myItem->group->name))
    //       {
    //         $groups[] = $myItem->group->name;
    //       } else {
    //         foreach ($myItem->group as $group)
    //         {
    //           $groups[] = $group->name;
    //         }
    //       }
    //     }

    //     $ips = [];

    //     $macs = [];

    //     $itemsLink = [
    //       'id' => $externallink->links->id,
    //       'name' => $myItem->name,
    //       'serial' => $myItem->serial,
    //       'otherserial' => $myItem->otherserial,
    //       'location_id' => $location_id,
    //       'location' => $location_name,
    //       'domains' => $domains,
    //       'network' => $network_name,
    //       'comment' => $myItem->comment,
    //       'users' => $users,
    //       'groups' => $groups,
    //       // 'realname' => $realname,
    //       // 'firstname' => $firstname,
    //       // 'login' => $login,
    //       // 'ips' => $ips,
    //       // 'macs' => $macs,
    //     ];

    //     $generate = $name . ' : ' . $this->generateLinkContents($data, $itemsLink, true);
    //   }

    //   $myExternalLinks[] = [
    //     'name'          => $name,
    //     'open_window'   => $open_window,
    //     'link'          => $link,
    //     'generate'      => $generate,
    //   ];
    // }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('externallinks', $myExternalLinks);

    return $view->render($response, 'subitem/externallinks.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, mixed> $item
   */
  private function generateLinkContents(string $link, array $item, bool $replaceByBr = false): string
  {
    $new_link = $link;
    if ($replaceByBr === true)
    {
      $new_link = str_ireplace("\n", "<br>", $new_link);
    }
    $matches = [];
    if (preg_match_all('/\[FIELD:(\w+)\]/', $new_link, $matches))
    {
      foreach ($matches[1] as $key => $field)
      {
        $new_link = $this->checkAndReplaceProperty($item, $field, $matches[0][$key], $new_link, $replaceByBr);
      }
    }

    if (strstr($new_link, "[ID]"))
     {
      $new_link = $this->checkAndReplaceProperty($item, 'id', "[ID]", $new_link, $replaceByBr);
    }
    if (strstr($link, "[NAME]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'name', "[NAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[SERIAL]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'serial', "[SERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[OTHERSERIAL]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'otherserial', "[OTHERSERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATIONID]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'location_id', "[LOCATIONID]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATION]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'location', "[LOCATION]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[DOMAIN]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'domains', "[DOMAIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[NETWORK]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'network', "[NETWORK]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[REALNAME]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'realname', "[REALNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[FIRSTNAME]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'firstname', "[FIRSTNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOGIN]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'login', "[LOGIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[USER]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'users', "[USER]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[GROUP]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'groups', "[GROUP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[IP]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'ips', "[IP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[MAC]"))
    {
      $new_link = $this->checkAndReplaceProperty($item, 'macs', "[MAC]", $new_link, $replaceByBr);
    }

    return $new_link;
  }

  /**
   * @param array<string, mixed> $item
   */
  private function checkAndReplaceProperty(
    array $item,
    string $field,
    string $strToReplace,
    string $new_link,
    bool $replaceByBr = false
  ): string
  {
    $ret = $new_link;

    if (array_key_exists($field, $item))
    {
      if (is_array($item[$field]))
      {
        $tmp = '';
        foreach ($item[$field] as $val)
        {
          if ($tmp != '')
          {
            $tmp = $tmp  . "\n";
          }
          $tmp = $tmp . $val;
        }
        $ret = str_replace($strToReplace, $tmp, $ret);
      } else {
        $ret = str_replace($strToReplace, $item[$field], $ret);
      }
      if ($replaceByBr === true)
      {
        $ret = str_ireplace("\n", "<br>", $ret);
      }
    }

    return $ret;
  }
}
