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
        $auto_val = pgettext('global', 'Yes');
      } else {
        $auto_val = pgettext('global', 'No');
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
        $encryption_status_val = pgettext('volume', 'Not encrypted');
      }
      if ($volume->encryption_status == 1)
      {
        $encryption_status_val = pgettext('volume', 'Encrypted');
      }
      if ($volume->encryption_status == 2)
      {
        $encryption_status_val = pgettext('volume', 'Partially encrypted');
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

    $viewData->addTranslation('auto', pgettext('inventory device', 'Automatic inventory'));
    $viewData->addTranslation('device', pgettext('volume', 'Partition'));
    $viewData->addTranslation('mountpoint', pgettext('volume', 'Mount point'));
    $viewData->addTranslation('filesystem', npgettext('global', 'File system', 'File systems', 1));
    $viewData->addTranslation('totalsize', pgettext('volume', 'Global size'));
    $viewData->addTranslation('freesize', pgettext('volume', 'Free size'));
    $viewData->addTranslation('encryption', pgettext('volume', 'Encryption'));
    $viewData->addTranslation('encryption_algorithm', pgettext('volume', 'Encryption algorithm'));
    $viewData->addTranslation('encryption_tool', pgettext('volume', 'Encryption tool'));
    $viewData->addTranslation('encryption_type', pgettext('volume', 'Encryption type'));
    $viewData->addTranslation('usedpercent', 'Pourcentage utilisé');

    return $view->render($response, 'subitem/volumes.html.twig', (array)$viewData);
  }
}
