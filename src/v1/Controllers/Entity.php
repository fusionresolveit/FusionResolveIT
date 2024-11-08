<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Entity extends Common
{
  protected $model = '\App\Models\Entity';
  protected $rootUrl2 = '/entities/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Entity();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Entity();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Entity();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubAddress(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $address = [];
    foreach ($myItem as $os)
    {
      $address = [
        'phonenumber' => $myItem->phonenumber,
        'fax' => $myItem->fax,
        'website' => $myItem->website,
        'email' => $myItem->email,
        'address' => $myItem->address,
        'postcode' => $myItem->postcode,
        'town' => $myItem->town,
        'state' => $myItem->state,
        'country' => $myItem->country,
        'longitude' => $myItem->longitude,
        'latitude' => $myItem->latitude,
        'altitude' => $myItem->altitude,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/address');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));


    $getDefs = $item->getSpecificFunction('getDefinitionAddress');
    $myItemData = [
      'phonenumber'  => $address['phonenumber'],
      'fax'  => $address['fax'],
      'website'  => $address['website'],
      'email'  => $address['email'],
      'address'  => $address['address'],
      'postcode'  => $address['postcode'],
      'town'  => $address['town'],
      'state'  => $address['state'],
      'country'  => $address['country'],
      'longitude'  => $address['longitude'],
      'latitude'  => $address['latitude'],
      'altitude'  => $address['altitude'],
    ];
    $myItemDataObject = json_decode(json_encode($myItemData));

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));

    return $view->render($response, 'subitem/adress.html.twig', (array)$viewData);
  }
}
