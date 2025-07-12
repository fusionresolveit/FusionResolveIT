<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Ticket extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Knowledgebasearticles;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Ticket::class;
  protected $icon = 'hands helping';
  /** @var string[] */
  protected $cascadeDeletes = [
    'knowledgebasearticles',
    'requester',
    'requestergroup',
    'watcher',
    'watchergroup',
    'technician',
    'techniciangroup',
    'problems',
    'changes',
    'linkedtickets',
    'followups',
    'solutions',
    'costs',
    'projecttasks',
    'approvals',
    'itemComputers',
    'itemMonitors',
    'itemNetworkequipments',
    'itemPeripherals',
    'itemPhones',
    'itemPrinters',
    'itemSoftwares',
    'itemSoftwarelicenses',
    'itemCertificates',
    'itemLines',
    'itemDcrooms',
    'itemRacks',
    'itemEnclosures',
    'itemClusters',
    'itemPdus',
    'itemDomains',
    'itemDomainrecords',
    'itemAppliances',
    'itemPassivedcequipments',
    'projects',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'requester',
    'requestergroup',
    'watcher',
    'watchergroup',
    'technician',
    'techniciangroup',
    'usersidlastupdater',
    'usersidrecipient',
    'category',
    'entity',
    'problems',
    'changes',
    'linkedtickets',
    'knowledgebasearticles',
    'followups',
    'costs',
    'approvals',
  ];

  protected $with = [
    'requester:id,name,firstname,lastname',
    'requestergroup:id,name,completename',
    'watcher:id,name,firstname,lastname',
    'watchergroup:id,name,completename',
    'technician:id,name,firstname,lastname',
    'techniciangroup:id,name,completename',
    'usersidlastupdater:id,name,firstname,lastname',
    'usersidrecipient:id,name,firstname,lastname',
    'category:id,name',
    'location:id,name',
    'problems:id,name',
    'changes:id,name',
    'linkedtickets:id,name',
    'knowledgebasearticles:id,name',
    'entity:id,name,completename,address,country,email,fax,phonenumber,postcode,state,town,website',
    'followups:id,content',
    'solutions',
    'costs:id,name,ticket_id,begin_date,end_date,actiontime,cost_time,cost_fixed,cost_material,budget_id,entity_id',
    'approvals',
  ];

  // For default values
  protected $attributes = [
    'status'    => 1,
    'type'      => 1,
    'urgency'   => 3,
    'impact'    => 3,
    'priority'  => 3,
    'entity_id' => 1,
  ];

  protected $fillable = [
    'name',
    'entity_id',
    'status',
    'type',
    'user_id_recipient',
    'requesttype_id',
    'content',
    'urgency',
    'impact',
    'priority',
    'category_id',
    'type',
    'location_id',
  ];

  protected $casts = [
    'user_id_recipient'   => 'integer',
    'user_id_lastupdater' => 'integer',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::creating(function (\App\Models\Ticket $model): void
    {
      $model->user_id_recipient = $GLOBALS['user_id'];
      $model->user_id_lastupdater = $GLOBALS['user_id'];

      $session = new \SlimSession\Helper();
      if (isset($session['ticketCreationDate']))
      {
        $model->created_at = $session['ticketCreationDate'];
      }
    });

    static::updating(function ($model): void
    {
      // Clean new lines before passing to rules
      if (property_exists($model, 'content'))
      {
        $model->content = preg_replace('/\\\\r\\\\n/', "\n", $model->content);
        $model->content = preg_replace('/\\\\n/', "\n", $model->content);
      }
      $model->user_id_lastupdater = $GLOBALS['user_id'];


     // TODO finish
    });
  }

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('ticket', 'Ticket', 'Tickets', $nb);
  }

  /** @return BelongsToMany<\App\Models\User, $this> */
  public function requester(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\User::class)->wherePivot('type', 1);
  }

  /** @return BelongsToMany<\App\Models\Group, $this> */
  public function requestergroup(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Group::class)->wherePivot('type', 1);
  }

  /** @return BelongsToMany<\App\Models\User, $this> */
  public function watcher(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\User::class)->wherePivot('type', 3);
  }

  /** @return BelongsToMany<\App\Models\Group, $this> */
  public function watchergroup(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Group::class)->wherePivot('type', 3);
  }

  /** @return BelongsToMany<\App\Models\User, $this> */
  public function technician(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\User::class)->wherePivot('type', 2);
  }

  /** @return BelongsToMany<\App\Models\Group, $this> */
  public function techniciangroup(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Group::class)->wherePivot('type', 2);
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usersidlastupdater(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_lastupdater');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usersidrecipient(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_recipient');
  }

  /** @return BelongsTo<\App\Models\Category, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Category::class);
  }

  /** @return BelongsToMany<\App\Models\Problem, $this> */
  public function problems(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Problem::class, 'problem_ticket', 'ticket_id', 'problem_id');
  }

  /** @return BelongsToMany<\App\Models\Change, $this> */
  public function changes(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Change::class, 'change_ticket', 'ticket_id', 'change_id');
  }

  /** @return BelongsToMany<\App\Models\Ticket, $this> */
  public function linkedtickets(): BelongsToMany
  {
    return $this->belongsToMany(
      \App\Models\Ticket::class,
      'ticket_ticket',
      'ticket_id_1',
      'ticket_id_2'
    )->withPivot('link');
  }

  /** @return MorphMany<\App\Models\Followup, $this> */
  public function followups(): MorphMany
  {
    return $this->morphMany(\App\Models\Followup::class, 'item');
  }

  /** @return MorphMany<\App\Models\Solution, $this> */
  public function solutions(): MorphMany
  {
    return $this->morphMany(\App\Models\Solution::class, 'item');
  }

  /**
   * @return array<mixed>
   */
  public function getFeeds(int $id): array
  {
    $feeds = [];

    /** @var \App\Models\Ticket|null */
    $ticket = \App\Models\Ticket::where('id', $id)->first();
    if (
        is_null($this->definition) ||
        !class_exists($this->definition) ||
        is_null($ticket)
    )
    {
      return [];
    }
    $statesDef = $this->definition::getStatusArray();

    $ctrl = new \App\v1\Controllers\Followup();
    // Get followups
    if (!$ctrl->canRightReadPrivateItem())
    {
      $followups = \App\Models\Followup::
          where('item_type', 'App\Models\Ticket')
        ->where('item_id', $id)
        ->where('is_private', false)
        ->get();
    }
    else
    {
      $followups = \App\Models\Followup::where('item_type', 'App\Models\Ticket')->where('item_id', $id)->get();
    }

    foreach ($followups as $followup)
    {
      $usertype = 'user';
      if ($followup->is_tech)
      {
        $usertype = 'tech';
      }
      $username = '';
      if (!is_null($followup->user))
      {
        $username = $followup->user->completename;
      }

      $feeds[] = [
        "user"     => $username,
        "usertype" => $usertype,
        "type"     => "followup",
        "date"     => $followup->created_at,
        "summary"  => pgettext('ticket feed', 'added a followup'),
        "content"  => \App\v1\Controllers\Toolbox::convertMarkdownToHtml($followup['content']),
        "time"     => null,
      ];
    }

    // Get solutions
    $solutions = \App\Models\Solution::where('item_type', 'App\Models\Ticket')->where('item_id', $id)->get();
    foreach ($solutions as $solution)
    {
      $usertype = 'tech';
      $username = '';
      if (!is_null($solution->user))
      {
        $username = $solution->user->completename;
      }

      $canValidate = false;
      if ($solution->status == 2)
      {
        // waiting mode
        if ($ticket->user_id_recipient == $GLOBALS['user_id'])
        {
          $canValidate = true;
        }
        foreach ($ticket->requester as $user)
        {
          if ($user->id == $GLOBALS['user_id'])
          {
            $canValidate = true;
          }
        }
      }
      $usernameValidator = '';
      if ($solution->status >= 3)
      {
        $usernameValidator = ' by ' . $solution->user_name_approval;
      }

      $feeds[] = [
        "id"       => $solution->id,
        "user"     => $username,
        "usertype" => $usertype,
        "type"     => "solution",
        "date"     => $solution->created_at,
        "summary"  => pgettext('ticket feed', 'added a solution'),
        "content"  => \App\v1\Controllers\Toolbox::convertMarkdownToHtml($solution['content']),
        "time"     => null,
        "status"   => $solution->status,
        "statusname"   => $solution->statusname . $usernameValidator,
        "canValidate" => $canValidate,
      ];
    }

    // Get important events in logs :
    $logs = \App\Models\Log::
      where('item_type', 'App\Models\Ticket')
      ->where('item_id', $id)
      ->whereIn('id_search_option', [12, 5, 8])
      ->get();
    foreach ($logs as $log)
    {
      if (is_null($log->user_name))
      {
        continue;
      }
      if ($log->id_search_option == 12)
      {
        $userActionSpl = explode(" (", $log->user_name);
        $stateDef = $statesDef[$log->new_value];
        $feeds[] = [
          "user"     => $userActionSpl[0],
          "usertype" => "tech",
          "type"     => "event",
          "date"     => $log->updated_at,
          "summary"  => pgettext('ticket feed', 'changed state to') .
                        " <span class=\"ui " . $stateDef['color'] .
                        " text\"><i class=\"" . $stateDef['icon'] . " icon\"></i>" . $stateDef['title'] . "</span>",
          "content"  => "",
          "time"     => null,
        ];
      }
      elseif ($log->id_search_option == 5)
      {
        $userActionSpl = explode(" (", $log->user_name);
        if (is_null($log->new_value))
        {
          continue;
        }
        $userSpl = explode(" (", $log->new_value);

        $feeds[] = [
          "user"     => $userActionSpl[0],
          "usertype" => "tech",
          "type"     => "event",
          "date"     => $log->updated_at,
          "summary"  => pgettext('ticket feed', 'add attribution to the user') . ' ' . $userSpl[0],
          "content"  => "",
          "time"     => null,
        ];
      }
      elseif ($log->id_search_option == 8)
      {
        $userActionSpl = explode(" (", $log->user_name);
        if (!is_null($log->new_value))
        {
          $groupSpl = explode(" (", $log->new_value);
          $feeds[] = [
            "user"     => $userActionSpl[0],
            "usertype" => "tech",
            "type"     => "event",
            "date"     => $log->updated_at,
            "summary"  => pgettext('ticket feed', 'add (+) attribution to the group') . ' ' . $groupSpl[0],
            "content"  => "",
            "time"     => null,
          ];
        }
        else
        {
          if (is_null($log->old_value))
          {
            continue;
          }
          $groupSpl = explode(" (", $log->old_value);
          $feeds[] = [
            "user"     => $userActionSpl[0],
            "usertype" => "tech",
            "type"     => "event",
            "date"     => $log->updated_at,
            "summary"  => pgettext('ticket feed', 'delete (-) attribution to the group') . ' ' .
                          $groupSpl[0],
            "content"  => "",
            "time"     => null,
          ];
        }
      }
    }

    // sort
    array_multisort(
      array_column($feeds, 'date'),
      SORT_DESC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $feeds
    );
    return $feeds;
  }

  /**
   * Get the color of the status of the ticket
   */
  public function getColor(): string
  {
    if (!is_null($this->definition) && class_exists($this->definition))
    {
      $statesDef = $this->definition::getStatusArray();
      return $statesDef[$this->attributes['status']]['color'];
    }
    return 'blue';
  }

  public function canOnlyReadItem(): bool
  {
    if ($this->attributes['status'] == 6)
    {
      return true;
    }
    return false;
  }

  /** @return HasMany<\App\Models\Ticketcost, $this> */
  public function costs(): HasMany
  {
    return $this->hasMany(\App\Models\Ticketcost::class, 'ticket_id');
  }

  /** @return BelongsToMany<\App\Models\Projecttask, $this> */
  public function projecttasks(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Projecttask::class);
  }

  /** @return HasMany<\App\Models\Ticketvalidation, $this> */
  public function approvals(): HasMany
  {
    return $this->hasMany(\App\Models\Ticketvalidation::class, 'ticket_id');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Monitor, $this> */
  public function itemMonitors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Monitor::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function itemPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Software, $this> */
  public function itemSoftwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Software::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Softwarelicense, $this> */
  public function itemSoftwarelicenses(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Softwarelicense::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Certificate, $this> */
  public function itemCertificates(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Certificate::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Line, $this> */
  public function itemLines(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Line::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Dcroom, $this> */
  public function itemDcrooms(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Dcroom::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Rack, $this> */
  public function itemRacks(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Rack::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Enclosure, $this> */
  public function itemEnclosures(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Enclosure::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Cluster, $this> */
  public function itemClusters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Cluster::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Pdu, $this> */
  public function itemPdus(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Pdu::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function itemDomains(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Domain::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Domainrecord, $this> */
  public function itemDomainrecords(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Domainrecord::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function itemAppliances(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Appliance::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Passivedcequipment, $this> */
  public function itemPassivedcequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Passivedcequipment::class, 'item', 'item_ticket');
  }

  /** @return MorphToMany<\App\Models\Project, $this> */
  public function projects(): MorphToMany
  {
    return $this->morphToMany(\App\Models\Project::class, 'item', 'itil_project');
  }
}
