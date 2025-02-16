<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class CartridgesMigration extends AbstractMigration
{
  public function change(): void
  {
    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // Migration of database

      $config = Config::fromPhp('phinx.php');

      $oldEnv = $config->getEnvironment('old');
      if (is_null($oldEnv))
      {
        throw new \Exception('Error', 500);
      }

      $environment = new Environment('old', $oldEnv);
      $pdo = $environment->getAdapter()->getConnection();
    } else {
      return;
    }
    $item = $this->table('cartridges');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_cartridges');
      if ($stmt === false)
      {
        throw new \Exception('Error', 500);
      }
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'entity_id'         => ($row['entities_id'] + 1),
            'cartridgeitem_id'  => $row['cartridgeitems_id'],
            'printer_id'        => $row['printers_id'],
            'date_in'           => $row['date_in'],
            'date_use'          => $row['date_use'],
            'date_out'          => $row['date_out'],
            'pages'             => $row['pages'],
            'updated_at'        => $row['date_mod'],
            'created_at'        => $row['date_creation'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('cartridges_id_seq', (SELECT MAX(id) FROM cartridges)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
