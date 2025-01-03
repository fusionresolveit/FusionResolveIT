<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use Phinx\Db\Adapter\MysqlAdapter;

final class ItilrightsMigration extends AbstractMigration
{
  public function up()
  {
    $table = $this->table('profilerights');
    $table->addColumn('readmyitems', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('readmygroupitems', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('readprivateitems', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('canassign', 'boolean', ['null' => false, 'default' => false])
          ->update();

    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // recompute ticket rights from old DB
      $oldRights = [
        'readmyitems'       => 1,
        'read'              => 1024,
        'readmygroupitems'  => 2048,
        'canassign'         => 8192,
      ];

      $stmt = $this->query('SELECT * FROM profilerights WHERE model = ?', ['App\Models\Ticket']);
      $items = $stmt->fetchAll();
      foreach ($items as $item)
      {
        if (intval($item['rights']) & $oldRights['readmyitems'])
        {
          $this->execute('UPDATE profilerights SET readmyitems = ? WHERE id = ?', [true, $item['id']]);
        }
        if (intval($item['rights']) & $oldRights['read'])
        {
          $this->execute('UPDATE profilerights SET `read` = ? WHERE id = ?', [true, $item['id']]);
        } else {
          $this->execute('UPDATE profilerights SET `read` = ? WHERE id = ?', [false, $item['id']]);
        }
        if (intval($item['rights']) & $oldRights['readmygroupitems'])
        {
          $this->execute('UPDATE profilerights SET readmygroupitems = ? WHERE id = ?', [true, $item['id']]);
        }
        if (intval($item['rights']) & $oldRights['canassign'])
        {
          $this->execute('UPDATE profilerights SET canassign = ? WHERE id = ?', [true, $item['id']]);
        }
      }
    }
  }

  public function down()
  {
    $table = $this->table('profilerights');
    $table->removeColumn('readmyitems')
          ->removeColumn('readmygroupitems')
          ->removeColumn('readprivateitems')
          ->removeColumn('canassign')
          ->update();
  }
}
