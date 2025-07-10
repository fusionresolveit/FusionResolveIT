<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ProjectsMigration extends AbstractMigration
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
    $item = $this->table('projects');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_projects');
      if ($stmt === false)
      {
        throw new \Exception('Error', 500);
      }
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                    => $row['id'],
            'name'                  => $row['name'],
            'code'                  => $row['code'],
            'priority'              => $row['priority'],
            'entity_id'             => ($row['entities_id'] + 1),
            'is_recursive'          => $row['is_recursive'],
            'project_id'            => $row['projects_id'],
            'projectstate_id'       => $row['projectstates_id'],
            'projecttype_id'        => $row['projecttypes_id'],
            'date'                  => $this->fixDate($row['date']),
            'updated_at'            => $this->fixDate($row['date_mod']),
            'user_id'               => $row['users_id'],
            'group_id'              => $row['groups_id'],
            'plan_start_date'       => $this->fixDate($row['plan_start_date']),
            'plan_end_date'         => $this->fixDate($row['plan_end_date']),
            'real_start_date'       => $this->fixDate($row['real_start_date']),
            'real_end_date'         => $this->fixDate($row['real_end_date']),
            'percent_done'          => $row['percent_done'],
            'auto_percent_done'     => $row['auto_percent_done'],
            'show_on_global_gantt'  => $row['show_on_global_gantt'],
            'content'               => $row['content'],
            'comment'               => $row['comment'],
            'created_at'            => $this->fixDate($row['date_creation']),
            'projecttemplate_id'    => $row['projecttemplates_id'],
            'is_template'           => $row['is_template'],
            'template_name'         => $row['template_name'],
            'deleted_at'            => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('projects_id_seq', (SELECT MAX(id) FROM projects)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertIsDeleted(int $is_deleted): string|null
  {
    if ($is_deleted == 1)
    {
      return date('Y-m-d H:i:s', time());
    }

    return null;
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
