<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostKnowledgebasearticle;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Entityview;
use App\Traits\Subs\Groupview;
use App\Traits\Subs\History;
use App\Traits\Subs\Profileview;
use App\Traits\Subs\Userview;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Factory\RendererFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Knowledgebasearticle extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Entityview;
  use Groupview;
  use Profileview;
  use Userview;
  use History;

  protected $model = \App\Models\Knowledgebasearticle::class;
  protected $rootUrl2 = '/knowledgebasearticless/';
  protected $choose = 'knowledgebasearticles';

  protected function instanciateModel(): \App\Models\Knowledgebasearticle
  {
    return new \App\Models\Knowledgebasearticle();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostKnowledgebasearticle((object) $request->getParsedBody());

    $knowledgebasearticle = new \App\Models\Knowledgebasearticle();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($knowledgebasearticle))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowledgebasearticle = \App\Models\Knowledgebasearticle::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($knowledgebasearticle, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/knowledgebasearticles/' . $knowledgebasearticle->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/knowledgebasearticles')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostKnowledgebasearticle((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowledgebasearticle = \App\Models\Knowledgebasearticle::where('id', $id)->first();
    if (is_null($knowledgebasearticle))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($knowledgebasearticle))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $knowledgebasearticle->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($knowledgebasearticle, 'update');

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
    $knowledgebasearticle = \App\Models\Knowledgebasearticle::withTrashed()->where('id', $id)->first();
    if (is_null($knowledgebasearticle))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($knowledgebasearticle->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowledgebasearticle->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/knowledgebasearticles')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowledgebasearticle->delete();
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
    $knowledgebasearticle = \App\Models\Knowledgebasearticle::withTrashed()->where('id', $id)->first();
    if (is_null($knowledgebasearticle))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($knowledgebasearticle->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $knowledgebasearticle->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showReadItem(Request $request, Response $response, array $args): Response
  {
    $view = Twig::fromRequest($request);

    $id = intval($args['id']);
    $article = \App\Models\Knowledgebasearticle::where('id', $id)->first();
    if (is_null($article))
    {
      throw new \Exception('Id not found', 404);
    }
    $article->views = $article->views + 1;
    $article->save();
    $article->refresh();

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($article, $request);
    $viewData->addHeaderColor('olive');

    $viewData->addData('article', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($article->article));
    $viewData->addData('views', $article->views);
    $viewData->addData('title', $article->name);

    return $view->render($response, 'user/article.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubRevisions(Request $request, Response $response, array $args): Response
  {
    $view = Twig::fromRequest($request);

    $id = intval($args['id']);
    $article = \App\Models\Knowledgebasearticle::where('id', $id)->first();
    if (is_null($article))
    {
      throw new \Exception('Id not found', 404);
    }

    $revisions = \App\Models\Knowledgebasearticlerevision::
        where('knowledgebasearticle_id', $id)
      ->orderBy('revision', 'desc')
      ->get();
    $revisions->shift();

    $activeRevision = null;
    $htmlDiff = '';
    if (isset($args['revisionid']))
    {
      $activeRevision = \App\Models\Knowledgebasearticlerevision::where('id', intval($args['revisionid']))->first();
      if (!is_null($activeRevision))
      {
        // Generate the diff
        $jsonResult = DiffHelper::calculate(
          explode("\n", $activeRevision->getAttribute('article')),
          explode("\n", $article->getAttribute('article')),
          'Json',
        );
        $htmlRenderer = RendererFactory::make('Inline');
        $htmlDiff = $htmlRenderer->renderArray(json_decode($jsonResult, true));
      }
    }

    $rootUrl = $this->genereRootUrl($request, '/revision');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($article, $request);
    $viewData->addRelatedPages($article->getRelatedPages($rootUrl));

    $viewData->addData('revisions', $revisions);
    $viewData->addData('activerevision', $activeRevision);
    $viewData->addData('diff', $htmlDiff);

    return $view->render($response, 'subitem/knowledgebasearticlerevisions.html.twig', (array)$viewData);
  }
}
