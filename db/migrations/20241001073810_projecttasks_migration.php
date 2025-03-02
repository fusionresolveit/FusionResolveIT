<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ProjectTasksMigration extends AbstractMigration
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
    $item = $this->table('projecttasks');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_projecttasks');
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
            'uuid'                    => $row['uuid'],
            'name'                    => $row['name'],
            'content'                 => $row['content'],
            'entity_id'               => ($row['entities_id'] + 1),
            'is_recursive'            => $row['is_recursive'],
            'project_id'              => $row['projects_id'],
            'projecttask_id'          => $row['projecttasks_id'],
            'date'                    => Toolbox::fixDate($row['date']),
            'updated_at'              => Toolbox::fixDate($row['date_mod']),
            'plan_start_date'         => Toolbox::fixDate($row['plan_start_date']),
            'plan_end_date'           => Toolbox::fixDate($row['plan_end_date']),
            'real_start_date'         => Toolbox::fixDate($row['real_start_date']),
            'real_end_date'           => Toolbox::fixDate($row['real_end_date']),
            'planned_duration'        => $row['planned_duration'],
            'effective_duration'      => $row['effective_duration'],
            'projectstate_id'         => $row['projectstates_id'],
            'projecttasktype_id'      => $row['projecttasktypes_id'],
            'user_id'                 => $row['users_id'],
            'percent_done'            => $row['percent_done'],
            'auto_percent_done'       => $row['auto_percent_done'],
            'is_milestone'            => $row['is_milestone'],
            'projecttasktemplate_id'  => $row['projecttasktemplates_id'],
            'is_template'             => $row['is_template'],
            'template_name'           => $row['template_name'],
            'created_at'              => Toolbox::fixDate($row['date_mod']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('projecttasks_id_seq', (SELECT MAX(id) FROM projecttasks)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
