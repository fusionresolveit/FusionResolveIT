<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostLocation;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Location extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;

  protected $model = \App\Models\Location::class;
  protected $rootUrl2 = '/dropdowns/locations/';

  protected function instanciateModel(): \App\Models\Location
  {
    return new \App\Models\Location();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostLocation((object) $request->getParsedBody());

    $location = new \App\Models\Location();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($location))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $location = \App\Models\Location::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The location has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($location, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/locations/' . $location->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/locations')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostLocation((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $location = \App\Models\Location::where('id', $id)->first();
    if (is_null($location))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($location))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $location->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The location has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($location, 'update');

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
    $location = \App\Models\Location::withTrashed()->where('id', $id)->first();
    if (is_null($location))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($location->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $location->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The location has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/locations')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $location->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The location has been soft deleted successfully');
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
    $location = \App\Models\Location::withTrashed()->where('id', $id)->first();
    if (is_null($location))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($location->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $location->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The location has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubLocations(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Location();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Location();
    $myItem2 = $item2->where('location_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/locations');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myLocations = [];
    foreach ($myItem2 as $current_location)
    {
      $name = $current_location->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $current_location->id);

      $entity = '';
      $entity_url = '';
      if ($current_location->entity !== null)
      {
        $entity = $current_location->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $current_location->entity->id);
      }

      $address = $current_location->address;

      $postcode = $current_location->postcode;

      $town = $current_location->town;

      $state = $current_location->state;

      $country = $current_location->country;

      $building = $current_location->building;

      $room = $current_location->room;

      $latitude = $current_location->latitude;

      $longitude = $current_location->longitude;

      $altitude = $current_location->altitude;

      $comment = $current_location->comment;

      $myLocations[$current_location->id] = [
        'name'             => $name,
        'url'              => $url,
        'entity'           => $entity,
        'entity_url'       => $entity_url,
        'address'          => $address,
        'postcode'         => $postcode,
        'town'             => $town,
        'state'            => $state,
        'country'          => $country,
        'building'         => $building,
        'room'             => $room,
        'latitude'         => $latitude,
        'longitude'        => $longitude,
        'altitude'         => $altitude,
        'comment'          => $comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('locations', $myLocations);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('address', $translator->translate('Address'));
    $viewData->addTranslation('postcode', $translator->translate('Postal code'));
    $viewData->addTranslation('town', $translator->translate('City'));
    $viewData->addTranslation('state', $translator->translate('location' . "\004" . 'State'));
    $viewData->addTranslation('country', $translator->translate('Country'));
    $viewData->addTranslation('building', $translator->translate('Building number'));
    $viewData->addTranslation('room', $translator->translate('Room number'));
    $viewData->addTranslation('latitude', $translator->translate('Latitude'));
    $viewData->addTranslation('longitude', $translator->translate('Longitude'));
    $viewData->addTranslation('altitude', $translator->translate('Altitude'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/locations.html.twig', (array)$viewData);
  }
}
