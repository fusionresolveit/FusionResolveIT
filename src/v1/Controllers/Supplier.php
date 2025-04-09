<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostSupplier;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Attacheditem;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Itil;
use App\Traits\Subs\Knowledgebasearticle;
use App\Traits\Subs\Note;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Supplier extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Note;
  use Externallink;
  use Knowledgebasearticle;
  use Document;
  use Contract;
  use Itil;
  use History;

  protected $model = \App\Models\Supplier::class;
  protected $rootUrl2 = '/suppliers/';
  protected $choose = 'suppliers';

  protected function instanciateModel(): \App\Models\Supplier
  {
    return new \App\Models\Supplier();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostSupplier((object) $request->getParsedBody());

    $supplier = new \App\Models\Supplier();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($supplier))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $supplier = \App\Models\Supplier::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The supplier has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($supplier, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/suppliers/' . $supplier->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/suppliers')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostSupplier((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $supplier = \App\Models\Supplier::where('id', $id)->first();
    if (is_null($supplier))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($supplier))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $supplier->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The supplier has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($supplier, 'update');

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
    $supplier = \App\Models\Supplier::withTrashed()->where('id', $id)->first();
    if (is_null($supplier))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($supplier->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $supplier->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The supplier has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/suppliers')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $supplier->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The supplier has been soft deleted successfully');
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
    $supplier = \App\Models\Supplier::withTrashed()->where('id', $id)->first();
    if (is_null($supplier))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($supplier->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $supplier->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The supplier has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubContracts(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Supplier();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $myItem2 = \App\Models\Contract::with('suppliers')->orderBy('name', 'asc')->get();

    $rootUrl = $this->genereRootUrl($request, '/contracts');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myContracts = [];
    foreach ($myItem2 as $contract)
    {
      $is_supplier_contract = false;
      if ($contract->suppliers !== null)
      {
        foreach ($contract->suppliers as $supplier)
        {
          if ($supplier->id == $args['id'])
          {
            $is_supplier_contract = true;
            break;
          }
        }
      }

      if ($is_supplier_contract == true)
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

        $duration = $contract->duration;
        $initial_contract_period = '';
        if ($duration == 0)
        {
          $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', 1), $duration);
        }
        if ($duration != 0)
        {
          $initial_contract_period = sprintf(
            $translator->translatePlural(
              '%d month',
              '%d months',
              $duration
            ),
            $duration
          );
        }

        if ($contract->begin_date !== null)
        {
          if ($duration != 0)
          {
            $beginDate = strtotime($contract->begin_date);
            if (is_numeric($beginDate))
            {
              $endDateTimestamp = strtotime('+' . $duration . ' month', $beginDate);
              if (is_int($endDateTimestamp))
              {
                $end_date = date('Y-m-d', $endDateTimestamp);
                if ($end_date < date('Y-m-d'))
                {
                  $end_date = "<span style=\"color: red;\">" . $end_date . "</span>";
                }
                $initial_contract_period = $initial_contract_period . ' => ' . $end_date;
              }
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
          'start_date'                => $contract->begin_date,
          'initial_contract_period'   => $initial_contract_period,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('contracts', $myContracts);
    $viewData->addData('show_suppliers', false);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('number', $translator->translate('phone' . "\004" . 'Number'));
    $viewData->addTranslation('type', $translator->translatePlural('Contract type', 'Contract types', 1));
    $viewData->addTranslation('supplier', $translator->translatePlural('Supplier', 'Suppliers', 1));
    $viewData->addTranslation('start_date', $translator->translate('Start date'));
    $viewData->addTranslation('initial_contract_period', $translator->translate('Initial contract period'));

    return $view->render($response, 'subitem/suppliercontracts.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubContacts(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Supplier();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $myItem2 = \App\Models\Contact::with('suppliers')->orderBy('name', 'asc')->get();

    $rootUrl = $this->genereRootUrl($request, '/contacts');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myContacts = [];
    foreach ($myItem2 as $contact)
    {
      $is_supplier_contact = false;
      if ($contact->suppliers !== null)
      {
        foreach ($contact->suppliers as $supplier)
        {
          if ($supplier->id == $args['id'])
          {
            $is_supplier_contact = true;
            break;
          }
        }
      }

      if ($is_supplier_contact == true)
      {
        $url = $this->genereRootUrl2Link($rootUrl2, '/contacts/', $contact->id);
        $url = '';
        if ($rootUrl2 != '')
        {
          $url = $rootUrl2 . "/contacts/" . $contact->id;
        }

        $entity = '';
        $entity_url = '';
        if ($contact->entity !== null)
        {
          $entity = $contact->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $contact->entity->id);
        }

        $type = '';
        $type_url = '';
        if ($contact->type !== null)
        {
          $type = $contact->type->name;
          $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/contacttypes/', $contact->type->id);
        }

        $myContacts[$contact->id] = [
          'name'        => $this->genereUserName($contact->name, $contact->name, $contact->firstname),
          'url'         => $url,
          'entity'      => $entity,
          'entity_url'  => $entity_url,
          'phone'       => $contact->phone,
          'phone2'      => $contact->phone2,
          'mobile'      => $contact->mobile,
          'fax'         => $contact->fax,
          'email'       => $contact->email,
          'type'        => $type,
          'type_url'    => $type_url,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('contacts', $myContacts);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('phone', $translator->translatePlural('Phone', 'Phones', 1));
    $viewData->addTranslation('phone2', $translator->translate('Phone 2'));
    $viewData->addTranslation('mobile', $translator->translate('Mobile phone'));
    $viewData->addTranslation('fax', $translator->translate('Fax'));
    $viewData->addTranslation('email', $translator->translatePlural('Email', 'Emails', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));

    return $view->render($response, 'subitem/suppliercontacts.html.twig', (array)$viewData);
  }
}
