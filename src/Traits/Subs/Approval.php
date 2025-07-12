<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Approval
{
  /**
   * @param array<string, string> $args
   */
  public function showSubApprovals(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('approvals')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/approvals');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myApprovals = [];
    foreach ($myItem->approvals as $approval)
    {
      $status = $this->getApprovalStatus()[$approval->status];

      $request_date = $approval->submission_date;

      $request_user = '';
      $request_user_url = '';
      if ($approval->usersrequester !== null)
      {
        $request_user = $this->genereUserName(
          $approval->usersrequester->name,
          $approval->usersrequester->lastname,
          $approval->usersrequester->firstname
        );
        $request_user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $approval->usersrequester->id);
      }

      $request_comment = $approval->comment_submission;

      $approval_date = $approval->validation_date;

      $approval_user = '';
      $approval_user_url = '';
      if ($approval->uservalidate !== null)
      {
        $approval_user = $this->genereUserName(
          $approval->uservalidate->name,
          $approval->uservalidate->lastname,
          $approval->uservalidate->firstname
        );
        $approval_user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $approval->uservalidate->id);
      }

      $approval_comment = $approval->comment_validation;

      $myApprovals[] = [
        'status'              => $status,
        'request_date'        => $request_date,
        'request_user'        => $request_user,
        'request_user_url'    => $request_user_url,
        'request_comment'     => $request_comment,
        'approval_date'       => $approval_date,
        'approval_user'       => $approval_user,
        'approval_user_url'   => $approval_user_url,
        'approval_comment'    => $approval_comment,
      ];
    }

    // tri de la + récente à la + ancienne
    array_multisort(array_column($myApprovals, 'request_date'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $myApprovals);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('approvals', $myApprovals);

    $viewData->addTranslation('status', pgettext('global', 'Status'));
    $viewData->addTranslation('request_date', pgettext('ITIL', 'Request date'));
    $viewData->addTranslation('request_user', pgettext('ITIL', 'Approval requester'));
    $viewData->addTranslation('request_comment', pgettext('ITIL', 'Request comments'));
    $viewData->addTranslation('approval_date', pgettext('ITIL', 'Approval status'));
    $viewData->addTranslation('approval_user', pgettext('ITIL', 'Approver'));
    $viewData->addTranslation('approval_comment', pgettext('ITIL', 'Approval comments'));

    return $view->render($response, 'subitem/approvals.html.twig', (array)$viewData);
  }
}
