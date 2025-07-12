<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostNotificationtemplate;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Notificationtemplate extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Notificationtemplate::class;

  protected function instanciateModel(): \App\Models\Notificationtemplate
  {
    return new \App\Models\Notificationtemplate();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostNotificationtemplate((object) $request->getParsedBody());

    $notificationtemplate = new \App\Models\Notificationtemplate();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($notificationtemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $notificationtemplate = \App\Models\Notificationtemplate::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($notificationtemplate, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/notificationtemplates/' . $notificationtemplate->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/notificationtemplates')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostNotificationtemplate((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $notificationtemplate = \App\Models\Notificationtemplate::where('id', $id)->first();
    if (is_null($notificationtemplate))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($notificationtemplate))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $notificationtemplate->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($notificationtemplate, 'update');

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
    $notificationtemplate = \App\Models\Notificationtemplate::withTrashed()->where('id', $id)->first();
    if (is_null($notificationtemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($notificationtemplate->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $notificationtemplate->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/notificationtemplates')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $notificationtemplate->delete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('softdeleted');
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
    $notificationtemplate = \App\Models\Notificationtemplate::withTrashed()->where('id', $id)->first();
    if (is_null($notificationtemplate))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($notificationtemplate->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $notificationtemplate->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubTemplatetranslations(Request $request, Response $response, array $args): Response
  {
    $view = Twig::fromRequest($request);

    $template = \App\Models\Notificationtemplate::where('id', $args['id'])->first();
    if (is_null($template))
    {
      throw new \Exception('Id not found', 404);
    }
    $notificationctrl = new \App\v1\Controllers\Notification();
    $templatetrs = \App\Models\Notificationtemplatetranslation::where('notificationtemplate_id', $args['id'])->get();
    $dataTranslate = [];
    foreach ($templatetrs as $templatetr)
    {
      $datat = [
        'id'            => $templatetr->id,
        'lang'          => $templatetr->language,
        'subject'       => true,
        'subject_error' => '',
        'text'          => false,
        'text_error'    => '',
        'html'          => true,
        'html_error'    => '',
      ];
      if (empty($datat['lang']))
      {
        $datat['lang'] = pgettext('notification', 'Default translation');
      }

      // verification of templates
      $className = $template->item_type;
      if ($className == 'Ticket')
      {
        $className = \App\Models\Ticket::class;
      }
      $item = new $className();
      if (!is_subclass_of($item, \App\Models\Common::class))
      {
        throw new \Exception('Error', 500);
      }

      try
      {
        $notificationctrl->render($templatetr->subject, $item);
      }
      catch (\Throwable $th)
      {
        $datat['subject_error'] = $th->getMessage();
        $datat['subject'] = false;
      }

      if (!is_null($templatetr->content_text))
      {
        try
        {
          $notificationctrl->render($templatetr->content_text, $item);
        }
        catch (\Throwable $th)
        {
          $datat['text_error'] = $th->getMessage();
          $datat['text'] = false;
        }
      }

      if (!is_null($templatetr->content_html))
      {
        try {
          $notificationctrl->render($templatetr->content_html, $item);
        }
        catch (\Throwable $th)
        {
          $datat['html_error'] = $th->getMessage();
          $datat['html'] = false;
        }
      }

      $dataTranslate[] = $datat;
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/templatetranslation');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($template, $request);

    $viewData->addRelatedPages($template->getRelatedPages($rootUrl));

    $viewData->addData('fields', $template->getFormData($template));
    $viewData->addData('translations', $dataTranslate);

    return $view->render($response, 'subitem/notificationtemplateslist.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubTemplatetranslation(Request $request, Response $response, array $args): Response
  {
    $view = Twig::fromRequest($request);
    $template = \App\Models\Notificationtemplate::where('id', $args['id'])->first();
    $templatetr = \App\Models\Notificationtemplatetranslation::where('id', $args['translationid'])->first();
    if (is_null($template) || is_null($templatetr))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/templatetranslation');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($templatetr, $request);

    $viewData->addRelatedPages($template->getRelatedPages($rootUrl));

    $viewData->addData('fields', $templatetr->getFormData($templatetr));
    // $viewData->addData('translations', $dataTranslate);

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }
}
