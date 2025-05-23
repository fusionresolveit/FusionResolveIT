<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Entity
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'completename' => $translator->translate('Complete name'),
      'id' => $translator->translate('ID'),
      'entity' => $translator->translatePlural('Entity', 'Entities', 1),
      'comment' => $translator->translate('Comments'),
      'address' => $translator->translate('Address'),
      'website' => $translator->translate('Website'),
      'phonenumber' => $translator->translatePlural('Phone', 'Phones', 1),
      'email' => $translator->translatePlural('Email', 'Emails', 1),
      'fax' => $translator->translate('Fax'),
      'postcode' => $translator->translate('Postal code'),
      'town' => $translator->translate('City'),
      'state' => $translator->translate('State'),
      'country' => $translator->translate('Country'),
      'latitude' => $translator->translate('Latitude'),
      'longitude' => $translator->translate('Longitude'),
      'altitude' => $translator->translate('Altitude'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(14, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(1, $t['completename'], 'input', 'completename', fillable: false, readonly: true));
    $defColl->add(new Def(2, $t['id'], 'input', 'id', display: false));
    $defColl->add(new Def(
      80,
      $t['entity'],
      'dropdown_remote',
      'entity',
      dbname: 'entity_id',
      itemtype: '\App\Models\Entity',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(3, $t['address'], 'textarea', 'address', fillable: true));
    $defColl->add(new Def(4, $t['website'], 'input', 'website', fillable: true));
    $defColl->add(new Def(5, $t['phonenumber'], 'input', 'phonenumber', fillable: true));
    $defColl->add(new Def(6, $t['email'], 'input', 'email', fillable: true));
    $defColl->add(new Def(10, $t['fax'], 'input', 'fax', fillable: true));
    $defColl->add(new Def(25, $t['postcode'], 'input', 'postcode', fillable: true));
    $defColl->add(new Def(11, $t['town'], 'input', 'town', fillable: true));
    $defColl->add(new Def(12, $t['state'], 'input', 'state', fillable: true));
    $defColl->add(new Def(13, $t['country'], 'input', 'country', fillable: true));
    $defColl->add(new Def(67, $t['latitude'], 'input', 'latitude', fillable: true));
    $defColl->add(new Def(68, $t['longitude'], 'input', 'longitude', fillable: true));
    $defColl->add(new Def(69, $t['altitude'], 'input', 'altitude', fillable: true));

    return $defColl;

    /*
    $tab[] = [
      'id'                 => 'common',
      'name'               => __('Characteristics')
    ];

    $tab[] = [
      'id'                 => '1',
      'table'              => $this->getTable(),
      'field'              => 'completename',
      'name'               => __('Complete name'),
      'datatype'           => 'itemlink',
      'massiveaction'      => false
    ];

    $tab[] = [
      'id'                 => '2',
      'table'              => $this->getTable(),
      'field'              => 'id',
      'name'               => __('ID'),
      'massiveaction'      => false,
      'datatype'           => 'number'
    ];



    $tab[] = [
      'id'                 => '122',
      'table'              => $this->getTable(),
      'field'              => 'updated_at',
      'name'               => __('Last update'),
      'datatype'           => 'datetime',
      'massiveaction'      => false
    ];

    $tab[] = [
      'id'                 => '121',
      'table'              => $this->getTable(),
      'field'              => 'created_at',
      'name'               => __('Creation date'),
      'datatype'           => 'datetime',
      'massiveaction'      => false
    ];

    // add objectlock search options
    $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

    $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

    $tab[] = [
      'id'                 => 'advanced',
      'name'               => __('Advanced information')
    ];

    $tab[] = [
      'id'                 => '7',
      'table'              => $this->getTable(),
      'field'              => 'ldap_dn',
      'name'               => __('LDAP directory information attribute representing the entity'),
      'massiveaction'      => false,
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '8',
      'table'              => $this->getTable(),
      'field'              => 'tag',
      'name'               => __('Information in inventory tool (TAG) representing the entity'),
      'massiveaction'      => false,
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '9',
      'table'              => 'glpi_authldaps',
      'field'              => 'name',
      'name'               => __('LDAP directory of an entity'),
      'massiveaction'      => false,
      'datatype'           => 'dropdown'
    ];

    $tab[] = [
      'id'                 => '17',
      'table'              => $this->getTable(),
      'field'              => 'entity_ldapfilter',
      'name'               => __('Search filter (if needed)'),
      'massiveaction'      => false,
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '20',
      'table'              => $this->getTable(),
      'field'              => 'mail_domain',
      'name'               => __('Mail domain'),
      'massiveaction'      => false,
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => 'notif',
      'name'               => __('Notification options')
    ];

    $tab[] = [
      'id'                 => '60',
      'table'              => $this->getTable(),
      'field'              => 'delay_send_emails',
      'name'               => __('Delay to send email notifications'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'number',
      'min'                => 0,
      'max'                => 60,
      'step'               => 1,
      'unit'               => 'minute',
      'toadd'              => [self::CONFIG_PARENT => __('Inheritance of the parent entity')]
    ];

    $tab[] = [
      'id'                 => '61',
      'table'              => $this->getTable(),
      'field'              => 'is_notif_enable_default',
      'name'               => __('Enable notifications by default'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'string'
    ];

    $tab[] = [
      'id'                 => '18',
      'table'              => $this->getTable(),
      'field'              => 'admin_email',
      'name'               => __('Administrator email'),
      'massiveaction'      => false,
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '19',
      'table'              => $this->getTable(),
      'field'              => 'admin_reply',
      'name'               => __('Administrator reply-to email (if needed)'),
      'massiveaction'      => false,
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '21',
      'table'              => $this->getTable(),
      'field'              => 'notification_subject_tag',
      'name'               => __('Prefix for notifications'),
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '22',
      'table'              => $this->getTable(),
      'field'              => 'admin_email_name',
      'name'               => __('Administrator name'),
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '23',
      'table'              => $this->getTable(),
      'field'              => 'admin_reply_name',
      'name'               => __('Response address (if needed)'),
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '24',
      'table'              => $this->getTable(),
      'field'              => 'mailing_signature',
      'name'               => __('Email signature'),
      'datatype'           => 'text'
    ];

    $tab[] = [
      'id'                 => '26',
      'table'              => $this->getTable(),
      'field'              => 'cartridges_alert_repeat',
      'name'               => __('Alarms on cartridges'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '27',
      'table'              => $this->getTable(),
      'field'              => 'consumables_alert_repeat',
      'name'               => __('Alarms on consumables'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '29',
      'table'              => $this->getTable(),
      'field'              => 'use_licenses_alert',
      'name'               => __('Alarms on expired licenses'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '53',
      'table'              => $this->getTable(),
      'field'              => 'send_licenses_alert_before_delay',
      'name'               => __('Send license alarms before'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '30',
      'table'              => $this->getTable(),
      'field'              => 'use_contracts_alert',
      'name'               => __('Alarms on contracts'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '54',
      'table'              => $this->getTable(),
      'field'              => 'send_contracts_alert_before_delay',
      'name'               => __('Send contract alarms before'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '31',
      'table'              => $this->getTable(),
      'field'              => 'use_infocoms_alert',
      'name'               => __('Alarms on financial and administrative information'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '55',
      'table'              => $this->getTable(),
      'field'              => 'send_infocoms_alert_before_delay',
      'name'               => __('Send financial and administrative information alarms before'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '32',
      'table'              => $this->getTable(),
      'field'              => 'use_reservations_alert',
      'name'               => __('Alerts on reservations'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '48',
      'table'              => $this->getTable(),
      'field'              => 'default_contract_alert',
      'name'               => __('Default value for alarms on contracts'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '49',
      'table'              => $this->getTable(),
      'field'              => 'default_infocom_alert',
      'name'               => __('Default value for alarms on financial and administrative information'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '50',
      'table'              => $this->getTable(),
      'field'              => 'default_cartridges_alarm_threshold',
      'name'               => __('Default threshold for cartridges count'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'number'
    ];

    $tab[] = [
      'id'                 => '52',
      'table'              => $this->getTable(),
      'field'              => 'default_consumables_alarm_threshold',
      'name'               => __('Default threshold for consumables count'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'number'
    ];

    $tab[] = [
      'id'                 => '57',
      'table'              => $this->getTable(),
      'field'              => 'use_certificates_alert',
      'name'               => __('Alarms on expired certificates'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '58',
      'table'              => $this->getTable(),
      'field'              => 'send_certificates_alert_before_delay',
      'name'               => __('Send Certificate alarms before'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => 'helpdesk',
      'name'               => __('Assistance')
    ];

    $tab[] = [
      'id'                 => '47',
      'table'              => $this->getTable(),
      'field'              => 'tickettemplates_id', // not a dropdown because of special value
      'name'               => _n('Ticket template', 'Ticket templates', 1),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '33',
      'table'              => $this->getTable(),
      'field'              => 'autoclose_delay',
      'name'               => __('Automatic closing of solved tickets after'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'number',
      'min'                => 1,
      'max'                => 99,
      'step'               => 1,
      'unit'               => 'day',
      'toadd'              => [
      self::CONFIG_PARENT  => __('Inheritance of the parent entity'),
      self::CONFIG_NEVER   => __('Never'),
      0                  => __('Immediatly')
      ]
    ];

    $tab[] = [
      'id'                 => '59',
      'table'              => $this->getTable(),
      'field'              => 'autopurge_delay',
      'name'               => __('Automatic purge of closed tickets after'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'number',
      'min'                => 1,
      'max'                => 3650,
      'step'               => 1,
      'unit'               => 'day',
      'toadd'              => [
      self::CONFIG_PARENT  => __('Inheritance of the parent entity'),
      self::CONFIG_NEVER   => __('Never'),
      0                  => __('Immediatly')
      ]
    ];

    $tab[] = [
      'id'                 => '34',
      'table'              => $this->getTable(),
      'field'              => 'notclosed_delay',
      'name'               => __('Alerts on tickets which are not solved'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '35',
      'table'              => $this->getTable(),
      'field'              => 'auto_assign_mode',
      'name'               => __('Automatic assignment of tickets'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '36',
      'table'              => $this->getTable(),
      'field'              => 'calendars_id',// not a dropdown because of special valu
      'name'               => _n('Calendar', 'Calendars', 1),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '37',
      'table'              => $this->getTable(),
      'field'              => 'tickettype',
      'name'               => __('Tickets default type'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => 'assets',
      'name'               => __('Assets')
    ];

    $tab[] = [
      'id'                 => '38',
      'table'              => $this->getTable(),
      'field'              => 'autofill_buy_date',
      'name'               => __('Date of purchase'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '39',
      'table'              => $this->getTable(),
      'field'              => 'autofill_order_date',
      'name'               => __('Order date'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '40',
      'table'              => $this->getTable(),
      'field'              => 'autofill_delivery_date',
      'name'               => __('Delivery date'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '41',
      'table'              => $this->getTable(),
      'field'              => 'autofill_use_date',
      'name'               => __('Startup date'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '42',
      'table'              => $this->getTable(),
      'field'              => 'autofill_warranty_date',
      'name'               => __('Start date of warranty'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '43',
      'table'              => $this->getTable(),
      'field'              => 'inquest_config',
      'name'               => __('Satisfaction survey configuration'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '44',
      'table'              => $this->getTable(),
      'field'              => 'inquest_rate',
      'name'               => __('Satisfaction survey trigger rate'),
      'massiveaction'      => false,
      'datatype'           => 'number'
    ];

    $tab[] = [
      'id'                 => '45',
      'table'              => $this->getTable(),
      'field'              => 'inquest_delay',
      'name'               => __('Create survey after'),
      'massiveaction'      => false,
      'datatype'           => 'number'
    ];

    $tab[] = [
      'id'                 => '46',
      'table'              => $this->getTable(),
      'field'              => 'inquest_URL',
      'name'               => __('URL'),
      'massiveaction'      => false,
      'datatype'           => 'string',
      'autocomplete'       => true,
    ];

    $tab[] = [
      'id'                 => '51',
      'table'              => $this->getTable(),
      'field'              => 'name',
      'linkfield'          => 'entities_id_software', // not a dropdown because of special value
      //TRANS: software in plural
      'name'               => __('Entity for software creation'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];

    $tab[] = [
      'id'                 => '56',
      'table'              => $this->getTable(),
      'field'              => 'autofill_decommission_date',
      'name'               => __('Decommission date'),
      'massiveaction'      => false,
      'nosearch'           => true,
      'datatype'           => 'specific'
    ];
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getDefinitionAddress(): array
  {
    global $translator;
    return [
      [
        'id'    => 1,
        'title' => $translator->translatePlural('Phone', 'Phones', 1),
        'type'  => 'input',
        'name'  => 'phonenumber',
      ],
      [
        'id'    => 2,
        'title' => $translator->translate('Fax'),
        'type'  => 'input',
        'name'  => 'fax',
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Website'),
        'type'  => 'input',
        'name'  => 'website',
      ],
      [
        'id'    => 4,
        'title' => $translator->translatePlural('Email', 'Emails', 1),
        'type'  => 'input',
        'name'  => 'email',
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Address'),
        'type'  => 'textarea',
        'name'  => 'address',
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('Postal code'),
        'type'  => 'input',
        'name'  => 'postcode',
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('City'),
        'type'  => 'input',
        'name'  => 'town',
      ],
      [
        'id'    => 8,
        'title' => $translator->translate('location' . "\004" . 'State'),
        'type'  => 'input',
        'name'  => 'state',
      ],
      [
        'id'    => 9,
        'title' => $translator->translate('Country'),
        'type'  => 'input',
        'name'  => 'country',
      ],
      [
        'id'    => 10,
        'title' => $translator->translate('Longitude'),
        'type'  => 'input',
        'name'  => 'longitude',
      ],
      [
        'id'    => 11,
        'title' => $translator->translate('Latitude'),
        'type'  => 'input',
        'name'  => 'latitude',
      ],
      [
        'id'    => 12,
        'title' => $translator->translate('Altitude'),
        'type'  => 'input',
        'name'  => 'altitude',
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Entity', 'Entities', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Entity', 'Entities', 2),
        'icon' => 'layer group',
        'link' => $rootUrl . '/entities',
      ],
      [
        'title' => $translator->translate('Address'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/address',
      ],
      [
        'title' => $translator->translate('Advanced information'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Notification', 'Notifications', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Assistance'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Assets'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('UI customization'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('User', 'Users', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/users',
      ],
      [
        'title' => $translator->translatePlural('Rule', 'Rules', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translate('Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowledgebasearticles',
      ],
      [
        'title' => $translator->translate('FusionInventory'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
