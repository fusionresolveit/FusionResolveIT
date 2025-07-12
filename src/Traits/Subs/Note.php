<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Note
{
  /**
   * @param array<string, string> $args
   */
  public function showSubNotes(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('notes')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/notes');

    $myNotes = [];
    foreach ($myItem->notes as $note)
    {
      $content = null;
      if (!is_null($note->content))
      {
        $content = str_ireplace("\n", "<br/>", $note->content);
      }

      $user = '';
      if ($note->user !== null)
      {
        $user = $this->genereUserName($note->user->name, $note->user->lastname, $note->user->firstname);
      }

      $user_lastupdater = '';
      if ($note->userlastupdater !== null)
      {
        $user_lastupdater = $this->genereUserName(
          $note->userlastupdater->name,
          $note->userlastupdater->lastname,
          $note->userlastupdater->firstname
        );
      }

      $create = sprintf(pgettext('global', 'Create by %1$s on %2$s'), $user, $note->created_at);

      $update = sprintf(pgettext('global', 'Last update by %1$s on %2$s'), $user_lastupdater, $note->updated_at);

      $myNotes[] = [
        'content'     => $content,
        'create'      => $create,
        'update'      => $update,
        'updated_at'  => $note->updated_at,
      ];
    }

    // tri de la + récente à la + ancienne
    array_multisort(array_column($myNotes, 'updated_at'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $myNotes);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('notes', $myNotes);

    $viewData->addTranslation('name', pgettext('global', 'Name'));

    return $view->render($response, 'subitem/notes.html.twig', (array)$viewData);
  }
}
