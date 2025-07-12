<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules\Actions;

class Ticket
{
  /**
   * @return array<mixed>
   */
  public static function get(): array
  {
    return [
      'categories_id' => [
        'table'     => 'categories',
        'name'      => npgettext('global', 'Category', 'Categories', 1),
        'type'      => 'dropdown',
      ],
      '_affect_itilcategory_by_code' => [
        'name'      => pgettext('ticket', 'Ticket category from code'),
        'type'      => 'text',
        'force_actions' => ['regex_result'],
      ],
      'type' => [
        'table'     => 'glpi_tickets',
        'name'      =>  npgettext('global', 'Type', 'Types', 1),
        'type'      => 'dropdown_tickettype',
      ],
      '_users_id_requester' => [
        'name'               => npgettext('ITIL', 'Requester', 'Requesters', 1),
        'type'               => 'dropdown_users',
        'force_actions'      => ['assign', 'append'],
        'permitseveral'      => ['append'],
        'appendto'           => '_additional_requesters',
        'appendtoarray'      => ['use_notification' => 1],
        'appendtoarrayfield' => 'users_id',
      ],
      '_groups_id_requester' => [
        'table'         => 'glpi_groups',
        'name'          => npgettext('ITIL', 'Requester group', 'Requester groups', 1),
        'type'          => 'dropdown',
        'condition'     => ['is_requester' => 1],
        'force_actions' => ['assign', 'append', 'fromitem', 'defaultfromuser'],
        'permitseveral' => ['append'],
        'appendto'      => '_additional_groups_requesters',
      ],
      '_users_id_assign' => [
        'name'               => pgettext('ITIL', 'Technician'),
        'type'               => 'dropdown_assign',
        'force_actions'      => ['assign', 'append'],
        'permitseveral'      => ['append'],
        'appendto'           => '_additional_assigns',
        'appendtoarray'      => ['use_notification' => 1],
        'appendtoarrayfield' => 'users_id',
      ],
      '_groups_id_assign' => [
        'table'         => 'glpi_groups',
        'name'          => pgettext('ITIL', 'Technician group'),
        'type'          => 'dropdown',
        'condition'     => ['is_assign' => 1],
        'force_actions' => ['assign', 'append'],
        'permitseveral' => ['append'],
        'appendto'      => '_additional_groups_assigns',
      ],
      '_suppliers_id_assign' => [
        'table'              => 'glpi_suppliers',
        'name'               => pgettext('ticket', 'Assigned to a supplier'),
        'type'               => 'dropdown',
        'force_actions'      => ['assign', 'append'],
        'permitseveral'      => ['append'],
        'appendto'           => '_additional_suppliers_assigns',
        'appendtoarray'      => ['use_notification' => 1],
        'appendtoarrayfield' => 'suppliers_id',
      ],
      '_users_id_observer' => [
        'name'               => npgettext('ITIL', 'Watcher', 'Watchers', 1),
        'type'               => 'dropdown_users',
        'force_actions'      => ['assign', 'append'],
        'permitseveral'      => ['append'],
        'appendto'           => '_additional_observers',
        'appendtoarray'      => ['use_notification' => 1],
        'appendtoarrayfield' => 'users_id',
      ],
      '_groups_id_observer' => [
        'table'         => 'glpi_groups',
        'name'          => npgettext('ITIL', 'Watcher group', 'Watcher groups', 1),
        'type'          => 'dropdown',
        'condition'     => ['is_watcher' => 1],
        'force_actions' => ['assign', 'append'],
        'permitseveral' => ['append'],
        'appendto'      => '_additional_groups_observers',
      ],
      'urgency' => [
        'name'      => pgettext('ITIL', 'Urgency'),
        'type'      => 'dropdown_urgency',
      ],
      'impact' => [
        'name'      => pgettext('ITIL', 'Impact'),
        'type'      => 'dropdown_impact',
      ],
      'priority' => [
        'name'          => pgettext('ITIL', 'Priority'),
        'type'          => 'dropdown_priority',
        'force_actions' => ['assign', 'compute'],
      ],
      'status' => [
        'name'      => pgettext('global', 'Status'),
        'type'      => 'dropdown_status',
      ],
      'affectobject' => [
        'name'          => npgettext('global', 'Associated item', 'Associated items', 2),
        'type'          => 'text',
        'force_actions' => ['affectbyip', 'affectbyfqdn', 'affectbymac'],
      ],
      'slas_id_ttr' => [
        'table'     => 'glpi_slas',
        'field'     => 'name',
        'name'      => npgettext('ITIL', 'SLA', 'SLAs', 1) . ' ' . pgettext('ITIL', 'Time to resolve'),
        'linkfield' => 'slas_id_ttr',
        'type'      => 'dropdown',
        // 'condition' => ['glpi_slas.type' => SLM::TTR],
      ],
      'slas_id_tto' => [
        'table'     => 'glpi_slas',
        'field'     => 'name',
        'name'      => npgettext('ITIL', 'SLA', 'SLAs', 1) . ' ' . pgettext('ticket', 'Time to own'),
        'linkfield' => 'slas_id_tto',
        'type'      => 'dropdown',
        // 'condition' => ['glpi_slas.type' => SLM::TTO],
      ],
      'olas_id_ttr' => [
        'table'     => 'glpi_olas',
        'field'     => 'name',
        'name'      => npgettext('ITIL', 'OLA', 'OLAs', 1) . ' ' . pgettext('ITIL', 'Time to resolve'),
        'linkfield' => 'olas_id_ttr',
        'type'      => 'dropdown',
        // 'condition' => ['glpi_olas.type' => SLM::TTR],
      ],
      'olas_id_tto' => [
        'table'     => 'glpi_olas',
        'field'     => 'name',
        'name'      => npgettext('ITIL', 'OLA', 'OLAs', 1) . ' ' . pgettext('ticket', 'Time to own'),
        'linkfield' => 'olas_id_tto',
        'type'      => 'dropdown',
        // 'condition' => ['glpi_olas.type' => SLM::TTO],
      ],
      'users_id_validate' => [
        'name'          => pgettext('ticket', 'Send an approval request') . ' - ' .
                           npgettext('global', 'User', 'Users', 1),
        'type'          => 'dropdown_users_validate',
        'force_actions' => ['add_validation'],
      ],
      'responsible_id_validate' => [
        'name'          => pgettext('ticket', 'Send an approval request') . ' - ' .
                           pgettext('ticket', 'Responsible of the requester'),
        'type'          => 'yesno',
        'force_actions' => ['add_validation'],
      ],
      'groups_id_validate' => [
        'name'          => pgettext('ticket', 'Send an approval request') . ' - ' .
                           npgettext('global', 'Group', 'Groups', 1),
        'type'          => 'dropdown_groups_validate',
        'force_actions' => ['add_validation'],
      ],
      'validation_percent' => [
        'name'          => pgettext('ticket', 'Send an approval request') . ' - ' .
                           pgettext('ticket', 'Minimum validation required'),
        'type'          => 'dropdown_validation_percent',
      ],
      'users_id_validate_requester_supervisor' => [
        'name'          => pgettext('ticket', 'Approval request to requester group manager'),
        'type'          => 'yesno',
        'force_actions' => ['add_validation'],
      ],
      'users_id_validate_assign_supervisor' => [
        'name'          => pgettext('ticket', 'Approval request to technician group manager'),
        'type'          => 'yesno',
        'force_actions' => ['add_validation'],
      ],
      'locations_id' => [
        'table'         => 'glpi_locations',
        'name'          => npgettext('global', 'Location', 'Locations', 1),
        'type'          => 'dropdown',
        'force_actions' => ['assign', 'fromuser', 'fromitem'],
      ],
      'requesttypes_id' => [
        'table'     => 'glpi_requesttypes',
        'name'      => npgettext('global', 'Request source', 'Request sources', 1),
        'type'      => 'dropdown',
      ],
      'takeintoaccount_delay_stat' => [
        'name'          => pgettext('ticket', 'Take into account delay'),
        'type'          => 'yesno',
        'force_actions' => ['do_not_compute'],
      ],
      'solution_template' => [
        'table'         => 'glpi_solutiontemplates',
        'name'          => npgettext('global', 'Solution template', 'Solution templates', 1),
        'type'          => 'dropdown',
        'force_actions' => ['assign'],
      ],
    ];
  }
}
