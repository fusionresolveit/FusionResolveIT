<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostNotification;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Notification extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Notification::class;

  protected function instanciateModel(): \App\Models\Notification
  {
    return new \App\Models\Notification();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostNotification((object) $request->getParsedBody());

    $notification = new \App\Models\Notification();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($notification))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $notification = \App\Models\Notification::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The notification has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($notification, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/notifications/' . $notification->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/notifications')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostNotification((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $notification = \App\Models\Notification::where('id', $id)->first();
    if (is_null($notification))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($notification))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $notification->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The notification has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($notification, 'update');

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
    $notification = \App\Models\Notification::withTrashed()->where('id', $id)->first();
    if (is_null($notification))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($notification->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $notification->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The notification has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/notifications')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $notification->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The notification has been soft deleted successfully');
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
    $notification = \App\Models\Notification::withTrashed()->where('id', $id)->first();
    if (is_null($notification))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($notification->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $notification->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The notification has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   */
  public static function prepareNotification($item, string $event): void
  {
    $item->refresh();
    $events = \App\Models\Definitions\Notification::getEvents();
    $notif = new self();

    // Search if event exists for this type of item
    $className = get_class($item);

    if (isset($events[$className]))
    {
      if (isset($events[$className][$event]))
      {
        // Search in DB if a notification exists
        $notifications = \App\Models\Notification::
            with('templates')
          ->where('item_type', $className)
          ->where('event', $event)
          ->get();
        foreach ($notifications as $notification)
        {
          // We create a notification and push it into the queuednotification
          foreach ($notification->templates as $template)
          {
            // Get translation
            $translation = \App\Models\Notificationtemplatetranslation::
                where('notificationtemplate_id', $template->id)
              ->first();
            if (!is_null($translation))
            {
              $queued = new \App\Models\Queuednotification();
              // $queued->entity_id
              $queued->item_type = 'MailCollector';
              $queued->notificationtemplate_id = $template->id;
              $queued->name = $notif->render($translation->subject, $item) . ' | ' . $translation->id;
              $queued->sender = 'xx@xx.xx';
              $queued->sendername = '';
              $queued->recipient = 'xx@xx.xx';
              $queued->recipientname = '';
              $queued->replyto = 'xx@xx.xx';
              $queued->replytoname = '';
              $queued->headers = '';
              if (!is_null($translation->content_html))
              {
                $queued->body_html = $notif->render($translation->content_html, $item);
              }
              if (!is_null($translation->content_text))
              {
                $queued->body_text = $notif->render($translation->content_text, $item);
              }
              $queued->mode = $template->getRelationValue('pivot')->mode;
              $queued->save();
            }
          }
        }
      }
    }
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   */
  public function render(string $text, $item): string
  {
    $loader = new \Twig\Loader\ArrayLoader([
      'notif.html' => $text,
    ]);
    $twig = new \Twig\Environment($loader, []);
    $template = $twig->load('notif.html');

    $data = $this->generateDataForNotification($item);
    // Manage lang
    $langData = $this->generateLangdataForNotification($item);
    $data['lang'] = $langData;

    try {
      $text = $template->render($data);
    }
    catch (\Throwable $th)
    {
      \App\v1\Controllers\Toolbox::addSessionMessage('Error with notification: ' . $th->getMessage(), 'error');
      return '';
    }
    return $text;
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   * @param array<mixed> $data
   * @param array<mixed> $fields
   *
   * @return array<mixed>
   */
  private function generateDataForNotification($item, array $data = [], array|null $fields = null): array
  {
    $nestedFields = [];
    // Special case for nested, for example have field 'user'completename'
    if (!is_null($fields))
    {
      foreach ($fields as $field)
      {
        if (strstr($field, '.'))
        {
          $spl = explode('.', $field);
          $first = array_shift($spl);
          if (!isset($nestedFields[$first]))
          {
            $nestedFields[$first] = [];
          }
          $nestedFields[$first][] = implode('.', $spl);
        }
      }
    }
    $definitions = $item->getDefinitions(true);
    foreach ($definitions as $definition)
    {
      // Special case, for example have user.completename
      if (
          !is_null($fields) &&
          !in_array($definition->name, $fields) &&
          !isset($nestedFields[$definition->name])
      )
      {
        continue;
      }
      if (count($definition->relationfields) > 0)
      {
        $relationFields = [];
        if (isset($nestedFields[$definition->name]))
        {
          $relationFields = $nestedFields[$definition->name];
        }
        else
        {
          $relationFields = $definition->relationfields;
        }

        // it's relationship
        if (!is_null($definition->dbname))
        {
          // one to one/many relation
          if (is_null($item->{$definition->name}))
          {
            $emptyItem = new $definition->itemtype();
            $data[$definition->name] = $this->generateDataForNotification(
              $emptyItem,
              [],
              $relationFields
            );
              // $data[$definition['name']] = [];
          }
          else
          {
            $data[$definition->name] = $this->generateDataForNotification(
              $item->{$definition->name},
              [],
              $relationFields
            );
          }
        }
        else
        {
          // many to may relation
          $data[$definition->name] = [];
          foreach ($item->{$definition->name}()->get() as $rItem)
          {
            $tempData = $this->generateDataForNotification($rItem, [], $relationFields);
            $data[$definition->name][] = $tempData;
          }
        }
      }
      else
      {
        // it's fields
        if (is_null($item->{$definition->name}))
        {
          $data[$definition->name] = null;
        }
        elseif (count($definition->values) > 0)
        {
          if (empty($item->{$definition->name}))
          {
            $data[$definition->name] = null;
          }
          else
          {
            $data[$definition->name] = $definition->values[$item->{$definition->name}]['title'];
          }
        }
        elseif ($definition->type == 'datetime' && ($item->{$definition->name} instanceof \Illuminate\Support\Carbon))
        {
          $data[$definition->name] = $item->{$definition->name}->format('Y-m-d H:i:s');
        }
        else
        {
          $data[$definition->name] = $item->{$definition->name};
        }
      }
    }
    return $data;
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   * @param array<mixed> $data
   * @param array<mixed> $fields
   *
   * @return array<mixed>
   */
  private function generateLangdataForNotification($item, array $data = [], array|null $fields = null): array
  {
    $nestedFields = [];
    // Special case for nested, for example have field 'user'completename'
    if (!is_null($fields))
    {
      foreach ($fields as $field)
      {
        if (strstr($field, '.'))
        {
          $spl = explode('.', $field);
          $first = array_shift($spl);
          if (!isset($nestedFields[$first]))
          {
            $nestedFields[$first] = [];
          }
          $nestedFields[$first][] = implode('.', $spl);
        }
      }
    }

    $definitions = $item->getDefinitions(true);
    foreach ($definitions as $definition)
    {
      // Special case, for example have user.completename
      if (
          !is_null($fields) &&
          !in_array($definition->name, $fields) &&
          !isset($nestedFields[$definition->name])
      )
      {
        continue;
      }

      if (count($definition->relationfields) > 0)
      {
        $relationFields = [];
        if (isset($nestedFields[$definition->name]))
        {
          $relationFields = $nestedFields[$definition->name];
        }
        else
        {
          $relationFields = $definition->relationfields;
        }

        $emptyItem = new $definition->itemtype();
        $data[$definition->name] = $this->generateLangdataForNotification(
          $emptyItem,
          [],
          $relationFields
        );
      }
      else
      {
        $data[$definition->name] = $definition->title;
      }
    }
    return $data;
  }
}
