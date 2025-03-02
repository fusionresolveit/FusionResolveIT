<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class SoftwaresMigration extends AbstractMigration
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
    $item = $this->table('softwares');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_softwares');
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
            'entity_id'             => ($row['entities_id'] + 1),
            'is_recursive'          => $row['is_recursive'],
            'name'                  => $row['name'],
            'comment'               => $row['comment'],
            'location_id'           => $row['locations_id'],
            'user_id_tech'          => $row['users_id_tech'],
            'group_id_tech'         => $row['groups_id_tech'],
            'is_update'             => $row['is_update'],
            'software_id'           => $row['softwares_id'],
            'manufacturer_id'       => $row['manufacturers_id'],
            'is_template'           => $row['is_template'],
            'template_name'         => $row['template_name'],
            'updated_at'            => Toolbox::fixDate($row['date_mod']),
            'user_id'               => $row['users_id'],
            'group_id'              => $row['groups_id'],
            'ticket_tco'            => $row['ticket_tco'],
            'is_helpdesk_visible'   => $row['is_helpdesk_visible'],
            'softwarecategory_id'   => $row['softwarecategories_id'],
            'is_valid'              => $row['is_valid'],
            'created_at'            => Toolbox::fixDate($row['date_creation']),
            'deleted_at'            => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('softwares_id_seq', (SELECT MAX(id) FROM softwares)+1)");
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
}
