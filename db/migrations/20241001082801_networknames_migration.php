<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class NetworknamesMigration extends AbstractMigration
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
    $item = $this->table('networknames');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_networknames');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'          => $row['id'],
            'entity_id'   => ($row['entities_id'] + 1),
            'item_id'     => $row['items_id'],
            'item_type'   => self::convertItemtype($row['itemtype']),
            'name'        => $row['name'],
            'comment'     => $row['comment'],
            'fqdn_id'     => $row['fqdns_id'],
            'is_dynamic'  => $row['is_dynamic'],
            'updated_at'  => Toolbox::fixDate($row['date_mod']),
            'created_at'  => Toolbox::fixDate($row['date_creation']),
            'deleted_at'  => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('networknames_id_seq', (SELECT MAX(id) FROM networknames)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertIsDeleted($is_deleted)
  {
    if ($is_deleted == 1)
    {
      return date('Y-m-d H:i:s', time());
    }

    return null;
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
