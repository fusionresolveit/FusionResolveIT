<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class KnowbaseitemsItemsMigration extends AbstractMigration
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
    $item = $this->table('knowbaseitem_item');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_knowbaseitems_items');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        if ($row['itemtype'] == 'Entity')
        {
          $row['items_id'] = $row['items_id'] + 1;
        }

        $data = [
          [
            'id'              => $row['id'],
            'knowbaseitem_id' => $row['knowbaseitems_id'],
            'item_type'       => self::convertItemtype($row['itemtype']),
            'item_id'         => $row['items_id'],
            'created_at'      => Toolbox::fixDate($row['date_creation']),
            'updated_at'      => Toolbox::fixDate($row['date_mod']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('knowbaseitem_item_id_seq', (SELECT MAX(id) FROM knowbaseitem_item)+1)");
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
