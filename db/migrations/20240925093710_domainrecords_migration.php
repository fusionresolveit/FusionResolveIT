<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class DomainrecordsMigration extends AbstractMigration
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
    $item = $this->table('domainrecords');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_domainrecords');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'name'                => $row['name'],
            'comment'             => $row['comment'],
            'entity_id'           => $row['entities_id'],
            'is_recursive'        => $row['is_recursive'],
            'data'                => $row['data'],
            'domain_id'           => $row['domains_id'],
            'domainrecordtype_id' => $row['domainrecordtypes_id'],
            'ttl'                 => $row['ttl'],
            'user_id_tech'        => $row['users_id_tech'],
            'group_id_tech'       => $row['groups_id_tech'],
            'is_deleted'          => $row['is_deleted'],
            'updated_at'          => $row['date_mod'],
            'created_at'          => $row['date_creation'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}