<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules\Criteria;

class Ticket
{
  /**
   * @return array<mixed>
   */
  public static function get(): array
  {
    return [
    //   'name' => [
    //     'title'   => pgettext('global', 'Title'),
    //     'dbname'  => 'name',
    //     'type'    => 'string',
    //     'execute' => 'standard',
    //   ],
    //   'content' => [
    //     'title'   => pgettext('global', 'Description'),
    //     'dbname'  => 'content',
    //     'type'    => 'string',
    //     'execute' => 'standard',
    //   ],

    //   // 'date_mod' => [
    //   //   // 'table'     => 'glpi_tickets',
    //   //   'model'     => '\App\Models\Ticket',
    //   //   'field'     => 'date_mod',
    //   //   'name'      => pgettext('global', 'Last update'),
    //   //   'linkfield' => 'date_mod',
    //   // ],

    //   'itilcategories_id' => [
    //     'title'           => npgettext('global', 'Category', 'Categories', 1) . ' - ' . pgettext('global', 'Name'),
    //     'dbname'          => 'category_id',
    //     'model'           => '\App\Models\Category',
    //     'relationdbname'  => 'name',
    //     'type'            => 'dropdown_remote',
    //     'execute'         => 'standard',
    //   ],
    //   'itilcategories_id_cn' => [
    //     'title'           => npgettext('global', 'Category', 'Categories', 1) . ' - ' .
    //                          pgettext('global', 'Complete name'),
    //     'dbname'          => 'category_id',
    //     'model'           => '\App\Models\Category',
    //     'relationdbname'  => 'completename',
    //     'type'            => 'dropdown_remote',
    //     'execute'         => 'standard',
    //   ],
    //   'itilcategories_id_code' => [
    //     'title'           => pgettext('category', 'Code representing the ticket category'),
    //     'dbname'          => 'category_id',
    //     'model'           => '\App\Models\Category',
    //     'relationdbname'  => 'code',
    //     'type'            => 'dropdown_remote',
    //     'execute'         => 'standard',
    //   ],

    //   // 'type' => [
    //   //   // 'table'     => 'glpi_tickets',
    //   //   'model'     => '\App\Models\Ticket',
    //   //   'field'     => 'type',
    //   //   'name'      =>  npgettext('global', 'Type', 'Types', 1),
    //   //   'linkfield' => 'type',
    //   //   'type'      => 'dropdown_tickettype',
    //   // ],
    //   // '_users_id_requester' => [
    //   //   // 'table'     => 'glpi_users',
    //   //   'model'     => '\App\Models\User',
    //   //   'field'     => 'name',
    //   //   'name'      => npgettext('ITIL', 'Requester', 'Requesters', 1),
    //   //   'linkfield' => '_users_id_requester',
    //   //   'type'      => 'dropdown_users',
    //   //   'linked_criteria' => '_groups_id_of_requester',
    //   // ],
    //   // '_groups_id_of_requester' => [
    //   //   // 'table'     => 'glpi_groups',
    //   //   'model'     => '\App\Models\Group',
    //   //   'field'     => 'completename',
    //   //   'name'      => 'Requester in group',
    //   //   'linkfield' => '_groups_id_of_requester',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // '_locations_id_of_requester' => [
    //   //   // 'table'     => 'glpi_locations',
    //   //   'model'     => '\App\Models\Location',
    //   //   'field'     => 'completename',
    //   //   'name'      => 'Requester location',
    //   //   'linkfield' => '_locations_id_of_requester',
    //   //   'type'      => 'dropdown',
    //   // ],

    //   '_locations_id_of_item' => [
    //     'title'           => 'Item location',
    //     'dbname'          => null,
    //     'model'           => '\App\Models\Location',
    //     'relationdbname'  => 'completename',
    //     'type'            => 'dropdown',
    //     'execute'         => 'locationOfItem',
    //   ],


    //   // '_groups_id_of_item' => [
    //   //   // 'table'     => 'glpi_groups',
    //   //   'model'     => '\App\Models\Group',
    //   //   'field'     => 'completename',
    //   //   'name'      => 'Item group',
    //   //   'linkfield' => '_groups_id_of_item',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // '_states_id_of_item' => [
    //   //   // 'table'     => 'glpi_states',
    //   //   'model'     => '\App\Models\State',
    //   //   'field'     => 'completename',
    //   //   'name'      => 'Item state',
    //   //   'linkfield' => '_states_id_of_item',
    //   //   'type'      => 'dropdown',
    //   // ],


    //   'locations_id' => [
    //     'title'           => 'Ticket location',
    //     'dbname'          => 'locations_id',
    //     'model'           => '\App\Models\Location',
    //     'relationdbname'  => 'completename',
    //     'type'            => 'dropdown',
    //     'execute'         => 'standard',
    //   ],



    //   // '_groups_id_requester' => [
    //   //   // 'table'     => 'glpi_groups',
    //   //   'model'     => '\App\Models\Group',
    //   //   'field'     => 'completename',
    //   //   'name'      => npgettext('ITIL', 'Requester group', 'Requester groups', 1),
    //   //   'linkfield' => '_groups_id_requester',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // '_users_id_assign' => [
    //   //   // 'table'     => 'glpi_users',
    //   //   'model'     => '\App\Models\User',
    //   //   'field'     => 'name',
    //   //   'name'      => pgettext('ITIL', 'Technician'),
    //   //   'linkfield' => '_users_id_assign',
    //   //   'type'      => 'dropdown_users',
    //   // ],
    //   // '_groups_id_assign' => [
    //   //   // 'table'     => 'glpi_groups',
    //   //   'model'     => '\App\Models\Group',
    //   //   'field'     => 'completename',
    //   //   'name'      => pgettext('ITIL', 'Technician group'),
    //   //   'linkfield' => '_groups_id_assign',
    //   //   'type'      => 'dropdown',
    //   //   'condition' => ['is_assign' => 1],
    //   // ],
    //   // '_suppliers_id_assign' => [
    //   //   // 'table'     => 'glpi_suppliers',
    //   //   'model'     => '\App\Models\Supplier',
    //   //   'field'     => 'name',
    //   //   'name'      => 'Assigned to a supplier',
    //   //   'linkfield' => '_suppliers_id_assign',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // '_users_id_observer' => [
    //   //   // 'table'     => 'glpi_users',
    //   //   'model'     => '\App\Models\User',
    //   //   'field'     => 'name',
    //   //   'name'      => npgettext('ITIL', 'Watcher', 'Watchers', 1),
    //   //   'linkfield' => '_users_id_observer',
    //   //   'type'      => 'dropdown_users',
    //   // ],
    //   // '_groups_id_observer' => [
    //   //   // 'table'     => 'glpi_groups',
    //   //   'model'     => '\App\Models\Group',
    //   //   'field'     => 'completename',
    //   //   'name'      => npgettext('ITIL', 'Watcher group', 'Watcher groups', 1),
    //   //   'linkfield' => '_groups_id_observer',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // 'requesttypes_id' => [
    //   //   // 'table'     => 'glpi_requesttypes',
    //   //   'model'     => '\App\Models\Requesttype',
    //   //   'field'     => 'name',
    //   //   'name'      => npgettext('global', 'Request source', 'Request sources', 1),
    //   //   'linkfield' => 'requesttypes_id',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // 'itemtype' => [
    //   //   // 'table'     => 'glpi_tickets',
    //   //   'model'     => '\App\Models\Ticket',
    //   //   'field'     => 'itemtype',
    //   //   'name'      => 'Item type',
    //   //   'linkfield' => 'itemtype',
    //   //   'type'      => 'dropdown_tracking_itemtype',
    //   // ],
    //   // 'entities_id' => [
    //   //   // 'table'     => 'glpi_entities',
    //   //   'model'     => '\App\Models\Entity',
    //   //   'field'     => 'name',
    //   //   'name'      => npgettext('global', 'Entity', 'Entities', 1),
    //   //   'linkfield' => 'entities_id',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // 'profiles_id' => [
    //   //   // 'table'     => 'glpi_profiles',
    //   //   'model'     => '\App\Models\Profile',
    //   //   'field'     => 'name',
    //   //   'name'      => pgettext('profile', 'Default profile'),
    //   //   'linkfield' => 'profiles_id',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // 'urgency' => [
    //   //   'name'      => pgettext('ITIL', 'Urgency'),
    //   //   'type'      => 'dropdown_urgency',
    //   // ],
    //   // 'impact' => [
    //   //   'name'      => pgettext('ITIL', 'Impact'),
    //   //   'type'      => 'dropdown_impact',
    //   // ],
    //   // 'priority' => [
    //   //   'name'      => pgettext('ITIL', 'Priority'),
    //   //   'type'      => 'dropdown_priority',
    //   // ],
    //   // 'status' => [
    //   //   'table'     => '',
    //   //   'field'     => '',
    //   //   'name'      => pgettext('global', 'Status'),
    //   //   'type'      => 'dropdown_status',
    //   // ],
    //   // '_mailgate' => [
    //   //   // 'table'     => 'glpi_mailcollectors',
    //   //   'model'     => '\App\Models\Mailcollector',
    //   //   'field'     => 'name',
    //   //   'name'      => 'Mails receiver',
    //   //   'linkfield' => '_mailgate',
    //   //   'type'      => 'dropdown',
    //   // ],
    //   // '_x-priority' => [
    //   //   'table'     => '',
    //   //   'name'      => 'X-Priority email header',
    //   //   'type'      => 'text',
    //   // ],
    //   // 'slas_id_ttr' => [
    //   //   // 'table'     => 'glpi_slas',
    //   //   'model'     => '\App\Models\Sla',
    //   //   'field'     => 'name',
    //   //   'name'      => npgettext('ITIL', 'SLA', 'SLAs', 1) . ' ' . pgettext('ITIL', 'Time to resolve'),
    //   //   'linkfield' => 'slas_id_ttr',
    //   //   'type'      => 'dropdown',
    //   //   // 'condition' => ['glpi_slas.type' => SLM::TTR],
    //   // ],
    //   // 'slas_id_tto' => [
    //   //   // 'table'     => 'glpi_slas',
    //   //   'model'     => '\App\Models\Sla',
    //   //   'field'     => 'name',
    //   //   'name'      => npgettext('ITIL', 'SLA', 'SLAs', 1) . ' ' . 'Time to own',
    //   //   'linkfield' => 'slas_id_tto',
    //   //   'type'      => 'dropdown',
    //   //   // 'condition' => ['glpi_slas.type' => SLM::TTO],
    //   // ],
    //   // 'olas_id_ttr' => [
    //   //   // 'table'     => 'glpi_olas',
    //   //   'model'     => '\App\Models\Ola',
    //   //   'field'     => 'name',
    //   //   'name'      => npgettext('ITIL', 'OLA', 'OLAs', 1) . ' ' . pgettext('ITIL', 'Time to resolve'),
    //   //   'linkfield' => 'olas_id_ttr',
    //   //   'type'      => 'dropdown',
    //   //   // 'condition' => ['glpi_olas.type' => SLM::TTR],
    //   // ],
    //   // 'olas_id_tto' => [
    //   //   // 'table'     => 'glpi_olas',
    //   //   'model'     => '\App\Models\Ola',
    //   //   'field'     => 'name',
    //   //   'name'      => npgettext('ITIL', 'OLA', 'OLAs', 1) . ' ' . 'Time to own',
    //   //   'linkfield' => 'olas_id_tto',
    //   //   'type'      => 'dropdown',
    //   //   // 'condition' => ['glpi_olas.type' => SLM::TTO],
    //   // ],
    //   // '_date_creation_calendars_id' => [
    //   //   // 'table'     => 'calendars',
    //   //   'model'     => '\App\Models\Calendar',
    //   //   'field'     => 'name',
    //   //   'name'      => 'Creation date is a working hour in calendar',
    //   //   'linkfield' => '_date_creation_calendars_id',
    //   //   'type'      => 'dropdown',
    //   // ],
    ];
  }
}
