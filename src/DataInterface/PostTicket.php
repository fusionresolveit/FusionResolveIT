<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostTicket extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $content;

  /** @var ?\App\Models\Entity */
  public $entity;

  /** @var ?int */
  public $type;

  /** @var ?int */
  public $status;

  /** @var ?\App\Models\Category */
  public $category;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?int */
  public $urgency;

  /** @var ?int */
  public $impact;

  /** @var ?int */
  public $priority;

  /** @var ?int */
  public $time_to_resolve;

  /** @var ?\App\Models\User */
  public $usersidlastupdater;

  /** @var ?\App\Models\User */
  public $usersidrecipient;

  /** @var array<\App\Models\User> */
  public $requester;

  /** @var array<\App\Models\Group> */
  public $requestergroup;

  /** @var array<\App\Models\User> */
  public $watcher;

  /** @var array<\App\Models\Group> */
  public $watchergroup;

  /** @var array<\App\Models\User> */
  public $technician;

  /** @var array<\App\Models\Group> */
  public $techniciangroup;

  // followups
  // problems

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Ticket');
    $ticket = new \App\Models\Ticket();
    $this->definitions = $ticket->getDefinitions();
    $this->filledFields = [];

    $this->name = $this->setName($data);

    if (
        Validation::attrStr('content')->isValid($data) &&
        isset($data->content)
    )
    {
      $this->filledFields[] = 'content';
      $this->content = $data->content;
    }

    $this->entity = $this->setEntity($data);

    // type

    // status

    $this->category = $this->setCategory($data);

    $this->location = $this->setLocation($data);

    if (
        Validation::attrNumericVal('urgency')->isValid($data) &&
        isset($data->urgency)
    )
    {
      $this->filledFields[] = 'urgency';
      $this->urgency = intval($data->urgency);
    }

    if (
        Validation::attrNumericVal('impact')->isValid($data) &&
        isset($data->impact)
    )
    {
      $this->filledFields[] = 'impact';
      $this->impact = intval($data->impact);
    }

    if (
        Validation::attrNumericVal('priority')->isValid($data) &&
        isset($data->priority)
    )
    {
      $this->filledFields[] = 'priority';
      $this->priority = intval($data->priority);
    }

    // time_to_resolve

    // usersidlastupdater

    // usersidrecipient

    if (
        Validation::attrStr('requester')->isValid($data) &&
        isset($data->requester)
    )
    {
      $this->filledFields[] = 'requester';
      $this->requester = [];
      if (!empty($data->requester))
      {
        $ids = explode(',', $data->requester);
        foreach ($ids as $id)
        {
          if (Validation::numericVal()->isValid($id))
          {
            $user = \App\Models\User::where('id', $id)->first();
            if (!is_null($user))
            {
              $this->requester[] = $user;
            } else {
              throw new \Exception('Wrong data request', 400);
            }
          } else {
            throw new \Exception('Wrong data request', 400);
          }
        }
      }
    }

    if (
        Validation::attrStr('requestergroup')->isValid($data) &&
        isset($data->requestergroup)
    )
    {
      $this->filledFields[] = 'requestergroup';
      $this->requestergroup = [];
      if (!empty($data->requestergroup))
      {
        $ids = explode(',', $data->requestergroup);
        foreach ($ids as $id)
        {
          if (Validation::numericVal()->isValid($id))
          {
            $group = \App\Models\Group::where('id', $id)->first();
            if (!is_null($group))
            {
              $this->requestergroup[] = $group;
            } else {
              throw new \Exception('Wrong data request', 400);
            }
          } else {
            throw new \Exception('Wrong data request', 400);
          }
        }
      }
    }

    if (
        Validation::attrStr('watcher')->isValid($data) &&
        isset($data->watcher)
    )
    {
      $this->filledFields[] = 'watcher';
      $this->watcher = [];
      if (!empty($data->watcher))
      {
        $ids = explode(',', $data->watcher);
        foreach ($ids as $id)
        {
          if (Validation::numericVal()->isValid($id))
          {
            $user = \App\Models\User::where('id', $id)->first();
            if (!is_null($user))
            {
              $this->watcher[] = $user;
            } else {
              throw new \Exception('Wrong data request', 400);
            }
          } else {
            throw new \Exception('Wrong data request', 400);
          }
        }
      }
    }

    if (
        Validation::attrStr('watchergroup')->isValid($data) &&
        isset($data->watchergroup)
    )
    {
      $this->filledFields[] = 'watchergroup';
      $this->watchergroup = [];
      if (!empty($data->watchergroup))
      {
        $ids = explode(',', $data->watchergroup);
        foreach ($ids as $id)
        {
          if (Validation::numericVal()->isValid($id))
          {
            $group = \App\Models\Group::where('id', $id)->first();
            if (!is_null($group))
            {
              $this->watchergroup[] = $group;
            } else {
              throw new \Exception('Wrong data request', 400);
            }
          } else {
            throw new \Exception('Wrong data request', 400);
          }
        }
      }
    }

    if (
        Validation::attrStr('technician')->isValid($data) &&
        isset($data->technician)
    )
    {
      $this->filledFields[] = 'technician';
      $this->technician = [];
      if (!empty($data->technician))
      {
        $ids = explode(',', $data->technician);
        foreach ($ids as $id)
        {
          if (Validation::numericVal()->isValid($id))
          {
            $user = \App\Models\User::where('id', $id)->first();
            if (!is_null($user))
            {
              $this->technician[] = $user;
            } else {
              throw new \Exception('Wrong data request', 400);
            }
          } else {
            throw new \Exception('Wrong data request', 400);
          }
        }
      }
    }

    if (
        Validation::attrStr('techniciangroup')->isValid($data) &&
        isset($data->techniciangroup)
    )
    {
      $this->filledFields[] = 'techniciangroup';
      $this->techniciangroup = [];
      if (!empty($data->techniciangroup))
      {
        $ids = explode(',', $data->techniciangroup);
        foreach ($ids as $id)
        {
          if (Validation::numericVal()->isValid($id))
          {
            $group = \App\Models\Group::where('id', $id)->first();
            if (!is_null($group))
            {
              $this->techniciangroup[] = $group;
            } else {
              throw new \Exception('Wrong data request', 400);
            }
          } else {
            throw new \Exception('Wrong data request', 400);
          }
        }
      }
    }
  }

  /**
   * @return array{name?: string, content?: string, entity?: \App\Models\Entity, type?: int, status?: int,
   *               category?: \App\Models\Category, location?: \App\Models\Location, urgency?: int, impact?: int,
   *               priority?: int, time_to_resolve?: int, usersidlastupdater?: \App\Models\User,
   *               usersidrecipient?: \App\Models\User, requester?: array<\App\Models\User>,
   *               requestergroup?: array<\App\Models\Group>, watcher?: array<\App\Models\User>,
   *               watchergroup?: array<\App\Models\Group>, technician?: array<\App\Models\User>,
   *               techniciangroup?: array<\App\Models\Group>}
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
   * @param-out array{name?: string, content?: string, entity?: \App\Models\Entity, type?: int, status?: int,
   *                  category?: \App\Models\Category, location?: \App\Models\Location, urgency?: int, impact?: int,
   *                  priority?: int, time_to_resolve?: int, usersidlastupdater?: \App\Models\User,
   *                  usersidrecipient?: \App\Models\User, requester?: array<\App\Models\User>,
   *                  requestergroup?: array<\App\Models\Group>, watcher?: array<\App\Models\User>,
   *                  watchergroup?: array<\App\Models\Group>, technician?: array<\App\Models\User>,
   *                  techniciangroup?: array<\App\Models\Group>} $data
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
