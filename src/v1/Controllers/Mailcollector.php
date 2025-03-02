<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostMailcollector;
use App\DataInterface\PostTicket;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Webklex\PHPIMAP\ClientManager;

final class Mailcollector extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Mailcollector::class;

  protected function instanciateModel(): \App\Models\Mailcollector
  {
    return new \App\Models\Mailcollector();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostMailcollector((object) $request->getParsedBody());

    $mailcollector = new \App\Models\Mailcollector();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($mailcollector))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $mailcollector = \App\Models\Mailcollector::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The mail collector has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($mailcollector, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/mailcollectors/' . $mailcollector->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/mailcollectors')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostMailcollector((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $mailcollector = \App\Models\Mailcollector::where('id', $id)->first();
    if (is_null($mailcollector))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($mailcollector))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $mailcollector->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The mail collector has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($mailcollector, 'update');

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $id = intval($args['id']);
    $mailcollector = \App\Models\Mailcollector::withTrashed()->where('id', $id)->first();
    if (is_null($mailcollector))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($mailcollector->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $mailcollector->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The mail collector has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/mailcollectors')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $mailcollector->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The mail collector has been soft deleted successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function restoreItem(Request $request, Response $response, array $args): Response
  {
    $id = intval($args['id']);
    $mailcollector = \App\Models\Mailcollector::withTrashed()->where('id', $id)->first();
    if (is_null($mailcollector))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($mailcollector->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $mailcollector->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The mail collector has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  public function collect(\App\Models\Mailcollector $collector): int
  {
    $createdTickets = 0;

    $cm = new ClientManager($options = []);
    $client = $cm->account('account_identifier');
    $client = $cm->make([
      'host'            => 'outlook.office365.com',
      'port'            => 993,
      'encryption'      => 'ssl',
      'validate_cert'   => true,
      'username'        => 'd.durieux@dcsit-group.com',
      // 'password'      => 'password',
      'password'        => $collector->oauth_token,
      'authentication'  => "oauth",
      'protocol'        => 'imap'
    ]);

    //Connect to the IMAP Server
    $retry = false;
    try
    {
      $client->connect();
    }
    catch (\Exception $e)
    {
      $retry = true;
    }

    if ($retry)
    {
      $this->refreshToken($collector);

      $collector->refresh();

      $client = $cm->make([
        'host'            => 'outlook.office365.com',
        'port'            => 993,
        'encryption'      => 'ssl',
        'validate_cert'   => true,
        'username'        => 'd.durieux@dcsit-group.com',
        // 'password'      => 'password',
        'password'        => $collector->oauth_token,
        'authentication'  => "oauth",
        'protocol'        => 'imap'
      ]);
      $client->connect();
    }
    // $status = $client->isConnected();


    // Code for folders
    // $folders = $client->getFolders(false);
    // foreach($folders as $folder)
    // {
    //   echo 'folder: ' . $folder->path . ' | ' . $folder->name;
    //   echo "\n";
    // }


    $folder = $client->getFolderByPath('Tests Fusion Resolve IT'); // INBOX');
    if (is_null($folder))
    {
      return $createdTickets;
    }
    $messages = $folder->messages()->unseen()->setFetchOrder('desc')->limit($limit = 10, $page = 0)->get();
    //                             ->all()->count();


    /** @var \Webklex\PHPIMAP\Message $message */
    foreach($messages as $message)
    {
      // echo 'Attachments: ' . $message->getAttachments()->count() . '<br />';

      //Move the current Message to 'INBOX.read'
      // if($message->move('INBOX.read') == true)
      // {
      //   echo 'Message has ben moved';
      // }
      // else
      // {
      //   echo 'Message could not be moved';
      // }

      // TODO: Create tickets

      $requesters = [];

      $from = $message->get("from");
      $values = $from->toArray();

      foreach ($values as $sender)
      {
        $user = \App\Models\User::where('name', $sender->mail)->first();
        if (!is_null($user))
        {
          $requesters[] = $user->id;
        }
      }

      // get priority => urgency
      $urgency = 3;
      $prio = $message->get("priority");
      $values = $prio->toArray();
      foreach ($values as $priority)
      {
        switch ($priority)
        {
          case '1':
            $urgency = 5;
              break;

          case '2':
            $urgency = 4;
              break;

          case '4':
            $urgency = 2;
              break;

          case '5':
            $urgency = 1;
              break;
        }
      }

      $tData = (object) [
        'name'        => $message->getSubject(),
        'content'     => \App\v1\Controllers\Toolbox::convertHtmlToMarkdown($message->getHTMLBody()),
        'requester'   => implode(',', $requesters),
        'urgency'     => $urgency,
      ];

      $data = new PostTicket((object) $tData);

      $t = new \App\v1\Controllers\Ticket();
      $data = $t->prepareDataSave($data);

      $dataCreate = $data->exportToArray();
      $ticket = \App\Models\Ticket::create($dataCreate);

      $t->updateRelationshipsMany($dataCreate, 'requester', $ticket, 1);
      $t->updateRelationshipsMany($dataCreate, 'requestergroup', $ticket, 1);
      $t->updateRelationshipsMany($dataCreate, 'watcher', $ticket, 3);
      $t->updateRelationshipsMany($dataCreate, 'watchergroup', $ticket, 3);
      $t->updateRelationshipsMany($dataCreate, 'technician', $ticket, 2);
      $t->updateRelationshipsMany($dataCreate, 'techniciangroup', $ticket, 2);

      $createdTickets++;
      $message->setFlag('Seen');
    }
    return $createdTickets;
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    global $translator, $basePath;

    if (get_class($item) !== 'App\Models\Mailcollector')
    {
      return [];
    }

    $uri = $request->getUri();

    $information = [];
    if ($item->is_oauth)
    {
      $information[] = [
        'key'   => 'callbackurl',
        'value' => $translator->translate('Redirect URL') . ' ' . $uri->getScheme() . '://' . $uri->getHost() .
                   $basePath . '/view/mailcollectors/' . $item->id . '/oauth/cb',
        'link'  => null,
      ];
      $information[] = [
        'key'   => 'loginoauth',
        'value' => $translator->translate('Authenticate with oauth'),
        'link'  => $basePath . '/view/mailcollectors/' . $item->id . '/oauth',
      ];

      if ($item->oauth_provider == 'azure')
      {
        $information[] = [
          'key'   => 'documentation',
          'value' => $translator->translate('Provider documentation'),
          'link'  => 'https://learn.microsoft.com/en-us/exchange/client-developer/legacy-protocols/' .
                     'how-to-authenticate-an-imap-pop-smtp-application-by-using-oauth',
        ];
      }
      elseif ($item->oauth_provider == 'google')
      {
        $information[] = [
          'key'   => 'documentation',
          'value' => $translator->translate('Provider documentation'),
          'link'  => 'https://developers.google.com/gmail/imap/xoauth2-protocol',
        ];
      }
    }

    return $information;
    // return [
    //   [
    //     'key'   => 'callbackurl',
    //     'value' => $translator->translate('Redirect URL') . ' ' . $uri->getScheme() . '://' . $uri->getHost() .
    //                $basePath . '/view/mailcollectors/' . $item->id . '/oauth/cb',
    //     'link'  => null,
    //   ],
    //   [
    //     'key'   => 'loginoauth',
    //     'value' => $translator->translate('Go login with oauth'),
    //     'link'  => $basePath . '/view/mailcollectors/' . $item->id . '/oauth',
    //   ],
    // ];
  }

  /**
   * @param array<string, string> $args
   */
  public function doOauth(Request $request, Response $response, array $args): Response
  {
    $provider = $this->getMyProvider($request, $args);

    return $response
      ->withHeader('Location', $provider->makeAuthUrl());
  }

  /**
   * @param array<string, string> $args
   */
  public function getMyProvider(Request $request, array $args): \SocialConnect\Provider\AbstractBaseProvider
  {
    global $basePath;

    $collector = \App\Models\Mailcollector::where('id', $args['id'])->first();
    if (is_null($collector))
    {
      throw new \Exception('Id not found', 404);
    }

    $dataProvider = [
      'title'             => 'Azure AD',
      'applicationId'     => $collector->oauth_applicationid,
      'directoryId'       => $collector->oauth_directoryid,
      'applicationSecret' => $collector->oauth_applicationsecret,
      'scope'             => [
        'openid',
        'https://outlook.office365.com/IMAP.AccessAsUser.All',
        // 'https://outlook.office.com/POP.AccessAsUser.All',
        // 'https://outlook.office.com/SMTP.Send',
        'offline_access',
      ],
    ];
    $uri = $request->getUri();

    $configureProviders = [
      'redirectUri' => $uri->getScheme() . '://' . $uri->getHost() . $basePath . '/view/mailcollectors/' .
      $collector->id . '/oauth/cb',
      'provider' => [
        'azure-ad' => $dataProvider,
      ],
    ];
    return \App\v1\Controllers\Authsso::getProviderInstance('azure-ad', $configureProviders);
  }

  /**
   * @param array<string, string> $args
   */
  public function callbackOauth(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $provider = $this->getMyProvider($request, $args);
    $accessToken = $provider->getAccessTokenByRequestParameters($_GET);
    $token = $accessToken->getToken();
    // $refreshtoken = $accessToken->getRefreshToken();
    $refreshtoken = null;
    if (!is_null($token)) // && !is_null($refreshtoken))
    {
      $mailcollector = \App\Models\Mailcollector::where('id', $args['id'])->first();
      if (is_null($mailcollector))
      {
        throw new \Exception('Id not found', 404);
      }
      $mailcollector->oauth_token = $token;
//      $mailcollector->oauth_refresh_token = $refreshtoken;
      $mailcollector->save();

      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage('Authentication done with success');

      $uri = $request->getUri();

      return $response
        ->withHeader('Location', $uri->getScheme() . '://' . $uri->getHost() . $basePath . '/view/mailcollectors/' .
          $mailcollector->id);
    }
    echo "Error :/";
    exit;
  }

  public function refreshToken(\App\Models\Mailcollector $collector): void
  {
    $parameters = [
      'refresh_token' => $collector->oauth_refresh_token,
      'client_id'     => $collector->oauth_applicationid,
      'client_secret' => $collector->oauth_applicationsecret,
      'grant_type'    => 'refresh_token',
    ];

    $streamFactory = new \SocialConnect\HttpClient\StreamFactory();

    $request = new \SocialConnect\HttpClient\Request(
      'POST',
      'https://login.microsoftonline.com/' . $collector->oauth_directoryid . '/oauth2/v2.0/token'
    );
    $request = $request->withBody(
      $streamFactory->createStream(http_build_query($parameters, '', '&'))
    );

    $client = new \SocialConnect\HttpClient\Curl();
    $response = $client->sendRequest($request);
    $content = $response->getBody()->getContents();
    $result = json_decode($content, true);
    $collector->oauth_token = $result['access_token'];
    $collector->oauth_refresh_token = $result['refresh_token'];
    $collector->save();
  }

  /**
   * Run the scheduled collect mails
   */
  public static function scheduleCollects(): bool
  {
    $crontask = \App\Models\Crontask::where('name', 'mailgate')->first();
    if (is_null($crontask))
    {
      return false;
    }

    $crontaskexecution = new \App\v1\Controllers\Crontaskexecution();
    $executionId = $crontaskexecution->createExecution($crontask);

    $collectors = \App\Models\Mailcollector::where('is_active', true)->get();
    $mailcollector = new \App\v1\Controllers\Mailcollector();
    foreach ($collectors as $collector)
    {
      $nbTickets = $mailcollector->collect($collector);

      $executionlog = new \App\Models\Crontaskexecutionlog();
      $executionlog->crontaskexecution_id = $executionId;
      $executionlog->volume = $nbTickets;
      $executionlog->content = $collector->name . ' (' . $collector->id . ')';
      $executionlog->save();
    }

    $crontaskexecution->endExecution($executionId);

    return true;
  }
}
