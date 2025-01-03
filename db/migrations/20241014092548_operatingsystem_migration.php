<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class OperatingsystemMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('item_operatingsystem');
    $table->addColumn('installationdate', 'timestamp', ['null' => true])
          ->addColumn('winowner', 'string', ['null' => true])
          ->addColumn('wincompany', 'string', ['null' => true])
          ->addColumn('oscomment', 'string', ['null' => true])
          ->addColumn('hostid', 'string', ['null' => true])
          ->update();

    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // Migration of database

      $config = Config::fromPhp('phinx.php');
      $environment = new Environment('old', $config->getEnvironment('old'));
      $pdo = $environment->getAdapter()->getConnection();
    } else {
      return;
    }
    $item = $this->table('item_operatingsystem');

    if ($this->isMigratingUp())
    {
      if ($this->hasTable('glpi_plugin_fusioninventory_inventorycomputercomputers'))
      {
        $nbRows = $pdo->query(
          'SELECT count(*) FROM glpi_plugin_fusioninventory_inventorycomputercomputers'
        )->fetchColumn();
        $nbLoops = ceil($nbRows / 5000);

        for ($i = 0; $i < $nbLoops; $i++)
        {
          $stmt = $pdo->query(
            'SELECT * FROM glpi_plugin_fusioninventory_inventorycomputercomputers ORDER BY id LIMIT 5000 OFFSET ' .
            ($i * 5000)
          );

          $rows = $stmt->fetchAll();
          $data = [];
          foreach ($rows as $row)
          {
            $this->execute(
              'UPDATE item_operatingsystem SET installationdate = ? WHERE item_type = ? AND item_id = ?',
              [
                Toolbox::fixDate($row['operatingsystem_installationdate']),
                'App\\Models\\Computer',
                $row['computers_id']
              ]
            );
            $this->execute(
              'UPDATE item_operatingsystem SET winowner = ? WHERE item_type = ? AND item_id = ?',
              [$row['winowner'], 'App\\Models\\Computer', $row['computers_id']]
            );
            $this->execute(
              'UPDATE item_operatingsystem SET wincompany = ? WHERE item_type = ? AND item_id = ?',
              [$row['wincompany'], 'App\\Models\\Computer', $row['computers_id']]
            );
            $this->execute(
              'UPDATE item_operatingsystem SET oscomment = ? WHERE item_type = ? AND item_id = ?',
              [$row['oscomment'], 'App\\Models\\Computer', $row['computers_id']]
            );
            $this->execute(
              'UPDATE item_operatingsystem SET hostid = ? WHERE item_type = ? AND item_id = ?',
              [$row['hostid'], 'App\\Models\\Computer', $row['computers_id']]
            );
          }
        }
      }
    }
  }
}
