<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250407220903 extends AbstractMigration
{
  /** @var array<mixed> */
  protected $tablesToUpdate = [
    'appliance_item',
    'appliance_item_relation',
    'audits',
    'certificate_item',
    'change_item',
    'computer_item',
    'consumables',
    'contract_item',
    'crontasks',
    'document_item',
    'domain_item',
    'dropdowntranslations',
    'fieldblacklists',
    'fieldunicities',
    'followups',
    'impactitems',
    'infocoms',
    'ipaddresses',
    'item_cluster',
    'item_devicebattery',
    'item_devicecase',
    'item_devicecontrol',
    'item_devicedrive',
    'item_devicefirmware',
    'item_devicegeneric',
    'item_devicegraphiccard',
    'item_deviceharddrive',
    'item_devicememory',
    'item_devicemotherboard',
    'item_devicenetworkcard',
    'item_devicepci',
    'item_devicepowersupply',
    'item_deviceprocessor',
    'item_devicesensor',
    'item_devicesimcard',
    'item_devicesoundcard',
    'item_enclosure',
    'item_kanban',
    'item_operatingsystem',
    'item_problem',
    'item_project',
    'item_rack',
    'item_softwarelicense',
    'item_softwareversion',
    'item_ticket',
    'itemdisks',
    'itil_project',
    'knowledgebasearticle_item',
    'link_itemtype',
    'logs',
    'networknames',
    'networkports',
    'notepads',
    'notifications',
    'notificationtemplates',
    'objectlocks',
    'planningrecalls',
    'projecttaskteams',
    'projectteams',
    'queuednotifications',
    'registeredids',
    'reservationitems',
    'savedsearch_user',
    'savedsearches',
    'solutions',
    'vobjects',
  ];

  public function up(): void
  {
    $knowbaseitem = $this->table('knowbaseitems');
    $knowbaseitem->rename('knowledgebasearticles')
                 ->renameColumn('answer', 'article')
                 ->renameColumn('view', 'views')
                 ->removeColumn('date')
                 ->update();

    $categories = $this->table('categories');
    $categories->removeColumn('knowbaseitemcategory_id')
               ->update();

    $table = $this->table('entity_knowbaseitem');
    $table->rename('entity_knowledgebasearticle')
          ->renameColumn('knowbaseitem_id', 'knowledgebasearticle_id')
          ->update();

    $table = $this->table('group_knowbaseitem');
    $table->rename('group_knowledgebasearticle')
          ->renameColumn('knowbaseitem_id', 'knowledgebasearticle_id')
          ->update();

    $table = $this->table('knowbaseitem_comment');
    $table->rename('knowledgebasearticle_comment')
          ->renameColumn('knowbaseitem_id', 'knowledgebasearticle_id')
          ->update();

    $table = $this->table('knowbaseitem_item');
    $table->rename('knowledgebasearticle_item')
          ->renameColumn('knowbaseitem_id', 'knowledgebasearticle_id')
          ->update();

    $table = $this->table('knowbaseitem_profile');
    $table->rename('knowledgebasearticle_profile')
          ->renameColumn('knowbaseitem_id', 'knowledgebasearticle_id')
          ->update();

    $table = $this->table('knowbaseitem_revision');
    $table->rename('knowledgebasearticle_revision')
          ->renameColumn('knowbaseitem_id', 'knowledgebasearticle_id')
          ->update();

    $table = $this->table('knowbaseitem_user');
    $table->rename('knowledgebasearticle_user')
          ->renameColumn('knowbaseitem_id', 'knowledgebasearticle_id')
          ->update();

    $table = $this->table('knowbaseitemtranslations');
    $table->rename('knowledgebasearticletranslations')
          ->renameColumn('knowbaseitem_id', 'knowledgebasearticle_id')
          ->update();

    $this->table('knowbaseitemcategories')->drop()->save();

    // update item_type of tables to replace old class name to new class name

    foreach ($this->tablesToUpdate as $table)
    {
      $this->execute(
        'UPDATE ' . $table . ' SET item_type=? WHERE item_type=?',
        ['App\Models\Knowledgebasearticle', 'App\Models\Knowbaseitem']
      );
      $this->execute('DELETE FROM ' . $table . ' WHERE item_type=?', ['App\Models\Knowbaseitemcategory']);
    }
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ['App\Models\Knowledgebasearticle', 'App\Models\Knowbaseitem']
    );
    $this->execute('DELETE FROM profilerights WHERE model=?', ['App\Models\Knowbaseitemcategory']);

    $this->execute(
      'UPDATE displaypreferences SET itemtype=? WHERE itemtype=?',
      ['App\Models\Knowledgebasearticle', 'App\Models\Knowbaseitem']
    );
    $this->execute(
      'DELETE FROM displaypreferences WHERE num=? AND itemtype=?',
      [79, 'App\Models\Knowledgebasearticle']
    );
  }

  public function down(): void
  {
    $knowbaseitem = $this->table('knowledgebasearticles');
    $knowbaseitem->rename('knowbaseitems')
                 ->renameColumn('article', 'answer')
                 ->renameColumn('views', 'view')
                 ->addColumn('date', 'datetime', ['null' => true])
                 ->update();

    $categories = $this->table('categories');
    $categories->addColumn('knowbaseitemcategory_id', 'integer', ['null' => false, 'default' => '0'])
               ->update();

    $table = $this->table('entity_knowledgebasearticle');
    $table->rename('entity_knowbaseitem')
          ->renameColumn('knowledgebasearticle_id', 'knowbaseitem_id')
          ->update();

    $table = $this->table('group_knowledgebasearticle');
    $table->rename('group_knowbaseitem')
          ->renameColumn('knowledgebasearticle_id', 'knowbaseitem_id')
          ->update();

    $table = $this->table('knowledgebasearticle_comment');
    $table->rename('knowbaseitem_comment')
          ->renameColumn('knowledgebasearticle_id', 'knowbaseitem_id')
          ->update();

    $table = $this->table('knowledgebasearticle_profile');
    $table->rename('knowbaseitem_profile')
          ->renameColumn('knowledgebasearticle_id', 'knowbaseitem_id')
          ->update();

    $table = $this->table('knowledgebasearticle_revision');
    $table->rename('knowbaseitem_revision')
          ->renameColumn('knowledgebasearticle_id', 'knowbaseitem_id')
          ->update();

    $table = $this->table('knowledgebasearticle_user');
    $table->rename('knowbaseitem_user')
          ->renameColumn('knowledgebasearticle_id', 'knowbaseitem_id')
          ->update();

    $table = $this->table('knowledgebasearticletranslations');
    $table->rename('knowbaseitemtranslations')
          ->renameColumn('knowledgebasearticle_id', 'knowbaseitem_id')
          ->update();

    $table = $this->table('knowbaseitemcategories');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('created_at', 'datetime', ['null' => true])
          ->addColumn('updated_at', 'datetime', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addColumn('entity_id', 'integer', ['null' => false, 'default' => 1])
          ->addColumn('is_recursive', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('knowbaseitemcategory_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('completename', 'text', ['null' => true])
          ->addColumn('treepath', 'string', ['null' => true])
          ->addIndex(['entity_id', 'knowbaseitemcategory_id', 'name'], ['unique' => true])
          ->addIndex(['name'])
          ->addIndex(['entity_id'])
          ->addIndex(['is_recursive'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();

    // update item_type of tables to replace old class name to new class name

    foreach ($this->tablesToUpdate as $table)
    {
      $this->execute(
        'UPDATE ' . $table . ' SET item_type=? WHERE item_type=?',
        ['App\Models\Knowbaseitem', 'App\\Models\\Knowledgebasearticle']
      );
    }
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ['App\Models\Knowbaseitem', 'App\Models\Knowledgebasearticle']
    );

    // Must done after update item_type
    $table = $this->table('knowledgebasearticle_item');
    $table->rename('knowbaseitem_item')
          ->renameColumn('knowledgebasearticle_id', 'knowbaseitem_id')
          ->update();
  }
}
