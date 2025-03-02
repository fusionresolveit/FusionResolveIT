<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ImpactcontextsMigration extends AbstractMigration
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
    $item = $this->table('impactcontexts');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_impactcontexts');
      if ($stmt === false)
      {
        throw new \Exception('Error', 500);
      }
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                        => $row['id'],
            'positions'                 => $row['positions'],
            'zoom'                      => $row['zoom'],
            'pan_x'                     => $row['pan_x'],
            'pan_y'                     => $row['pan_y'],
            'impact_color'              => $row['impact_color'],
            'depends_color'             => $row['depends_color'],
            'impact_and_depends_color'  => $row['impact_and_depends_color'],
            'show_depends'              => $row['show_depends'],
            'show_impact'               => $row['show_impact'],
            'max_depth'                 => $row['max_depth'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('impactcontexts_id_seq', (SELECT MAX(id) FROM impactcontexts)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
