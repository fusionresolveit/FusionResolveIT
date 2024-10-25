<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ProfilecustomsMigration extends AbstractMigration
{
  public function change(): void
  {
    $oldRights = [
      'read'              => 1,
      'update'            => 2,
      'create'            => 4,
      'delete'            => 8,
      'purge'             => 16,
      'allstandardright'  => 31,
      'readnote'          => 32,
      'updatenote'        => 64,
      'unlock'            => 128,
    ];

    $table = $this->table('profilerights');
    $table->renameColumn('name', 'model')
          ->addColumn('read', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('create', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('update', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('softdelete', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('delete', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('custom', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->update();

    $table = $this->table('profilerightcustoms');
    $table->addColumn('profileright_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('definitionfield_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('read', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('write', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->create();

    if ($this->isMigratingUp())
    {
      // Update model
      $builderSelect = $this->getQueryBuilder('select');
      $statement = $builderSelect->select('*')->from('profilerights')->execute();
      $items = $statement->fetchAll('assoc');
      $models = [
        'computer'                => 'App\\Models\\Computer',
        'budget'                  => 'App\\Models\\Budget',
        'cluster'                 => 'App\\Models\\Cluster',
        'change'                  => 'App\\Models\\Change',
        'plugin_formcreator_form' => 'App\\Models\\Forms\\Form',
        'printer'                 => 'App\\Models\\Printer',
        'problem'                 => 'App\\Models\\Problem',
        'software'                => 'App\\Models\\Software',
        'ticket'                  => 'App\\Models\\Ticket',
        'user'                    => 'App\\Models\\User',
        'itilcategory'            => 'App\\Models\\Category',
        'document'                => 'App\\Models\\Document',
        'entity'                  => 'App\\Models\\Entity',
        'contract'                => 'App\\Models\\Contract',
        'profile'                 => 'App\\Models\\Profile',
        'rule_ticket'             => 'App\\Models\\Rules\\Ticket',
        'project'                 => 'App\\Models\\Project',
        'peripheral'              => 'App\\Models\\Peripheral',
        'phone'                   => 'App\\Models\\Phone',
        'line'                    => 'App\\Models\\Line',
        'datacenter'              => 'App\\Models\\Datacenter',
        'domain'                  => 'App\\Models\\Domain',
        'license'                 => 'App\\Models\\Softwarelicense',
        'contact_enterprise'      => 'App\\Models\\Contact',
        'certificate'             => 'App\\Models\\Certificate',
        'cartridge'               => 'App\\Models\\Cartridgeitem',
      ];
      foreach ($items as $item)
      {
        if (isset($models[$item['model']]))
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('model', $models[$item['model']])
            ->where(['id' => $item['id']])
            ->execute();
        }
        // update 'rights' to new system
        if (intval($item['rights']) & $oldRights['read'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('read', true)
            ->where(['id' => $item['id']])
            ->execute();
        }
        if (intval($item['rights']) & $oldRights['update'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('update', true)
            ->where(['id' => $item['id']])
            ->execute();
        }

        if (intval($item['rights']) & $oldRights['create'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('create', true)
            ->where(['id' => $item['id']])
            ->execute();
        }
        if (intval($item['rights']) & $oldRights['delete'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('softdelete', true)
            ->where(['id' => $item['id']])
            ->execute();
        }
        if (intval($item['rights']) & $oldRights['purge'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('delete', true)
            ->where(['id' => $item['id']])
            ->execute();
        }
        if (intval($item['rights']) & $oldRights['allstandardright'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('read', true)
            ->set('create', true)
            ->set('update', true)
            ->set('softdelete', true)
            ->set('delete', true)
            ->where(['id' => $item['id']])
            ->execute();
        }
      }
    } else {
    }
  }
}
