<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Reservation
{
  /**
   * @param array<string, string> $args
   */
  public function showSubReservations(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/reservations');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myReservations = [];
    $myReservations_old = [];
    foreach ($myItem->reservations as $current_reservationitem)
    {
      if ($current_reservationitem->reservations !== null)
      {
        foreach ($current_reservationitem->reservations as $current_reservation)
        {
          $begin = $current_reservation->begin;

          $end = $current_reservation->end;

          $user = '';
          $user_url = '';
          if ($current_reservation->user !== null)
          {
            $user = $this->genereUserName(
              $current_reservation->user->name,
              $current_reservation->user->lastname,
              $current_reservation->user->firstname
            );
            $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $current_reservation->user->id);
          }

          $comment = $current_reservation->comment;


          if ($end < date('Y-m-d H:i:s'))
          {
            $myReservations_old[] = [
              'begin'       => $begin,
              'end'         => $end,
              'user'        => $user,
              'user_url'    => $user_url,
              'comment'     => $comment,
            ];
          } else {
            $myReservations[] = [
              'begin'       => $begin,
              'end'         => $end,
              'user'        => $user,
              'user_url'    => $user_url,
              'comment'     => $comment,
            ];
          }
        }
      }
    }

    // tri par ordre + ancien
    array_multisort(array_column($myReservations, 'begin'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $myReservations);
    // tri par ordre + recent
    array_multisort(
      array_column($myReservations_old, 'begin'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myReservations_old
    );

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('reservations', $myReservations);
    $viewData->addData('reservations_old', $myReservations_old);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('start_date', $translator->translate('Start date'));
    $viewData->addTranslation('end_date', $translator->translate('End date'));
    $viewData->addTranslation('by', $translator->translate('By'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));
    $viewData->addTranslation('current_reservations', $translator->translate('Current and future reservations'));
    $viewData->addTranslation('past_reservations', $translator->translate('Past reservations'));
    $viewData->addTranslation('no_reservations', $translator->translate('No reservation'));

    return $view->render($response, 'subitem/reservations.html.twig', (array)$viewData);
  }
}
