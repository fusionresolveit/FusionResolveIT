<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Volume
{
  /**
   * @param array<string, string> $args
   */
  public function showSubVolumes(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('volumes')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/volumes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myVolumes = [];
    foreach ($myItem->volumes as $volume)
    {
      if ($volume->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      } else {
        $auto_val = $translator->translate('No');
      }

      $filesystem = '';
      $filesystem_url = '';
      if ($volume->filesystem !== null)
      {
        $filesystem = $volume->filesystem->name;
        $filesystem_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/filesystems/', $volume->filesystem->id);
      }

      $usedpercent = 100;
      if ($volume->totalsize > 0)
      {
        $usedpercent = 100 - round(($volume->freesize / $volume->totalsize) * 100);
      }

      $encryption_status_val = '';
      if ($volume->encryption_status == 0)
      {
        $encryption_status_val = $translator->translate('Not encrypted');
      }
      if ($volume->encryption_status == 1)
      {
        $encryption_status_val = $translator->translate('Encrypted');
      }
      if ($volume->encryption_status == 2)
      {
        $encryption_status_val = $translator->translate('Partially encrypted');
      }

      $myVolumes[] = [
        'name'                      => $volume->name,
        'auto'                      => $volume->is_dynamic,
        'auto_val'                  => $auto_val,
        'device'                    => $volume->device,
        'mountpoint'                => $volume->mountpoint,
        'filesystem'                => $filesystem,
        'filesystem_url'            => $filesystem_url,
        'totalsize'                 => $volume->totalsize,
        'freesize'                  => $volume->freesize,
        'usedpercent'               => $usedpercent,
        'encryption_status'         => $volume->encryption_status,
        'encryption_status_val'     => $encryption_status_val,
        'encryption_tool'           => $volume->encryption_tool,
        'encryption_algorithm'      => $volume->encryption_algorithm,
        'encryption_type'           => $volume->encryption_type,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('volumes', $myVolumes);

    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('device', $translator->translate('Partition'));
    $viewData->addTranslation('mountpoint', $translator->translate('Mount point'));
    $viewData->addTranslation('filesystem', $translator->translatePlural('File system', 'File systems', 1));
    $viewData->addTranslation('totalsize', $translator->translate('Global size'));
    $viewData->addTranslation('freesize', $translator->translate('Free size'));
    $viewData->addTranslation('encryption', $translator->translate('Encryption'));
    $viewData->addTranslation('encryption_algorithm', $translator->translate('Encryption algorithm'));
    $viewData->addTranslation('encryption_tool', $translator->translate('Encryption tool'));
    $viewData->addTranslation('encryption_type', $translator->translate('Encryption type'));
    $viewData->addTranslation('usedpercent', 'Pourcentage utilisé');

    return $view->render($response, 'subitem/volumes.html.twig', (array)$viewData);
  }
}
