<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class SavedsearchesMigration extends AbstractMigration
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
    $item = $this->table('savedsearches');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_savedsearches');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'name'                => $row['name'],
            'type'                => $row['type'],
            'item_type'           => self::convertItemtype($row['itemtype']),
            'user_id'             => $row['users_id'],
            'is_private'          => $row['is_private'],
            'entity_id'           => ($row['entities_id'] + 1),
            'is_recursive'        => $row['is_recursive'],
            'path'                => $row['path'],
            'query'               => $row['query'],
            'last_execution_time' => $row['last_execution_time'],
            'do_count'            => $row['do_count'],
            'last_execution_date' => Toolbox::fixDate($row['last_execution_date']),
            'counter'             => $row['counter'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('savedsearches_id_seq', (SELECT MAX(id) FROM savedsearches)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertItemtype($itemtype)
  {
    $new_itemtype = '';

    if ($itemtype != null) {
      $new_itemtype = $itemtype;
      $new_itemtype = ucfirst(strtolower($new_itemtype));
      if ($new_itemtype == 'Item_devicesimcard') {
        $new_itemtype = 'ItemDevicesimcard';
      }
      $new_itemtype = 'App\\Models\\' . $new_itemtype;
    }

    return $new_itemtype;
  }
}
