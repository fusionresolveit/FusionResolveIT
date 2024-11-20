<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class LogsMigration extends AbstractMigration
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

      $chunkSize = 30000;
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $chunkSize = 5900;
      }
    } else {
      return;
    }
    $item = $this->table('logs');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_logs')->fetchColumn();
      $nbLoops = ceil($nbRows / $chunkSize);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_logs ORDER BY id LIMIT ' . $chunkSize . ' OFFSET ' .
          ($i * $chunkSize));
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $item_id = $row['items_id'];
          if ($row['itemtype'] == 'Entity')
          {
            $item_id += 1;
          }

          $data[] = [
            'id'                => $row['id'],
            'item_type'         => self::convertItemtype($row['itemtype']),
            'item_id'           => $item_id,
            'itemtype_link'     => $row['itemtype_link'],
            'linked_action'     => $row['linked_action'],
            'user_name'         => $row['user_name'],
            'updated_at'        => Toolbox::fixDate($row['date_mod']),
            'id_search_option'  => $row['id_search_option'],
            'old_value'         => $row['old_value'],
            'new_value'         => $row['new_value'],
            'created_at'        => Toolbox::fixDate($row['date_mod']),
          ];
        }
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('logs_id_seq', (SELECT MAX(id) FROM logs)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertItemtype($itemtype) {
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
