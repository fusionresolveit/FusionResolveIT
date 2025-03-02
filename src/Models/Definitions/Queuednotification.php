<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Queuednotification
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Subject'),
      'itemtype' => $translator->translatePlural('Type', 'Types', 1),
      'notificationtemplate' => $translator->translatePlural('Notification template', 'Notification templates', 1),
      'create_time' => $translator->translate('Creation date'),
      'send_time' => $translator->translate('Expected send date'),
      'sent_time' => $translator->translate('Send date'),
      'sent_try' => $translator->translate('Number of tries of sent'),
      'sender' => $translator->translate('Sender email'),
      'sendername' => $translator->translate('Sender name'),
      'recipient' => $translator->translate('Recipient email'),
      'recipientname' => $translator->translate('Recipient name'),
      'replyto' => $translator->translate('Reply-to email'),
      'replytoname' => $translator->translate('Reply-to name'),
      'headers' => $translator->translate('Additional headers'),
      'messageid' => $translator->translate('Message ID'),
      'body_html' => $translator->translate('Email HTML body'),
      'body_text' => $translator->translate('Email text body'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', readonly: true));
    $defColl->add(new Def(20, $t['itemtype'], 'input', 'itemtype', readonly: true));
    $defColl->add(new Def(
      22,
      $t['notificationtemplate'],
      'dropdown_remote',
      'notificationtemplate',
      dbname: 'notificationtemplate_id',
      itemtype: '\App\Models\Notificationtemplate',
      readonly: true
    ));
    $defColl->add(new Def(16, $t['create_time'], 'datetime', 'create_time', readonly: true));
    $defColl->add(new Def(3, $t['send_time'], 'datetime', 'send_time', readonly: true));
    $defColl->add(new Def(4, $t['sent_time'], 'datetime', 'sent_time', readonly: true));
    $defColl->add(new Def(15, $t['sent_try'], 'input', 'sent_try', readonly: true));
    $defColl->add(new Def(5, $t['sender'], 'input', 'sender', readonly: true));
    $defColl->add(new Def(6, $t['sendername'], 'input', 'sendername', readonly: true));
    $defColl->add(new Def(7, $t['recipient'], 'input', 'recipient', readonly: true));
    $defColl->add(new Def(8, $t['recipientname'], 'input', 'recipientname', readonly: true));
    $defColl->add(new Def(9, $t['replyto'], 'input', 'replyto', readonly: true));
    $defColl->add(new Def(10, $t['replytoname'], 'input', 'replytoname', readonly: true));
    $defColl->add(new Def(11, $t['headers'], 'input', 'headers', readonly: true));
    $defColl->add(new Def(14, $t['messageid'], 'input', 'messageid', readonly: true));
    $defColl->add(new Def(12, $t['body_html'], 'textarea', 'body_html', readonly: true));
    $defColl->add(new Def(13, $t['body_text'], 'textarea', 'body_text', readonly: true));

    return $defColl;
    /*

    $tab[] = [
        'id'                 => 'common',
        'name'               => __('Characteristics')
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
        'id'                 => '21',
        'table'              => $this->getTable(),
        'field'              => 'items_id',
        'name'               => __('Associated item ID'),
        'massiveaction'      => false,
        'datatype'           => 'integer'
    ];

    $tab[] = [
        'id'                 => '23',
        'table'              => 'glpi_queuednotifications',
        'field'              => 'mode',
        'name'               => __('Mode'),
        'massiveaction'      => false,
        'datatype'           => 'specific',
        'searchtype'         => [
          0 => 'equals',
          1 => 'notequals'
        ]
    ];

    $tab[] = [
        'id'                 => '80',
        'table'              => 'glpi_entities',
        'field'              => 'completename',
        'name'               => Entity::getTypeName(1),
        'massiveaction'      => false,
        'datatype'           => 'dropdown'
    ];

    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      // [
      //   'title' => $translator->translate('Historical'),
      //   'icon' => 'history',
      //   'link' => '',
      // ],
    ];
  }
}
