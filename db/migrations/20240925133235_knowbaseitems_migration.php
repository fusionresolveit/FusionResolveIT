<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class KnowbaseitemsMigration extends AbstractMigration
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
    $item = $this->table('knowbaseitems');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_knowbaseitems');
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
            'knowbaseitemcategory_id' => $row['knowbaseitemcategories_id'],
            'name'                    => $row['name'],
            'answer'                  => $row['answer'],
            'is_faq'                  => $row['is_faq'],
            'user_id'                 => $row['users_id'],
            'view'                    => $row['view'],
            'date'                    => $this->fixDate($row['date']),
            'updated_at'              => $this->fixDate($row['date_mod']),
            'begin_date'              => $this->fixDate($row['begin_date']),
            'end_date'                => $this->fixDate($row['end_date']),
            'created_at'              => $this->fixDate($row['date_mod']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('knowbaseitems_id_seq', (SELECT MAX(id) FROM knowbaseitems)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
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
