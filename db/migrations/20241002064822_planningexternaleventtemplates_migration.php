<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class PlanningexternaleventtemplatesMigration extends AbstractMigration
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
    $item = $this->table('planningexternaleventtemplates');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_planningexternaleventtemplates');
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
            'entity_id'                 => ($row['entities_id'] + 1),
            'name'                      => $row['name'],
            'text'                      => $row['text'],
            'comment'                   => $row['comment'],
            'duration'                  => $row['duration'],
            'before_time'               => $row['before_time'],
            'rrule'                     => $row['rrule'],
            'state'                     => $row['state'],
            'planningeventcategory_id'  => $row['planningeventcategories_id'],
            'background'                => $row['background'],
            'updated_at'                => Toolbox::fixDate($row['date_mod']),
            'created_at'                => Toolbox::fixDate($row['date_creation']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('planningexternaleventtemplates_id_seq', (SELECT MAX(id) FROM " .
          "planningexternaleventtemplates)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
