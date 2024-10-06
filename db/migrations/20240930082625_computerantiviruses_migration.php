<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ComputerantivirusesMigration extends AbstractMigration
{
  public function change()
  {
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
    $item = $this->table('computerantiviruses');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_computerantiviruses');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'computer_id'       => $row['computers_id'],
            'name'              => $row['name'],
            'manufacturer_id'   => $row['manufacturers_id'],
            'antivirus_version' => $row['antivirus_version'],
            'signature_version' => $row['signature_version'],
            'is_active'         => $row['is_active'],
            'is_deleted'        => $row['is_deleted'],
            'is_uptodate'       => $row['is_uptodate'],
            'is_dynamic'        => $row['is_dynamic'],
            'date_expiration'   => $row['date_expiration'],
            'updated_at'        => $row['date_mod'],
            'created_at'        => $row['date_creation'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}