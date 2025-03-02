<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Contract
{
  /**
   * @param array<string, string> $args
   */
  public function showSubContracts(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('contracts')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/contracts');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myContracts = [];
    foreach ($myItem->contracts as $contract)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/contracts/', $contract->id);

      $entity = '';
      $entity_url = '';
      if ($contract->entity !== null)
      {
        $entity = $contract->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $contract->entity->id);
      }

      $type = '';
      $contracttype_url = '';
      if ($contract->type !== null)
      {
        $type = $contract->type->name;
        $contracttype_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/contracttypes/', $contract->type->id);
      }

      $suppliers = [];
      if ($contract->suppliers !== null)
      {
        foreach ($contract->suppliers as $supplier)
        {
          $supplier_url = $this->genereRootUrl2Link($rootUrl2, '/suppliers/', $supplier->id);

          $suppliers[$supplier->id] = [
            'name' => $supplier->name,
            'url' => $supplier_url,
          ];
        }
      }

      $duration = $contract->duration;
      if ($duration == 0)
      {
        $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', 1), $duration);
      } else {
        $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', $duration), $duration);
      }

      $ladate = $contract->begin_date;
      if (!is_null($ladate))
      {
        $ladateTimestamp = strtotime($ladate);
        if ($ladateTimestamp !== false && $duration != 0)
        {
          $futureTimestamp = strtotime('+' . $duration . ' month', $ladateTimestamp);
          if ($futureTimestamp !== false)
          {
            $end_date = date('Y-m-d', $futureTimestamp);
            if ($end_date < date('Y-m-d'))
            {
              $end_date = "<span style=\"color: red;\">" . $end_date . "</span>";
            }
            $initial_contract_period = $initial_contract_period . ' => ' . $end_date;
          }
        }
      }

      $myContracts[$contract->id] = [
        'name'                      => $contract->name,
        'url'                       => $url,
        'entity'                    => $entity,
        'entity_url'                => $entity_url,
        'number'                    => $contract->num,
        'type'                      => $type,
        'contracttype_url'          => $contracttype_url,
        'suppliers'                 => $suppliers,
        'start_date'                => $contract->begin_date,
        'initial_contract_period'   => $initial_contract_period,
      ];
    }

    array_multisort(array_column($myContracts, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myContracts);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('contracts', $myContracts);
    $viewData->addData('show_suppliers', true);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('number', $translator->translate('phone' . "\004" . 'Number'));
    $viewData->addTranslation('type', $translator->translatePlural('Contract type', 'Contract types', 1));
    $viewData->addTranslation('supplier', $translator->translatePlural('Supplier', 'Suppliers', 1));
    $viewData->addTranslation('start_date', $translator->translate('Start date'));
    $viewData->addTranslation('initial_contract_period', $translator->translate('Initial contract period'));

    return $view->render($response, 'subitem/contracts.html.twig', (array)$viewData);
  }
}
