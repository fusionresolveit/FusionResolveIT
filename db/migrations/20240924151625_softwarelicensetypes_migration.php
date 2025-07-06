<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class SoftwarelicensetypesMigration extends AbstractMigration
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
    $item = $this->table('softwarelicensetypes');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_softwarelicensetypes');
      if ($stmt === false)
      {
        throw new \Exception('Error', 500);
      }
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $updated_at = null;
        $created_at = null;

        if (!is_null($row['date_mod']))
        {
          $updated_at = Toolbox::fixDate($row['date_mod']);
        }
        if (!is_null($row['date_creation']))
        {
          $created_at = Toolbox::fixDate($row['date_creation']);
        }

        $data = [
          [
            'id'                      => $row['id'],
            'name'                    => $row['name'],
            'comment'                 => $row['comment'],
            'updated_at'              => $updated_at,
            'created_at'              => $created_at,
            'softwarelicensetype_id'  => $row['softwarelicensetypes_id'],
            'level'                   => $row['level'],
            'ancestors_cache'         => $row['ancestors_cache'],
            'sons_cache'              => $row['sons_cache'],
            'entity_id'               => ($row['entities_id'] + 1),
            'is_recursive'            => $row['is_recursive'],
            'completename'            => $row['completename'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('softwarelicensetypes_id_seq', (SELECT MAX(id) FROM softwarelicensetypes)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
