<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ItemsSoftwareversionsMigration extends AbstractMigration
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
    $item = $this->table('item_softwareversion');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_items_softwareversions');
      if ($stmt === false)
      {
        throw new \Exception('Error', 500);
      }
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $deletedItem = false;
        $templateItem = false;
        if (isset($row['is_deleted_computer']))
        {
          $deletedItem = $row['is_deleted_computer'];
        }
        if (isset($row['is_deleted_item']))
        {
          $deletedItem = $row['is_deleted_item'];
        }
        if (isset($row['is_template_computer']))
        {
          $templateItem = $row['is_template_computer'];
        }
        if (isset($row['is_template_item']))
        {
          $templateItem = $row['is_template_item'];
        }

        $data = [
          [
            'id'                  => $row['id'],
            'item_id'             => $row['items_id'],
            'item_type'           => self::convertItemtype($row['itemtype']),
            'softwareversion_id'  => $row['softwareversions_id'],
            'is_deleted_item'     => $deletedItem,
            'is_template_item'    => $templateItem,
            'entity_id'           => ($row['entities_id'] + 1),
            'is_dynamic'          => $row['is_dynamic'],
            'date_install'        => Toolbox::fixDate($row['date_install']),
            'deleted_at'          => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('item_softwareversion_id_seq', (SELECT MAX(id) FROM item_softwareversion)+1)");
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
