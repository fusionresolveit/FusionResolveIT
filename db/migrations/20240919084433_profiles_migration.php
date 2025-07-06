<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ProfilesMigration extends AbstractMigration
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
    $item = $this->table('profiles');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_profiles');
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
            'id'                        => $row['id'],
            'name'                      => $row['name'],
            'interface'                 => $row['interface'],
            'is_default'                => $row['is_default'],
            'helpdesk_hardware'         => $row['helpdesk_hardware'],
            'helpdesk_item_type'        => $row['helpdesk_item_type'],
            'ticket_status'             => $row['ticket_status'],
            'updated_at'                => $updated_at,
            'comment'                   => $row['comment'],
            'problem_status'            => $row['problem_status'],
            'create_ticket_on_login'    => $row['create_ticket_on_login'],
            'tickettemplate_id'         => $row['tickettemplates_id'],
            'changetemplate_id'         => $row['changetemplates_id'],
            'problemtemplate_id'        => $row['problemtemplates_id'],
            'change_status'             => $row['change_status'],
            'managed_domainrecordtypes' => $row['managed_domainrecordtypes'],
            'created_at'                => $created_at,
          ]
        ];

        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('profiles_id_seq', (SELECT MAX(id) FROM profiles)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
