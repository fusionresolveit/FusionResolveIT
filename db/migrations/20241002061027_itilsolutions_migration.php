<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ItilsolutionsMigration extends AbstractMigration
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
    $item = $this->table('solutions');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_itilsolutions');
      if ($stmt === false)
      {
        throw new \Exception('Error', 500);
      }
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'item_type'           => self::convertItemtype($row['itemtype']),
            'item_id'             => $row['items_id'],
            'solutiontype_id'     => $row['solutiontypes_id'],
            'solutiontype_name'   => $row['solutiontype_name'],
            'content'             => Toolbox::convertHtmlToMarkdown($row['content']),
            'created_at'          => $this->fixDate($row['date_creation']),
            'updated_at'          => $this->fixDate($row['date_mod']),
            'date_approval'       => $this->fixDate($row['date_approval']),
            'user_id'             => $row['users_id'],
            'user_name'           => $row['user_name'],
            'user_id_editor'      => $row['users_id_editor'],
            'user_id_approval'    => $row['users_id_approval'],
            'user_name_approval'  => $row['user_name_approval'],
            'status'              => $row['status'],
            'followup_id'         => $row['itilfollowups_id'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('solutions_id_seq', (SELECT MAX(id) FROM solutions)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertItemtype(string $itemtype): string
  {
    $new_itemtype = '';

    if ($itemtype != null)
    {
      $new_itemtype = $itemtype;
      $new_itemtype = ucfirst(strtolower($new_itemtype));
      if ($new_itemtype == 'Item_devicesimcard')
      {
        $new_itemtype = 'ItemDevicesimcard';
      }
      $new_itemtype = 'App\\Models\\' . $new_itemtype;
    }

    return $new_itemtype;
  }

  /**
   * @param string|null $value
   */
  private function fixDate($value): string|null
  {
    if (is_null($value))
    {
      return $value;
    }
    return Toolbox::fixDate($value);
  }
}
