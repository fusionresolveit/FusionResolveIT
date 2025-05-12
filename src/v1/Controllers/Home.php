<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Illuminate\Support\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Type\Integer;
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
    $session = new \SlimSession\Helper();
    if (isset($session['interface']) && $session['interface'] == 'helpdesk')
    {
      return $this->homepageTech($request, $response, $args);
    }
    return $this->homepageUser($request, $response, $args);
  }

  /**
   * @param array<string, string> $args
   */
  public function switchHomepage(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $session = new \SlimSession\Helper();
    if (
        $session->exists('interface') &&
        $session->get('interface') == 'helpdesk'
    )
    {
      $session->delete('interface');
    } else {
      $session->set('interface', 'helpdesk');
    }
    return $response
      ->withHeader('Location', $basePath . '/view/home')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function homepageTech(Request $request, Response $response, array $args): Response
  {
    global $translator, $basePath;

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
        $this->lastknowledgearticles($items, $nbLast);
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

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($home, $request);

    $viewData->addData('rootUrl', $rootUrl);

    $nb_paging_total = [];
    $nb_paging_total['paging']['total'] = $nbValTotal;
    $viewData->addData('items', $items);

    $myData = [
      // [
      //   'header' => [
      //     'title'     => 'Last escaladed tickets',
      //     'subtitle'  => '',
      //     'name'      => 'last-escaladed-tickets',
      //   ],
      //   'list' => [
      //     [
      //       'id'   => '254678',
      //       'name' => 'signature problem',
      //     ],
      //   ],
      //   'footer' => [
      //     'enabled' => true,
      //     'url' => '',
      //   ],
      //   'color' => 'blue',
      // ],
    ];

    $myData = [];
    [$cnt, $data] = $this->getNewTickets();
    $myData[] = [
      'header' => [
        'icon'      => 'book open',
        'title'     => $translator->translate('New tickets'),
        'subtitle'  => $cnt . ' tickets',
        'name'      => 'new-tickets',
      ],
      'list'   => $data,
      'footer' => [
        'enabled' => $cnt >= 5,
        'url'     => '',
      ],
      'color'  => 'olive',
      'url'    => $basePath . '/view/tickets',
    ];

    [$cnt, $data] = $this->getTicketsAssignedToMe();

    $myData[] = [
      'header' => [
        'icon'      => 'book reader',
        'title'     => 'Tickets assigned to me',
        'subtitle'  => $cnt . ' tickets',
        'name'      => 'my-assigned-tickets',
      ],
      'list'   => $data,
      'footer' => [
        'enabled' => $cnt >= 5,
        'url'     => '',
      ],
      'color'  => 'blue',
      'url'    => $basePath . '/view/tickets',
    ];

    [$cnt, $data] = $this->getMyTickets();

    $myData[] = [
      'header' => [
        'icon'      => 'user circle',
        'title'     => 'My tickets',
        'subtitle'  => $cnt . ' tickets',
        'name'      => 'my-tickets',
      ],
      'list'   => $data,
      'footer' => [
        'enabled' => $cnt >= 5,
        'url'     => '',
      ],
      'color'  => 'green',
      'url'    => $basePath . '/view/tickets',
    ];

    [$cnt, $data] = $this->getMyGroupTickets();

    $myData[] = [
      'header' => [
        'icon'      => 'user friends',
        'title'     => 'Tickets of my groups',
        'subtitle'  => $cnt . ' tickets',
        'name'      => 'my-groups-tickets',
      ],
      'list'   => $data,
      'footer' => [
        'enabled' => $cnt >= 5,
        'url'     => '',
      ],
      'color'  => 'blue',
      'url'    => $basePath . '/view/tickets',
    ];

    $cnt = $this->getNumberIncidentsToday();

    $myData[] = [
      'header' => [
        'title'     => 'Number of today incidents',
        'subtitle'  => '',
        'name'      => 'number-today-incidents',
      ],
      'stat'   => $cnt,
      'footer' => [
        'enabled' => false,
        'url'     => '',
      ],
      'color'  => 'blue',
      'url'    => $basePath . '/view/tickets',
    ];

    [$cnt, $data] = $this->getLastProblems();

    $myData[] = [
      'header' => [
        'icon'      => 'drafting compass',
        'title'     => 'Last problems',
        'subtitle'  => $cnt . ' problems',
        'name'      => 'last-problems',
      ],
      'list'   => $data,
      'footer' => [
        'enabled' => $cnt >= 5,
        'url'     => '',
      ],
      'color'  => 'blue',
      'url'    => $basePath . '/view/problems',
    ];

    [$cnt, $data] = $this->getLastChanges();

    $myData[] = [
      'header' => [
        'icon'      => 'paint roller',
        'title'     => 'Last changes',
        'subtitle'  => $cnt . ' changes',
        'name'      => 'last changes',
      ],
      'list'   => $data,
      'footer' => [
        'enabled' => $cnt >= 5,
        'url'     => '',
      ],
      'color'  => 'blue',
      'url'    => $basePath . '/view/changes',
    ];

    [$cnt, $data] = $this->getLastKnowledgebasearticles();

    $myData[] = [
      'header' => [
        'icon'      => 'edit',
        'title'     => 'Last knowledge base articles',
        'subtitle'  => $cnt . ' articles',
        'name'      => 'last-knowledge-articles',
      ],
      'list'   => $data,
      'footer' => [
        'enabled' => $cnt >= 5,
        'url'     => '',
      ],
      'color'  => 'blue',
      'url'    => $basePath . '/view/knowledgebasearticles',
    ];

    [$cnt, $data] = $this->getLinkedTickets();

    $myData[] = [
      'header' => [
        'title'     => 'Linked tickets',
        'subtitle'  => $cnt . ' articles',
        'name'      => 'linked-tickets',
      ],
      'list'   => $data,
      'footer' => [
        'enabled' => $cnt >= 5,
        'url'     => '',
      ],
      'color'  => 'blue',
      'url'    => $basePath . '/view/tickets',
    ];

    $viewData->addData('mytest', $myData);

    $viewData->addData('fields', $nb_paging_total);
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

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
      'last_knowledgebasearticles',
      $translator->translatePlural('Last knowledge base article', 'Last knowledge base articles', 2)
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
   * @param array<string, string> $args
   */
  public function homepageUser(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = (object) $request->getQueryParams();

    $view = Twig::fromRequest($request);

    $home = new \App\Models\Home();

    $messageTypes = \App\Models\Definitions\Alert::getTypeArray();

    $alerts = \App\Models\Alert::
        where('is_active', true)
      ->where('is_displayed_oncentral', true)
      ->get();

    $messages = [];
    foreach ($alerts as $alert)
    {
      $messages[] = [
        'type'    => $messageTypes[$alert->type],
        'name'    => $alert->name,
        'message' => \App\v1\Controllers\Toolbox::convertMarkdownToHtml($alert->message),
      ];
    }

    $cards = [];

    $categoriesId = [];

    // get forms
    $forms = \App\Models\Forms\Form::where('is_active', true)->get();

    foreach ($forms as $form)
    {
      if (
          is_null($form->category) ||
          (
            isset($data->category) &&
            is_numeric($data->category) &&
            $form->category->id == (int) $data->category
          )
      )
      {
        $cards[] = [
          'color'         => $form->icon_color,
          'icon'          => $form->icon,
          'title'         => $form->name,
          'description'   => $form->content,
          'button_title'  => 'Fill this form',
          'url'           => '',
        ];
      }
      elseif (!is_null($form->category->treepath))
      {
        if (isset($data->category) && is_numeric($data->category))
        {
          $next = false;
          foreach (str_split($form->category->treepath, 5) as $idx => $id)
          {
            if ($next)
            {
              $categoriesId = array_merge($categoriesId, [(int) $id]);
              $next = false;
            }
            if ((int) $id == (int) $data->category)
            {
              $next = true;
            }
          }
        } else {
          $categoriesId = array_merge($categoriesId, [str_split($form->category->treepath, 5)[0]]);
        }
      }
    }

    // get knowledgebasearticles
    $knowledgebasearticles = \App\Models\Knowledgebasearticle::get();

    foreach ($knowledgebasearticles as $article)
    {
      // Check if the current user can view it
      $authorized = false;
      // entity
      foreach ($article->entitiesview as $entity)
      {
        if ($entity->treepath == $GLOBALS['entity_treepath'])
        {
          $authorized = true;
        }
        if (
            $entity->getRelationValue('pivot')->is_recursive &&
            str_starts_with($GLOBALS['entity_treepath'], $entity->getAttribute('treepath'))
        )
        {
          $authorized = true;
          break;
        }
      }
      // group
      $user = \App\Models\User::where('id', $GLOBALS['user_id'])->first();
      if (!is_null($user))
      {
        foreach ($article->groupsview as $groupview)
        {
          foreach ($user->group as $group)
          {
            if ($groupview->treepath == $group->treepath)
            {
              $authorized = true;
            }
            if (
                $groupview->getRelationValue('pivot')->is_recursive &&
                str_starts_with($group->getAttribute('treepath'), $groupview->getAttribute('treepath'))
            )
            {
              $authorized = true;
              break 2;
            }
          }
        }
      }

      // profile
      foreach ($article->profilesview as $profile)
      {
        if ($profile->id == $GLOBALS['profile_id'])
        {
          $authorized = true;
        }
      }

      // user
      foreach ($article->usersview as $user)
      {
        if ($user->id == $GLOBALS['user_id'])
        {
          $authorized = true;
        }
      }

      if (!$authorized)
      {
        continue;
      }

      if (
          is_null($article->category) ||
          (
            isset($data->category) &&
            is_numeric($data->category) &&
            $article->category->id == (int) $data->category
          )
      )
      {
        $cards[] = [
          'color'         => 'olive',
          'icon'          => 'book',
          'title'         => $article->name,
          'description'   => '',
          'button_title'  => 'Read this article',
          'url'           => $basePath . '/view/knowledgebasearticles/read/' . $article->id,
        ];
      }
      elseif (!is_null($article->category->treepath))
      {
        if (isset($data->category) && is_numeric($data->category))
        {
          $next = false;
          foreach (str_split($article->category->treepath, 5) as $idx => $id)
          {
            if ($next)
            {
              $categoriesId = array_merge($categoriesId, [(int) $id]);
              $next = false;
            }
            if ((int) $id == (int) $data->category)
            {
              $next = true;
            }
          }
        } else {
          $categoriesId = array_merge($categoriesId, [str_split($article->category->treepath, 5)[0]]);
        }
      }
    }

    $ids = [];
    foreach ($categoriesId as $id)
    {
      $ids[] = (int) $id;
    }
    $categories = \App\Models\Category::whereIn('id', $ids)->get();
    foreach ($categories as $category)
    {
      $cards[] = [
        'color'         => 'teal',
        'icon'          => 'layer group',
        'title'         => $category->name,
        'description'   => $category->comment,
        'button_title'  => 'Go in this category',
        'url'           => $basePath . '/view/home?category=' . $category->id,
      ];
    }

    $breadcrumb = [];
    if (isset($data->category) && is_numeric($data->category))
    {
      $category = \App\Models\Category::where('id', (int) $data->category)->first();
      if (!is_null($category) && !is_null($category->treepath))
      {
        $categories = str_split($category->treepath, 5);
        foreach ($categories as $id)
        {
          $myCat = \App\Models\Category::where('id', (int) $id)->first();
          if (!is_null($myCat))
          {
            $breadcrumb[] = [
              'id'    => $myCat->id,
              'name'  => $myCat->name,
            ];
          }
        }
      }
    }

    // Check if the profile can be helpdesk (for tech)
    $canSwitchToTech = false;
    $profile = \App\Models\Profile::where('id', $GLOBALS['profile_id'])->first();
    if (!is_null($profile) && $profile->interface == 'helpdesk')
    {
      $canSwitchToTech = true;
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($home, $request);

    $viewData->addData('messages', $messages);
    $viewData->addData('cards', $cards);
    $viewData->addData('breadcrumb', $breadcrumb);
    $viewData->addData('canswitchtotech', $canSwitchToTech);
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    return $view->render($response, 'homeuser.html.twig', (array)$viewData);
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
  private function lastknowledgearticles(&$items, int $nbLast): void
  {
    $item2 = new \App\Models\Knowledgebasearticle();
    $myItem2 = $item2->orderBy('created_at', 'desc')->take($nbLast)->take(5)->get();
    $myItem3 = $item2->orderBy('created_at', 'desc')->count();

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

      $items['lastknowledgearticles']['datas'][$it->id] = [
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
    $items['lastknowledgearticles']['datas']['#'] = [
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

  /**
   * @return array<int, mixed>
   */
  private function getNewTickets(): array
  {
    $query = \App\Models\Ticket::where('status', 1);
    $tickets = $query->take(5)->get();
    $status = \App\Models\Definitions\Ticket::getStatusArray();
    $data = [];
    foreach ($tickets as $ticket)
    {
      $data[] = [
        'id'      => $ticket->id,
        'name'    => $ticket->name,
        'status'  => '<i class="' . $status[$ticket->status]['color'] . ' ' . $status[$ticket->status]['icon'] .
          ' icon"></i>',
      ];
    }
    return [$query->count(), $data];
  }

  /**
   * @return array<int, mixed>
   */
  private function getTicketsAssignedToMe()
  {
    $query = \App\Models\Ticket::whereRelation('technician', 'users.id', $GLOBALS['user_id']);
    $tickets = $query->take(5)->get();
    $status = \App\Models\Definitions\Ticket::getStatusArray();
    $data = [];
    foreach ($tickets as $ticket)
    {
      $data[] = [
        'id'      => $ticket->id,
        'name'    => $ticket->name,
        'status'  => '<i class="' . $status[$ticket->status]['color'] . ' ' . $status[$ticket->status]['icon'] .
          ' icon"></i>',
      ];
    }
    return [$query->count(), $data];
  }

  /**
   * @return array<int, mixed>
   */
  private function getMyTickets()
  {
    $query = \App\Models\Ticket::whereRelation('requester', 'users.id', $GLOBALS['user_id']);
    $tickets = $query->take(5)->get();
    $status = \App\Models\Definitions\Ticket::getStatusArray();
    $data = [];
    foreach ($tickets as $ticket)
    {
      $data[] = [
        'id'      => $ticket->id,
        'name'    => $ticket->name,
        'status'  => '<i class="' . $status[$ticket->status]['color'] . ' ' . $status[$ticket->status]['icon'] .
          ' icon"></i>',
      ];
    }
    // TODO add priority?
    return [$query->count(), $data];
  }

  /**
   * @return array<int, mixed>
   */
  private function getMyGroupTickets()
  {
    $groupIds = [];
    $user = \App\Models\User::where('id', $GLOBALS['user_id'])->first();
    if (is_null($user))
    {
      return [0, []];
    }
    foreach ($user->group as $group)
    {
      $groupIds[] = $group->id;
    }

    $query = \App\Models\Ticket::whereRelation('requestergroup', 'groups.id', $groupIds);
    $tickets = $query->take(5)->get();
    $status = \App\Models\Definitions\Ticket::getStatusArray();
    $data = [];
    foreach ($tickets as $ticket)
    {
      $data[] = [
        'id'      => $ticket->id,
        'name'    => $ticket->name,
        'status'  => '<i class="' . $status[$ticket->status]['color'] . ' ' . $status[$ticket->status]['icon'] .
          ' icon"></i>',
      ];
    }
    return [$query->count(), $data];
  }

  private function getNumberIncidentsToday(): int
  {
    $query = \App\Models\Ticket::whereDate('created_at', Carbon::today());
    return $query->count();
  }

  /**
   * @return array<int, mixed>
   */
  private function getLastProblems()
  {
    $query = \App\Models\Problem::where('status', 1);
    $problems = $query->take(5)->get();
    $status = \App\Models\Definitions\Problem::getStatusArray();
    $data = [];
    foreach ($problems as $problem)
    {
      $data[] = [
        'id'      => $problem->id,
        'name'    => $problem->name,
        'status'  => '<i class="' . $status[$problem->status]['color'] . ' ' . $status[$problem->status]['icon'] .
          ' icon"></i>',
      ];
    }
    return [$query->count(), $data];
  }

  /**
   * @return array<int, mixed>
   */
  private function getLastChanges()
  {
    $query = \App\Models\Change::where('status', 1);
    $changes = $query->take(5)->get();
    $status = \App\Models\Definitions\Change::getStatusArray();
    $data = [];
    foreach ($changes as $change)
    {
      $data[] = [
        'id'      => $change->id,
        'name'    => $change->name,
        'status'  => '<i class="' . $status[$change->status]['color'] . ' ' . $status[$change->status]['icon'] .
          ' icon"></i>',
      ];
    }
    return [$query->count(), $data];
  }

  /**
   * @return array<int, mixed>
   */
  private function getLastKnowledgebasearticles()
  {
    $query = new \App\Models\Knowledgebasearticle();
    $knowledgebasearticles = $query->take(5)->get();
    $data = [];
    foreach ($knowledgebasearticles as $item)
    {
      $data[] = [
        'id'      => $item->id,
        'name'    => $item->name,
        'status'  => '',
      ];
    }
    return [$query->count(), $data];
  }

  /**
   * @return array<int, mixed>
   */
  private function getLinkedTickets()
  {
    $query = \App\Models\Ticket::has('linkedtickets')->where('status', '<', 5);
    $tickets = $query->take(5)->get();

    $data = [];
    foreach ($tickets as $ticket)
    {
      $links = [
        self::LINK => 0,
        self::DUPLICATE => 0,
        self::SON => 0,
        self::PARENT => 0,
      ];
      foreach ($ticket->linkedtickets as $linkedticket)
      {
        $links[$linkedticket->getRelationValue('pivot')->link]++;
        $ticketFound[] = $linkedticket->id;
      }

      $data[] = [
        'id'      => $ticket->id,
        'name'    => $ticket->name,
        'status'  => implode(', ', self::showLinkedTickets($links)),
      ];
    }
    return [$query->count(), $data];
  }
}
