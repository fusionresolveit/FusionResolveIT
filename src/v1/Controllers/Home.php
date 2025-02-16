<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Home extends Common
{
  public const LINK = 1;
  public const DUPLICATE = 2;
  public const SON = 3;
  public const PARENT = 4;

  /**
   * @param array<string, string> $args
   */
  public function homepage(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $nbLast = 8;

    $nbValTotal = 0;

    $view = Twig::fromRequest($request);

    $rootUrl = $this->genereRootUrl($request, '/home');

    $home = new \App\Models\Home();

    $items = [];
    $itemByUserid = $home->where('user_id', $GLOBALS['user_id'])->orderBy('row', 'asc')->get();
    if (count($itemByUserid) > 0)
    {
      foreach ($itemByUserid as $item)
      {
        $items[$item->module] = [
          'column'  => $item->column,
          'row'     => $item->row,
          'datas'   => [],
        ];
      }
    }
    else
    {
      $itemByProfileid = $home->where('profile_id', $GLOBALS['profile_id'])->orderBy('row', 'asc')->get();
      if (count($itemByProfileid) > 0)
      {
        foreach ($itemByProfileid as $item)
        {
          $items[$item->module] = [
            'column'  => $item->column,
            'row'     => $item->row,
            'datas'   => [],
          ];
        }
      }
      else
      {
        $itemByDefaultUseridProfileid = $home
          ->where(['user_id' => 0, 'profile_id' => 0])
          ->orderBy('row', 'asc')
          ->get();
        if (count($itemByDefaultUseridProfileid) > 0)
        {
          foreach ($itemByDefaultUseridProfileid as $item)
          {
            $items[$item->module] = [
              'column'  => $item->column,
              'row'     => $item->row,
              'datas'   => [],
            ];
          }
        }
      }
    }

    foreach (array_keys($items) as $key)
    {
      if ($key == 'mytickets')
      {
        $this->myTickets($items);
      }
      if ($key == 'groupstickets')
      {
        $this->groupstickets($items);
      }
      if ($key == 'lastknowledgeitems')
      {
        $this->lastknowledgeitems($items, $nbLast);
      }
      if ($key == 'lastproblems')
      {
        $this->lastproblems($items, $nbLast);
      }
      if ($key == 'todayincidents')
      {
        $this->todayincidents($items);
      }
      // if ($key == 'linkedincidents')
      // {
      //   $myItem2 = \App\Models\Ticket::has('linkedtickets')->get();
      //   $ticketFound = [];

      //   foreach ($myItem2 as $ticket)
      //   {
      //     if (!in_array($ticket->id, $ticketFound))
      //     {
      //       $ticketFound[] = $ticket->id;


      //       $links = [
      //         self::LINK => 0,
      //         self::DUPLICATE => 0,
      //         self::SON => 0,
      //         self::PARENT => 0,
      //       ];
      //       foreach ($ticket->linkedtickets as $linkedticket)
      //       {
      //         $links[$linkedticket->getRelationValue('pivot')->link]++;
      //         $ticketFound[] = $linkedticket->id;
      //       }

      // TODO commented because heavy query on base with 450 000 tickets
      //       $items[$key]['datas'][$ticket->id] = [
      //         'name' => $ticket->name,
      //         'status' => self::getStatusArray()[$ticket->status],
      //         'priority' => self::getPriorityArray()[$ticket->priority],
      //         'date_open' => $ticket->date,
      //         'date_last_modif' => $ticket->updated_at,
      //         'linkedtickets' => self::showLinkedTickets($links),
      //       ];
      //     }
      //   }

      //   $nbValTotal = $nbValTotal + count($items[$key]['datas']);
      // }
      if ($key == 'lastescaladedtickets')
      {
        $this->lastescaladedtickets($items, $nbLast);
      }
      if ($key == 'knowledgelink')
      {
        $link = '<i class="question icon"></i>';
        $link = '<a href="' . $rootUrl . '/knowledgebase">' . $link . '</a>';

        $items[$key]['datas']['link'] = $link;
        $nbValTotal = $nbValTotal + count($items[$key]['datas']);
      }
      if ($key == 'forms')
      {
        $this->forms($items);
      }
      if ($key == 'incidentsfromcategory')
      {
      }
    }
    // echo "<pre>";
    // print_r($myItem2);
    // echo "</pre>";
    // die();


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($home, $request);

    // table homes
    // vérifier si il y a des entrées pour l'utilisateur ($GLOBALS['user_id'])
    // si non, vérifier si il y a une entrée pour son profile ($GLOBALS['profile_id'])
    // si non, prendre les entrées avec user_id et profile_id = 0

    // L'affichage se fait sur plusieurs colonnes, on va en mettre 3 par défaut,
    // mais si un utilisateur a un grand écran, il peut en mettre ce qu'il veut (limit de 10 quand même)

    // le but est d'avoir une affichage par défaut.
    // Possibilité d'en créer un pour un profile
    // chaque utilisateur pourra créer sa home page si il veut.

    // Modules :

    // incidentsfromcategory : (voir avec Marie-Noëlle ce qu'elle veut dire) Ou le nombre
    // d'incidents résultants d'un catégories ou d'un tag PBG

    // pour les last, peut être afficher les 8 derniers, pas trop sûr de ça encore

    $viewData->addData('rootUrl', $rootUrl);

    $nb_paging_total = [];
    $nb_paging_total['paging']['total'] = $nbValTotal;
    $viewData->addData('items', $items);
    $viewData->addData('fields', $nb_paging_total);

    $viewData->addTranslation('id', $translator->translate('ID'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('date_open', $translator->translate('Opening date'));
    $viewData->addTranslation('date_last_modif', $translator->translate('Last update'));
    $viewData->addTranslation('mytickets', $translator->translatePlural('My ticket', 'My tickets', 2));
    $viewData->addTranslation(
      'groupstickets',
      $translator->translatePlural('Ticket of my group', 'Tickets of my groups', 2)
    );
    $viewData->addTranslation('nb_today_incidents', $translator->translate('Number of today incidents'));
    $viewData->addTranslation('last_problems', $translator->translatePlural('Last Problem', 'Last Problems', 2));
    $viewData->addTranslation('subject', $translator->translate('Subject'));
    $viewData->addTranslation('writer', $translator->translate('Writer'));
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('visible_since', $translator->translate('Visible since'));
    $viewData->addTranslation('visible_until', $translator->translate('Visible until'));
    $viewData->addTranslation(
      'last_knowbaseitems',
      $translator->translatePlural('Last knowbase item', 'Last knowbase items', 2)
    );
    $viewData->addTranslation('linkedtickets', $translator->translatePlural('Linked ticket', 'Linked tickets', 2));
    $viewData->addTranslation(
      'lastescaladedtickets',
      $translator->translatePlural('Last escaladed ticket', 'Last escaladed tickets', 2)
    );
    $viewData->addTranslation('knowledgebase', $translator->translate('Knowledge base'));
    $viewData->addTranslation('forms', $translator->translatePlural('Form', 'Forms', 2));

    return $view->render($response, 'home.html.twig', (array)$viewData);
  }

  /**
   * @param array<int, int> $links
   *
   * @return array<int, int|string>
   */
  public static function showLinkedTickets(array $links): array
  {
    $tab = [];

    foreach ($links as $type_link => $nb_link)
    {
      if ($nb_link > 0)
      {
        if ($type_link == self::LINK)
        {
          $nb_link = 'Lié à ' . $nb_link . ' ticket(s)';
        }
        if ($type_link == self::DUPLICATE)
        {
          $nb_link = 'Duplique ' . $nb_link . ' ticket(s)';
        }
        if ($type_link == self::SON)
        {
          $nb_link = 'Enfant de ' . $nb_link . ' ticket(s)';
        }
        if ($type_link == self::PARENT)
        {
          $nb_link = 'Parent de ' . $nb_link . ' ticket(s)';
        }
        $tab[$type_link] = $nb_link;
      }
    }
    return $tab;
  }

  /**
   * @param array<mixed> &$items
   */
  private function myTickets(&$items): void
  {
    $tickets = \App\Models\Ticket::with('requester')->take(5)->get();

    foreach ($tickets as $it)
    {
      if ($it->requester !== null)
      {
        foreach ($it->requester as $req)
        {
          if ($req->getRelationValue('pivot')->user_id == $GLOBALS['user_id'])
          {
            $items['mytickets']['datas'][$it->id] = [
              'name'              => $it->name,
              'status'            => $this->getStatusArray()[$it->status],
              'priority'          => $this->getPriorityArray()[$it->priority],
              'date_open'         => $it->created_at,
              'date_last_modif'   => $it->updated_at,
            ];
          }
        }
      }
    }
  }

  /**
   * @param array<mixed> &$items
   */
  private function groupstickets(&$items): void
  {
    $itemUser = \App\Models\User::with('group')->where('id', $GLOBALS['user_id'])->first();
    if (is_null($itemUser))
    {
      return;
    }

    $groups = [];
    foreach ($itemUser->group as $grp)
    {
      $groups[$grp->id] = $grp->name;
    }

    if (count($groups) > 0)
    {
      $tickets = \App\Models\Ticket::with('requestergroup', 'watchergroup', 'techniciangroup')->take(5)->get();
      foreach ($tickets as $it)
      {
        if ($it->requestergroup !== null)
        {
          foreach ($it->requestergroup as $req)
          {
            if (array_key_exists($req->getRelationValue('pivot')->group_id, $groups))
            {
              $items['groupstickets']['datas'][$it->id] = [
                'name'              => $it->name,
                'status'            => $this->getStatusArray()[$it->status],
                'priority'          => $this->getPriorityArray()[$it->priority],
                'date_open'         => $it->created_at,
                'date_last_modif'   => $it->updated_at,
              ];
            }
          }
        }
        if ($it->watchergroup !== null)
        {
          foreach ($it->watchergroup as $req)
          {
            if (array_key_exists($req->getRelationValue('pivot')->group_id, $groups))
            {
              $items['groupstickets']['datas'][$it->id] = [
                'name'              => $it->name,
                'status'            => $this->getStatusArray()[$it->status],
                'priority'          => $this->getPriorityArray()[$it->priority],
                'date_open'         => $it->created_at,
                'date_last_modif'   => $it->updated_at,
              ];
            }
          }
        }
        if ($it->techniciangroup !== null)
        {
          foreach ($it->techniciangroup as $req)
          {
            if (array_key_exists($req->getRelationValue('pivot')->group_id, $groups))
            {
              $items['groupstickets']['datas'][$it->id] = [
                'name'              => $it->name,
                'status'            => $this->getStatusArray()[$it->status],
                'priority'          => $this->getPriorityArray()[$it->priority],
                'date_open'         => $it->created_at,
                'date_last_modif'   => $it->updated_at,
              ];
            }
          }
        }
      }
    }
  }

  /**
   * @param array<mixed> &$items
   */
  private function lastknowledgeitems(&$items, int $nbLast): void
  {
    $item2 = new \App\Models\Knowbaseitem();
    $myItem2 = $item2->orderBy('date', 'desc')->take($nbLast)->take(5)->get();
    $myItem3 = $item2->orderBy('date', 'desc')->count();

    foreach ($myItem2 as $it)
    {
      $user = '';
      if ($it->user !== null)
      {
        $user = $it->user->name;
      }
      $category = '';
      if ($it->category !== null)
      {
        $category = $it->category->name;
      }

      $items['lastknowledgeitems']['datas'][$it->id] = [
        'name'            => $it->name,
        'user'            => $user,
        'category'        => $category,
        'visible_since'   => $it->begin_date,
        'visible_until'   => $it->end_date,
      ];
    }
    $limit = false;
    if ($myItem3 > $nbLast)
    {
      $limit = true;
    }
    $items['lastknowledgeitems']['datas']['#'] = [
      'limit'   => $limit,
      'nbLast'  => $nbLast,
      'nb_res'  => $myItem3,
    ];
  }

  /**
   * @param array<mixed> &$items
   */
  private function lastproblems(&$items, int $nbLast): void
  {
    $item2 = new \App\Models\Problem();
    $myItem2 = $item2->orderBy('date', 'desc')->take($nbLast)->get();
    $myItem3 = $item2->orderBy('date', 'desc')->count();

    foreach ($myItem2 as $it)
    {
      $items['lastproblems']['datas'][$it->id] = [
        'name'              => $it->name,
        'status'            => $this->getStatusArray()[$it->status],
        'priority'          => $this->getPriorityArray()[$it->priority],
        'date_open'         => $it->created_at,
        'date_last_modif'   => $it->updated_at,
      ];
    }
    $limit = false;
    if ($myItem3 > $nbLast)
    {
      $limit = true;
    }
    $items['lastproblems']['datas']['#'] = [
      'limit'   => $limit,
      'nbLast'  => $nbLast,
      'nb_res'  => $myItem3,
    ];
  }

  /**
   * @param array<mixed> &$items
   */
  private function todayincidents(&$items): void
  {
    $user = \App\Models\User::with('group')->where('id', $GLOBALS['user_id'])->first();
    if (is_null($user))
    {
      return;
    }

    $groups = [];
    foreach ($user->group as $grp)
    {
      $groups[$grp->id] = $grp->name;
    }

    if (count($groups) > 0)
    {
      $nbTodayIncidents = 0;

      $tickets = \App\Models\Ticket::with('requester', 'requestergroup')->take(5)->get();
      foreach ($tickets as $it)
      {
        if ($it->requestergroup !== null)
        {
          foreach ($it->requestergroup as $req)
          {
            if (array_key_exists($req->getRelationValue('pivot')->group_id, $groups))
            {
              if (!is_null($it->created_at) && $it->created_at->format('Y-m-d') == date('Y-m-d'))
              {
                $nbTodayIncidents = $nbTodayIncidents + 1;
              }
            }
          }
        }
        if ($it->requester !== null)
        {
          foreach ($it->requester as $req)
          {
            if ($req->getRelationValue('pivot')->user_id == $GLOBALS['user_id'])
            {
              if (!is_null($it->created_at) && $it->created_at->format('Y-m-d') == date('Y-m-d'))
              {
                $nbTodayIncidents = $nbTodayIncidents + 1;
              }
            }
            else
            {
              $user2 = new \App\Models\User();
              $itemUser2 = $user2::with('group')->where('id', $req->getRelationValue('pivot')->user_id)->first();
              if (is_null($itemUser2))
              {
                return;
              }
              $groups2 = [];
              foreach ($itemUser2->group as $grp2)
              {
                $groups2[$grp2->id] = $grp2->name;
              }

              foreach (array_keys($groups2) as $key2)
              {
                if (array_key_exists($key2, $groups))
                {
                  if (!is_null($it->created_at) && $it->created_at->format('Y-m-d') == date('Y-m-d'))
                  {
                    $nbTodayIncidents = $nbTodayIncidents + 1;
                  }
                }
              }
            }
          }
        }
      }
      $items['todayincidents']['datas']['nb'] = $nbTodayIncidents;
    }
  }

  /**
   * @param array<mixed> &$items
   */
  private function lastescaladedtickets(&$items, int $nbLast): void
  {
    $tickets = \App\Models\Ticket::
        with('requester', 'technician', 'techniciangroup')
      ->orderBy('created_at', 'desc')
      ->take(5)
      ->get();

    $incr = 0;
    foreach ($tickets as $it)
    {
      if ($it->requester !== null)
      {
        foreach ($it->requester as $req)
        {
          if ($req->getRelationValue('pivot')->user_id == $GLOBALS['user_id'])
          {
            $others_tech = false;
            if ($it->technician !== null)
            {
              foreach ($it->technician as $tec)
              {
                if ($tec->getRelationValue('pivot')->user_id != $GLOBALS['user_id'])
                {
                  $others_tech = true;
                  break;
                }
              }
            }
            if ($others_tech === false)
            {
              if ($it->techniciangroup !== null)
              {
                foreach ($it->techniciangroup as $tec)
                {
                  $others_tech = true;
                  break;
                }
              }
            }

            if ($others_tech)
            {
              $incr = $incr + 1;
              if ($incr <= $nbLast)
              {
                $items['lastescaladedtickets']['datas'][$it->id] = [
                  'name'              => $it->name,
                  'status'            => $this->getStatusArray()[$it->status],
                  'priority'          => $this->getPriorityArray()[$it->priority],
                  'date_open'         => $it->created_at,
                  'date_last_modif'   => $it->updated_at,
                ];
              }
            }
          }
        }
      }
    }

    $limit = false;
    if ($incr > $nbLast)
    {
      $limit = true;
    }
    $items['lastescaladedtickets']['datas']['#'] = [
      'limit'   => $limit,
      'nbLast'  => $nbLast,
      'nb_res'  => $incr,
    ];
  }

  /**
   * @param array<mixed> &$items
   */
  private function forms(&$items): void
  {
    $forms = \App\Models\Forms\Form::has('category')->get();

    foreach ($forms as $form)
    {
      if (!is_null($form->category))
      {
        $items['forms']['datas'][$form->category->id]['category'] = $form->category->name;
        $items['forms']['datas'][$form->category->id]['forms'][$form->id] = [
          'name' => $form->name,
        ];
      }
    }
  }
}
