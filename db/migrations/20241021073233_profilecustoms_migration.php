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
      $items = $this->fetchAll('SELECT * FROM profilerights');
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
          $this->execute('UPDATE profilerights SET model = ? WHERE id = ?', [$models[$item['model']], $item['id']]);
        }
        // update 'rights' to new system
        if (intval($item['rights']) & $oldRights['read'])
        {
          $this->execute('UPDATE profilerights SET `read` = ? WHERE id = ?', [true, $item['id']]);
        }
        if (intval($item['rights']) & $oldRights['update'])
        {
          $this->execute('UPDATE profilerights SET `update` = ? WHERE id = ?', [true, $item['id']]);
        }

        if (intval($item['rights']) & $oldRights['create'])
        {
          $this->execute('UPDATE profilerights SET `create` = ? WHERE id = ?', [true, $item['id']]);
        }
        if (intval($item['rights']) & $oldRights['delete'])
        {
          $this->execute('UPDATE profilerights SET softdelete = ? WHERE id = ?', [true, $item['id']]);
        }
        if (intval($item['rights']) & $oldRights['purge'])
        {
          $this->execute('UPDATE profilerights SET `delete` = ? WHERE id = ?', [true, $item['id']]);
        }
        if (intval($item['rights']) & $oldRights['allstandardright'])
        {
          $this->execute('UPDATE profilerights SET `read` = ? WHERE id = ?', [true, $item['id']]);
          $this->execute('UPDATE profilerights SET `create` = ? WHERE id = ?', [true, $item['id']]);
          $this->execute('UPDATE profilerights SET `update` = ? WHERE id = ?', [true, $item['id']]);
          $this->execute('UPDATE profilerights SET softdelete = ? WHERE id = ?', [true, $item['id']]);
          $this->execute('UPDATE profilerights SET `delete` = ? WHERE id = ?', [true, $item['id']]);
        }
      }
    } else {
    }
  }
}
