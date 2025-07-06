<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class DevicefirmwaresMigration extends AbstractMigration
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
    $item = $this->table('devicefirmwares');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_devicefirmwares');
      if ($stmt === false)
      {
        throw new \Exception('Error', 500);
      }
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'name'                    => $row['designation'],
            'comment'                 => $row['comment'],
            'manufacturer_id'         => $row['manufacturers_id'],
            'date'                    => $this->fixDate($row['date']),
            'version'                 => $row['version'],
            'devicefirmwaretype_id'   => $row['devicefirmwaretypes_id'],
            'entity_id'               => ($row['entities_id'] + 1),
            'is_recursive'            => $row['is_recursive'],
            'devicefirmwaremodel_id'  => $row['devicefirmwaremodels_id'],
            'updated_at'              => $this->fixDate($row['date_mod']),
            'created_at'              => $this->fixDate($row['date_creation']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('devicefirmwares_id_seq', (SELECT MAX(id) FROM devicefirmwares)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  /**
   * @param string|null $value
   */
  private function fixDate($value): string|null
  {
    if (is_null($value))
    {
      return null;
    }
    return Toolbox::fixDate($value);
  }
}
