<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class EventsMigration extends AbstractMigration
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

      $chunkSize = 100000;
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $chunkSize = 9200;
      }
    } else {
      return;
    }
    $item = $this->table('events');

    if ($this->isMigratingUp())
    {
      $query = $pdo->query('SELECT count(*) FROM glpi_events');
      if ($query === false)
      {
        throw new \Exception('Error', 500);
      }

      $nbRows = $query->fetchColumn();
      if ($nbRows === false || is_null($nbRows))
      {
        throw new \Exception('Error', 500);
      }
      $nbLoops = ceil(intval($nbRows) / $chunkSize);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_events ORDER BY id LIMIT ' . $chunkSize . ' OFFSET ' .
          ($i * $chunkSize));

        if ($stmt === false)
        {
          throw new \Exception('Error', 500);
        }
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'id'      => $row['id'],
            'item_id' => $row['items_id'],
            'type'    => $row['type'],
            'date'    => Toolbox::fixDate($row['date']),
            'service' => $row['service'],
            'level'   => $row['level'],
            'message' => $row['message'],
          ];
        }
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('events_id_seq', (SELECT MAX(id) FROM events)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
