<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

abstract class Common
{
  /** @var string */
  protected $model = '';

  /** @var string */
  protected $rootUrl2 = '';

  /** @var string */
  protected $choose = '';

  /** @var string */
  protected $associateditems_model = '';

  /** @var string */
  protected $associateditems_model_id = '';

  /** @var int */
  protected $MINUTE_TIMESTAMP = 60;

  /** @var int */
  protected $HOUR_TIMESTAMP = 3600;

  /** @var int */
  protected $DAY_TIMESTAMP = 86400;

  /** @var int */
  protected $WEEK_TIMESTAMP = 604800;

  /** @var int */
  protected $MONTH_TIMESTAMP = 2592000;

  /** @var int */
  protected $APPROVAL_NONE = 1;

  /** @var int */
  protected $APPROVAL_WAITING = 2;

  /** @var int */
  protected $APPROVAL_ACCEPTED = 3;

  /** @var int */
  protected $APPROVAL_REFUSED = 4;

  /** @var int */
  protected $TTR = 0;

  /** @var int */
  protected $TTO = 1;

  protected function getUrlWithoutQuery(Request $request): string
  {
    $uri = $request->getUri();
    return $uri->getPath();
  }

  /**
   * @param array<string, string> $args
   */
  public function getAll(Request $request, Response $response, array $args): Response
  {
    /** @var \App\Models\Common */
    $item = new $this->model();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   * @param array<string, string> $args
   */
  protected function commonGetAll(Request $request, Response $response, array $args, $item): Response
  {
    $params = $request->getQueryParams();
    $page = 1;
    $view = Twig::fromRequest($request);
    if (!$this->canRightRead())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $search = new \App\v1\Controllers\Search();
    $url = $this->getUrlWithoutQuery($request);
    if (isset($params['page']) && is_numeric($params['page']))
    {
      $page = (int) $params['page'];
    }

    $fields = $search->getData($item, $url, $page, $params);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);
    $viewData->addHeaderTitle('Fusion Resolve IT - ' . $item->getTitle(2));

    $viewData->addData('fields', $fields);

    $viewData->addData('definition', $item->getDefinitions());

    return $view->render($response, 'search.html.twig', (array)$viewData);
  }

  /**
   * @param \App\Models\Ticket|\App\Models\Change $item
   * @param array<string, string> $args
   */
  protected function commonShowITILItem(Request $request, Response $response, array $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    // Load the item
    // $item->loadId($args['id']);
    $myItem = $item->withTrashed()->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($myItem))
    {
      throw new \Exception('Unauthorized access', 401);
    }
    $title = '';

    $fields = $item->getFormData($myItem);
    foreach ($fields as $field)
    {
      if ($field->name == 'name')
      {
        $title = $field->value;
        break;
      }
    }

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($this->getUrlWithoutQuery($request)));
    if (!$myItem->trashed() && method_exists($myItem, 'getColor'))
    {
      $viewData->addHeaderColor($myItem->getColor());
    }

    $viewData->addData('fields', $fields);
    $viewData->addData('feeds', $item->getFeeds(intval($args['id'])));
    $viewData->addData('title', $title);

    if (is_null($myItem->content))
    {
      $viewData->addData('content', null);
    } else {
      $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    }
    $ctrlFollowup = new \App\v1\Controllers\Followup();
    $viewData->addData('fullFollowup', $ctrlFollowup->canRightReadPrivateItem());

    $canAddFollowup = true;
    $canAddSolution = true;
    if ($myItem->canOnlyReadItem())
    {
      $canAddFollowup = false;
      $canAddSolution = false;
    }
    $viewData->addData('canAddFollowup', $canAddFollowup);
    $viewData->addData('canAddSolution', $canAddSolution);

    $viewData->addTranslation('description', $translator->translate('Description'));
    $viewData->addTranslation('feeds', $translator->translate('Feeds'));
    $viewData->addTranslation('followup', $translator->translatePlural('Followup', 'Followups', 1));
    $viewData->addTranslation('solution', $translator->translatePlural('Solution', 'Solutions', 1));
    $viewData->addTranslation('template', $translator->translatePlural('Template', 'Templates', 1));
    $viewData->addTranslation('private', $translator->translate('Private'));
    $viewData->addTranslation('sourcefollow', $translator->translate('Source of followup'));
    $viewData->addTranslation('category', $translator->translatePlural('Category', 'Categories', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('duration', $translator->translate('Duration'));
    $viewData->addTranslation('seconds', $translator->translatePlural('Second', 'Seconds', 2));
    $viewData->addTranslation('minutes', $translator->translatePlural('Minute', 'Minutes', 2));
    $viewData->addTranslation('hours', $translator->translatePlural('Hour', 'Hours', 2));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('addfollowup', $translator->translate('Add followup'));
    $viewData->addTranslation('timespent', $translator->translate('Time spent'));
    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));
    $viewData->addTranslation('yes', $translator->translate('Yes'));
    $viewData->addTranslation('no', $translator->translate('No'));

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   * @param array<string, string> $args
   */
  public function commonShowITILNewItem(Request $request, Response $response, array $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addData('fields', $item->getFormData($item));
    $viewData->addData('feeds', []);
    $viewData->addData('content', '');

    $viewData->addTranslation('description', $translator->translate('Description'));
    $viewData->addTranslation('feeds', $translator->translate('Feeds'));
    $viewData->addTranslation('followup', $translator->translatePlural('Followup', 'Followups', 1));
    $viewData->addTranslation('solution', $translator->translatePlural('Solution', 'Solutions', 1));
    $viewData->addTranslation('template', $translator->translatePlural('Template', 'Templates', 1));
    $viewData->addTranslation('private', $translator->translate('Private'));
    $viewData->addTranslation('sourcefollow', $translator->translate('Source of followup'));
    $viewData->addTranslation('category', $translator->translatePlural('Category', 'Categories', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('duration', $translator->translate('Duration'));
    $viewData->addTranslation('seconds', $translator->translatePlural('Second', 'Seconds', 2));
    $viewData->addTranslation('minutes', $translator->translatePlural('Minute', 'Minutes', 2));
    $viewData->addTranslation('hours', $translator->translatePlural('Hour', 'Hours', 2));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('addfollowup', $translator->translate('Add followup'));
    $viewData->addTranslation('timespent', $translator->translate('Time spent'));
    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));
    $viewData->addTranslation('yes', $translator->translate('Yes'));
    $viewData->addTranslation('no', $translator->translate('No'));

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    return [];
  }

  /**
   * @template C of \App\Models\Common
   * @param C $item
   * @return array<mixed>
   */
  protected function getInformationBottom($item, Request $request): array
  {
    return [];
  }

  /**
   * @return array<mixed>
   */
  public static function getStatusArray(): array
  {
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => $translator->translate('Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => $translator->translate('Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => $translator->translate('Closed'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      7 => [
        'title' => $translator->translate('status' . "\004" . 'Accepted'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      8 => [
        'title' => $translator->translate('Review'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      9 => [
        'title' => $translator->translate('Evaluation'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      10 => [
        'title' => $translator->translatePlural('Approval', 'Approvals', 1),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      11 => [
        'title' => $translator->translate('change' . "\004" . 'Testing'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      12 => [
        'title' => $translator->translate('Qualification'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getPriorityArray(): array
  {
    global $translator;
    return [
      6 => [
        'title' => $translator->translate('priority' . "\004" . 'Major'),
        'color' => 'fusionmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => $translator->translate('priority' . "\004" . 'Very high'),
        'color' => 'fusionveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => $translator->translate('priority' . "\004" . 'High'),
        'color' => 'fusionhigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => $translator->translate('priority' . "\004" . 'Medium'),
        'color' => 'fusionmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => $translator->translate('priority' . "\004" . 'Low'),
        'color' => 'fusionlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => $translator->translate('priority' . "\004" . 'Very low'),
        'color' => 'fusionverylow',
        'icon'  => 'volume off',
      ],
    ];
  }

  protected function canRightRead(): bool
  {
    $profileright = \App\Models\Profileright::where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->read)
        {
          return true;
        }
      }
    }
    if ($profileright->read || $profileright->readmyitems || $profileright->readmygroupitems)
    {
      return true;
    }
    return false;
  }

  protected function canRightCreate(): bool
  {
    $profileright = \App\Models\Profileright::where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->write)
        {
          return true;
        }
      }
    }
    if ($profileright->create)
    {
      return true;
    }
    return false;
  }

  protected function canRightUpdate(): bool
  {
    $profileright = \App\Models\Profileright::where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->write)
        {
          return true;
        }
      }
    }
    if ($profileright->update)
    {
      return true;
    }
    return false;
  }

  protected function canRightSoftdelete(): bool
  {
    $profileright = \App\Models\Profileright::where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->softdelete)
    {
      return true;
    }
    return false;
  }

  protected function canRightDelete(): bool
  {
    $profileright = \App\Models\Profileright::where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->delete)
    {
      return true;
    }
    return false;
  }

  public function canRightReadPrivateItem(): bool
  {
    return false;
  }

  public function timestampToString(int|float $time, bool $display_sec = true, bool $use_days = true): string
  {
    global $translator;

    $time = (float)$time;

    $sign = '';
    if ($time < 0)
    {
      $sign = '- ';
      $time = abs($time);
    }
    $time = floor($time);

    // Force display seconds if time is null
    if ($time < $this->MINUTE_TIMESTAMP)
    {
      $display_sec = true;
    }

    $units = $this->getTimestampTimeUnits($time);
    if ($use_days)
    {
      if ($units['day'] > 0)
      {
        if ($display_sec)
        {
          return sprintf(
            $translator->translate('%1$s%2$d days %3$d hours %4$d minutes %5$d seconds'),
            $sign,
            $units['day'],
            $units['hour'],
            $units['minute'],
            $units['second']
          );
        }
        return sprintf(
          $translator->translate('%1$s%2$d days %3$d hours %4$d minutes'),
          $sign,
          $units['day'],
          $units['hour'],
          $units['minute']
        );
      }
    } else {
      if ($units['day'] > 0)
      {
        $units['hour'] += 24 * $units['day'];
      }
    }

    if ($units['hour'] > 0)
    {
      if ($display_sec)
      {
        return sprintf(
          $translator->translate('%1$s%2$d hours %3$d minutes %4$d seconds'),
          $sign,
          $units['hour'],
          $units['minute'],
          $units['second']
        );
      }
      return sprintf($translator->translate('%1$s%2$d hours %3$d minutes'), $sign, $units['hour'], $units['minute']);
    }

    if ($units['minute'] > 0)
    {
      if ($display_sec)
      {
        return sprintf(
          $translator->translate('%1$s%2$d minutes %3$d seconds'),
          $sign,
          $units['minute'],
          $units['second']
        );
      }
      return sprintf(
        $translator->translatePlural('%1$s%2$d minute', '%1$s%2$d minutes', $units['minute']),
        $sign,
        $units['minute']
      );
    }

    if ($display_sec)
    {
      return sprintf(
        $translator->translatePlural('%1$s%2$s second', '%1$s%2$s seconds', $units['second']),
        $sign,
        $units['second']
      );
    }
    return '';
  }

  /**
   * @return array<mixed>
   */
  public function getTimestampTimeUnits(int|float $time): array
  {
    $out = [];

    $time          = round(abs($time));
    $out['second'] = 0;
    $out['minute'] = 0;
    $out['hour']   = 0;
    $out['day']    = 0;

    $out['second'] = $time % $this->MINUTE_TIMESTAMP;
    $time         -= $out['second'];

    if ($time > 0)
    {
      $out['minute'] = ($time % $this->HOUR_TIMESTAMP) / $this->MINUTE_TIMESTAMP;
      $time         -= $out['minute'] * $this->MINUTE_TIMESTAMP;

      if ($time > 0)
      {
        $out['hour'] = ($time % $this->DAY_TIMESTAMP) / $this->HOUR_TIMESTAMP;
        $time       -= $out['hour'] * $this->HOUR_TIMESTAMP;

        if ($time > 0)
        {
          $out['day'] = $time / $this->DAY_TIMESTAMP;
        }
      }
    }
    return $out;
  }

  public function showCosts(mixed $cost): string
  {
    return sprintf("%.2f", $cost);
  }

  public function computeCostTime(float $actiontime, float $cost_time): string
  {
    return $this->showCosts(($actiontime * $cost_time / $this->HOUR_TIMESTAMP));
  }

  public function computeTotalCost(float $actiontime, float $cost_time, float $cost_fixed, float $cost_material): float
  {
    return floatval(
      $this->showCosts(($actiontime * $cost_time / $this->HOUR_TIMESTAMP) + $cost_fixed + $cost_material)
    );
  }

  /**
   * @return array<int, string>
   */
  public function getDaysOfWeekArray(): array
  {
    global $translator;

    $tab = [];
    $tab[0] = $translator->translate("Sunday");
    $tab[1] = $translator->translate("Monday");
    $tab[2] = $translator->translate("Tuesday");
    $tab[3] = $translator->translate("Wednesday");
    $tab[4] = $translator->translate("Thursday");
    $tab[5] = $translator->translate("Friday");
    $tab[6] = $translator->translate("Saturday");

    return $tab;
  }

  /**
   * @return array<int, string>
   */
  public function getMonthsOfYearArray(): array
  {
    global $translator;

    $tab = [];
    $tab[1]  = $translator->translate("January");
    $tab[2]  = $translator->translate("February");
    $tab[3]  = $translator->translate("March");
    $tab[4]  = $translator->translate("April");
    $tab[5]  = $translator->translate("May");
    $tab[6]  = $translator->translate("June");
    $tab[7]  = $translator->translate("July");
    $tab[8]  = $translator->translate("August");
    $tab[9]  = $translator->translate("September");
    $tab[10] = $translator->translate("October");
    $tab[11] = $translator->translate("November");
    $tab[12] = $translator->translate("December");

    return $tab;
  }

  public function getValueWithUnit(string|int $value, string $unit, int $decimals = 0): string
  {
    global $translator;

    $formatted_number = is_numeric($value)
      ? $this->formatNumber($value, false, $decimals)
      : $value;

    if (strlen($unit) == 0)
    {
      return $formatted_number;
    }

    switch ($unit)
    {
      case 'year':
        //TRANS: %s is a number of years
          return sprintf($translator->translatePlural('%s year', '%s years', $value), $formatted_number);

      case 'month':
        //TRANS: %s is a number of months
          return sprintf($translator->translatePlural('%s month', '%s months', $value), $formatted_number);

      case 'day':
        //TRANS: %s is a number of days
          return sprintf($translator->translatePlural('%s day', '%s days', $value), $formatted_number);

      case 'hour':
        //TRANS: %s is a number of hours
          return sprintf($translator->translatePlural('%s hour', '%s hours', $value), $formatted_number);

      case 'minute':
        //TRANS: %s is a number of minutes
          return sprintf($translator->translatePlural('%s minute', '%s minutes', $value), $formatted_number);

      case 'second':
        //TRANS: %s is a number of seconds
          return sprintf($translator->translatePlural('%s second', '%s seconds', $value), $formatted_number);

      case 'millisecond':
        //TRANS: %s is a number of milliseconds
          return sprintf($translator->translatePlural('%s millisecond', '%s milliseconds', $value), $formatted_number);

      case 'auto':
          return $this->getSize(intval($value) * 1024 * 1024);

      case '%':
          return sprintf($translator->translate('%s%%'), $formatted_number);

      default:
          return sprintf($translator->translate('%1$s %2$s'), $formatted_number, $unit);
    }
  }

  public function formatNumber(string|int $number, bool $edit = false, int $forcedecimal = -1): string
  {
    if (!(isset($_SESSION['glpinumber_format'])))
    {
      $_SESSION['glpinumber_format'] = '';
    }

    // Php 5.3 : number_format() expects parameter 1 to be double,
    if ($number == "")
    {
      $number = 0;
    }
    elseif ($number == "-")
    { // used for not defines value (from Infocom::Amort, p.e.)
      return "-";
    }

    $number  = doubleval($number);
    $decimal = 2;
    if ($forcedecimal >= 0)
    {
      $decimal = $forcedecimal;
    }

    // Edit: clean display for mysql
    if ($edit)
    {
      return number_format($number, $decimal, '.', '');
    }

    // Display: clean display
    switch ($_SESSION['glpinumber_format'])
    {
      case 0: // French
          return str_replace(' ', '&nbsp;', number_format($number, $decimal, '.', ' '));

      case 2: // Other French
          return str_replace(' ', '&nbsp;', number_format($number, $decimal, ',', ' '));

      case 3: // No space with dot
          return number_format($number, $decimal, '.', '');

      case 4: // No space with comma
          return number_format($number, $decimal, ',', '');

      default: // English
          return number_format($number, $decimal, '.', ',');
    }
  }

  public function getSize(int $size): string
  {
    global $translator;

    //TRANS: list of unit (o for octet)
    $bytes = [
      $translator->translate('o'),
      $translator->translate('Kio'),
      $translator->translate('Mio'),
      $translator->translate('Gio'),
      $translator->translate('Tio')
    ];
    foreach ($bytes as $val)
    {
      if ($size > 1024)
      {
        $size = $size / 1024;
      } else {
        break;
      }
    }
    //TRANS: %1$s is a number maybe float or string and %2$s the unit
    return sprintf($translator->translate('%1$s %2$s'), round($size, 2), $val);
  }

  /**
   * @return array<int, mixed>
   */
  public function getApprovalStatus(): array
  {
    global $translator;

    return [
      $this->APPROVAL_WAITING => [
        'title' => $translator->translate('Waiting for approval'),
        'color' => '#FFC65D',
      ],
      $this->APPROVAL_REFUSED => [
        'title' => $translator->translate('Refused'),
        'color' => '#cf9b9b',
      ],
      $this->APPROVAL_ACCEPTED => [
        'title' => $translator->translate('Granted'),
        'color' => '#9BA563',
      ],
    ];
  }

  public function genereUserName(
    string|null $name,
    string|null $lastname = null,
    string|null $firstname = null,
    bool $add_name = false
  ): string
  {
    $ret = '';
    $names = [];
    if (!is_null($lastname))
    {
      $$names[] = $lastname;
    }
    if (!is_null($firstname))
    {
      $$names[] = $firstname;
    }

    $ret = implode(' ', $names);
    $ret = trim($ret);

    if ($ret != '')
    {
      if ($add_name === true && !is_null($name))
      {
        $ret = $ret . ' (' . $name . ')';
      }
    }
    elseif (!is_null($name))
    {
      $ret = $name;
    }

    return $ret;
  }

  public function genereRootUrl(Request $request, string $param = ''): string
  {
    $rootUrl = $this->getUrlWithoutQuery($request);
    if ($param != '')
    {
      $rootUrl = rtrim($rootUrl, '/' . $param);
    }

    return $rootUrl;
  }

  public function genereRootUrl2(string $rootUrl, string $param = ''): string
  {
    $rootUrl2 = '';
    if (($this->rootUrl2 != '') && ($param != ''))
    {
      $rootUrl2 = rtrim($rootUrl, $param);
    }

    return $rootUrl2;
  }

  public function genereRootUrl2Link(string $rootUrl2, string $param, int $id): string
  {
    $rootUrl2Link = '';
    if (($rootUrl2 != '') && ($param != '') && ($param != '//'))
    {
      $rootUrl2Link = $rootUrl2 . $param . strval($id);
    }

    return $rootUrl2Link;
  }

  // public function runRules(object $data, int|null $id): object
  // {
  //   return $data;
  // }

  /**
   * Redirect to referer page (previous page)
   */
  protected function goBack(Response $response): Response
  {
    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER']);
  }
}
