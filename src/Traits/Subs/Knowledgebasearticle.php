<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Knowledgebasearticle
{
  /**
   * @param array<string, string> $args
   */
  public function showSubKnowledgebasearticles(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('knowledgebasearticles')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/knowledgebasearticles');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myKnowledgebasearticles = [];
    foreach ($myItem->knowledgebasearticles as $article)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/knowledgebasearticles/', $article->id);

      $myKnowledgebasearticles[$article->id] = [
        'name'           => $article->name,
        'created_at'     => $article->created_at,
        'updated_at'     => $article->updated_at,
        'url'            => $url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('knowledgebasearticles', $myKnowledgebasearticles);

    $viewData->addTranslation('name', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('created_at', $translator->translate('Creation date'));
    $viewData->addTranslation('updated_at', $translator->translate('Update date'));

    return $view->render($response, 'subitem/knowledgebasearticles.html.twig', (array)$viewData);
  }
}
