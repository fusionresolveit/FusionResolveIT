<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class NetworkportfiberchannelsMigration extends AbstractMigration
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
    $item = $this->table('networkportfiberchannels');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_networkportfiberchannels');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                        => $row['id'],
            'networkport_id'            => $row['networkports_id'],
            'item_devicenetworkcard_id' => $row['items_devicenetworkcards_id'],
            'netpoint_id'               => $row['netpoints_id'],
            'wwn'                       => $row['wwn'],
            'speed'                     => $row['speed'],
            'updated_at'                => Toolbox::fixDate($row['date_mod']),
            'created_at'                => Toolbox::fixDate($row['date_creation']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('networkportfiberchannels_id_seq', (SELECT MAX(id) FROM " .
          "networkportfiberchannels)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
