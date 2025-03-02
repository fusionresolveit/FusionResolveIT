<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostCategory extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Category */
  public $category;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?\App\Models\Knowbaseitemcategory */
  public $knowbaseitemcategory;

  /** @var ?string */
  public $code;

  /** @var ?bool */
  public $is_helpdeskvisible;

  /** @var ?bool */
  public $is_incident;

  /** @var ?bool */
  public $is_request;

  /** @var ?bool */
  public $is_problem;

  /** @var ?bool */
  public $is_change;

  /** @var ?\App\Models\Tickettemplate */
  public $tickettemplateDemand;

  /** @var ?\App\Models\Tickettemplate */
  public $tickettemplateIncident;

  /** @var ?\App\Models\Changetemplate */
  public $changetemplate;

  /** @var ?\App\Models\Problemtemplate */
  public $problemtemplate;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\Entity */
  public $entity;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Category');
    $category = new \App\Models\Category();
    $this->definitions = $category->getDefinitions();

    $this->name = $this->setName($data);

    $this->category = $this->setCategory($data);

    $this->user = $this->setUser($data);

    $this->group = $this->setGroup($data);

    if (
        Validation::attrNumericVal('knowbaseitemcategory')->isValid($data) &&
        isset($data->knowbaseitemcategory)
    )
    {
      $knowbaseitemcategory = \App\Models\Knowbaseitemcategory::where('id', $data->knowbaseitemcategory)->first();
      if (!is_null($knowbaseitemcategory))
      {
        $this->knowbaseitemcategory = $knowbaseitemcategory;
      }
      elseif (intval($data->knowbaseitemcategory) == 0)
      {
        $emptyKnowbaseitemcategory = new \App\Models\Knowbaseitemcategory();
        $emptyKnowbaseitemcategory->id = 0;
        $this->knowbaseitemcategory = $emptyKnowbaseitemcategory;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('code')->isValid($data) &&
        isset($data->code)
    )
    {
      $this->code = $data->code;
    }

    if (
        Validation::attrStr('is_helpdeskvisible')->isValid($data) &&
        isset($data->is_helpdeskvisible) &&
        $data->is_helpdeskvisible == 'on'
    )
    {
      $this->is_helpdeskvisible = true;
    } else {
      $this->is_helpdeskvisible = false;
    }

    if (
        Validation::attrStr('is_incident')->isValid($data) &&
        isset($data->is_incident) &&
        $data->is_incident == 'on'
    )
    {
      $this->is_incident = true;
    } else {
      $this->is_incident = false;
    }

    if (
        Validation::attrStr('is_request')->isValid($data) &&
        isset($data->is_request) &&
        $data->is_request == 'on'
    )
    {
      $this->is_request = true;
    } else {
      $this->is_request = false;
    }

    if (
        Validation::attrStr('is_problem')->isValid($data) &&
        isset($data->is_problem) &&
        $data->is_problem == 'on'
    )
    {
      $this->is_problem = true;
    } else {
      $this->is_problem = false;
    }

    if (
        Validation::attrStr('is_change')->isValid($data) &&
        isset($data->is_change) &&
        $data->is_change == 'on'
    )
    {
      $this->is_change = true;
    } else {
      $this->is_change = false;
    }

    if (
        Validation::attrNumericVal('tickettemplateDemand')->isValid($data) &&
        isset($data->tickettemplateDemand)
    )
    {
      $ttd = \App\Models\Tickettemplate::where('id', $data->tickettemplateDemand)->first();
      if (!is_null($ttd))
      {
        $this->tickettemplateDemand = $ttd;
      }
      elseif (intval($data->tickettemplateDemand) == 0)
      {
        $emptyTickettemplate = new \App\Models\Tickettemplate();
        $emptyTickettemplate->id = 0;
        $this->tickettemplateDemand = $emptyTickettemplate;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('tickettemplateIncident')->isValid($data) &&
        isset($data->tickettemplateIncident)
    )
    {
      $tti = \App\Models\Tickettemplate::where('id', $data->tickettemplateIncident)->first();
      if (!is_null($tti))
      {
        $this->tickettemplateIncident = $tti;
      }
      elseif (intval($data->tickettemplateIncident) == 0)
      {
        $emptyTickettemplate = new \App\Models\Tickettemplate();
        $emptyTickettemplate->id = 0;
        $this->tickettemplateIncident = $emptyTickettemplate;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('changetemplate')->isValid($data) &&
        isset($data->changetemplate)
    )
    {
      $changetemplate = \App\Models\Changetemplate::where('id', $data->changetemplate)->first();
      if (!is_null($changetemplate))
      {
        $this->changetemplate = $changetemplate;
      }
      elseif (intval($data->changetemplate) == 0)
      {
        $emptyChangetemplate = new \App\Models\Changetemplate();
        $emptyChangetemplate->id = 0;
        $this->changetemplate = $emptyChangetemplate;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('problemtemplate')->isValid($data) &&
        isset($data->problemtemplate)
    )
    {
      $problemtemplate = \App\Models\Problemtemplate::where('id', $data->problemtemplate)->first();
      if (!is_null($problemtemplate))
      {
        $this->problemtemplate = $problemtemplate;
      }
      elseif (intval($data->problemtemplate) == 0)
      {
        $emptyProblemtemplate = new \App\Models\Problemtemplate();
        $emptyProblemtemplate->id = 0;
        $this->problemtemplate = $emptyProblemtemplate;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    $this->entity = $this->setEntity($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, category?: \App\Models\Category, user?: \App\Models\User, group?: \App\Models\Group,
   *               knowbaseitemcategory?: \App\Models\Knowbaseitemcategory, code?: string, is_helpdeskvisible?: bool,
   *               is_incident?: bool, is_request?: bool, is_problem?: bool, is_change?: bool,
   *               tickettemplateDemand?: \App\Models\Tickettemplate,
   *               tickettemplateIncident?: \App\Models\Tickettemplate, changetemplate?: \App\Models\Changetemplate,
   *               problemtemplate?: \App\Models\Problemtemplate, comment?: string, entity?: \App\Models\Entity,
   *               is_recursive?: bool}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    $vars = get_object_vars($this);
    $data = [];
    foreach (array_keys($vars) as $key)
    {
      if (!is_null($this->{$key}))
      {
        if (!$filterRights)
        {
          $this->getFieldForArray($key, $data);
        } else {
          // TODO filter by custom
          if (is_null($this->profileright))
          {
            return [];
          }
          elseif (count($this->profilerightcustoms) > 0)
          {
            foreach ($this->profilerightcustoms as $custom)
            {
              if ($custom->write)
              {
                $this->getFieldForArray($key, $data);
              }
            }
          } else {
            $this->getFieldForArray($key, $data);
          }
        }
      }
    }
    return $data;
  }

  /**
   * @param-out array{name?: string, category?: \App\Models\Category, user?: \App\Models\User,
   *                  group?: \App\Models\Group, knowbaseitemcategory?: \App\Models\Knowbaseitemcategory, code?: string,
   *                  is_helpdeskvisible?: bool, is_incident?: bool, is_request?: bool, is_problem?: bool,
   *                  is_change?: bool, tickettemplateDemand?: \App\Models\Tickettemplate,
   *                  tickettemplateIncident?: \App\Models\Tickettemplate, changetemplate?: \App\Models\Changetemplate,
   *                  problemtemplate?: \App\Models\Problemtemplate, comment?: string, entity?: \App\Models\Entity,
   *                  is_recursive?: bool} $data
   */
  private function getFieldForArray(string $key, mixed &$data): void
  {
    foreach ($this->definitions as $def)
    {
      if ($def->name == $key)
      {
        if (!is_null($def->dbname))
        {
          $data[$def->dbname] = $this->{$key}->id;
          return;
        }
        if ($def->multiple === true)
        {
          $data[$key] = [];
          foreach ($this->{$key} as $item)
          {
            $data[$key][] = $item->id;
          }
          return;
        }
        $data[$key] = $this->{$key};
        return;
      }
    }
  }
}
