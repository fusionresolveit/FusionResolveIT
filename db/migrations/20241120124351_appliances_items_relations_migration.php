<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use Phinx\Db\Adapter\MysqlAdapter;

final class AppliancesItemsRelationsMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('appliance_item_relation');
    $table->addColumn('appliance_item_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('item_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('item_type', 'string', ['null' => false])
          ->addIndex(['appliance_item_id', 'item_id', 'item_type'], ['unique' => true])
          ->create();

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
    $item = $this->table('appliance_item_relation');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_appliances_items_relations');
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
            'appliance_item_id'   => $row['appliances_items_id'],
            'item_id'             => $row['items_id'],
            'item_type'           => self::convertItemtype($row['itemtype']),
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
}
