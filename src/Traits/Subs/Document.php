<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Document
{
  /**
   * @param array<string, string> $args
   */
  public function showSubDocuments(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('documents')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/documents');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDocuments = [];
    foreach ($myItem->documents as $document)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

      $entity = '';
      $entity_url = '';
      if ($document->entity !== null)
      {
        $entity = $document->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $document->entity->id);
      }

      $rubrique = '';
      $rubrique_url = '';
      if ($document->categorie !== null)
      {
        $rubrique = $document->categorie->name;
        $rubrique_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/documentcategories/',
          $document->categorie->id
        );
      }

      $myDocuments[$document->id] = [
        'name'              => $document->name,
        'date'              => $document->getRelationValue('pivot')->updated_at,
        'url'               => $url,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'file'              => $document->filename,
        'weblink'           => $document->link,
        'rubrique'          => $rubrique,
        'rubrique_url'      => $rubrique_url,
        'mimetype'          => $document->mime,
        'balise'            => $document->tag,
      ];
    }

    // tri de la + récente à la + ancienne
    array_multisort(array_column($myDocuments, 'date'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $myDocuments);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('documents', $myDocuments);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('file', $translator->translate('File'));
    $viewData->addTranslation('weblink', $translator->translate('Web link'));
    $viewData->addTranslation('rubrique', $translator->translate('Heading'));
    $viewData->addTranslation('mimetype', $translator->translate('MIME type'));
    $viewData->addTranslation('balise', $translator->translate('Tag'));
    $viewData->addTranslation('date', $translator->translatePlural('Dates', 'Dates', 1));

    return $view->render($response, 'subitem/documents.html.twig', (array)$viewData);
  }
}
