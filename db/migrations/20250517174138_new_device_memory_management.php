<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;

final class NewDeviceMemoryManagement extends AbstractMigration
{
  public function change(): void
  {

    // create empty devicememories to be able attach to computer
    // add is_empty in devicememories
    // move size and serial from item_devicememory to devicememories
    // [OK] remove size_default in devicememories
    // move location_id and state_id form item_devicememory to devicememories
    // move otherserial from item_devicememory to devicememories
    // remove entity_id and is_recursive from item_devicememory

    //  => memory module into memory slot
    // create new tables to replace devicememories and item_devicememory:
    //   * memoryslots
    //   * memorymodules



    $table = $this->table('memoryslots');
    $table->addColumn('item_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('item_type', 'string', ['null' => true])
          ->addColumn('is_dynamic', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('slotnumber', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addIndex(['item_type', 'item_id'])
          ->addIndex(['is_dynamic'])
          ->addIndex(['slotnumber'])
          ->create();

    $table = $this->table('memorymodules');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('entity_id', 'integer', ['null' => false, 'default' => 1])
          ->addColumn('is_recursive', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('size', 'integer', ['null' => true])
          ->addColumn('frequence', 'integer', ['null' => true])
          ->addColumn('manufacturer_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('devicememorymodel_id', 'integer', ['null' => true])
          ->addColumn('devicememorytype_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('serial', 'string', ['null' => true])
          ->addColumn('otherserial', 'string', ['null' => true])
          ->addColumn('state_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('location_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('memoryslot_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->create();
  

      // root@localhost [fusionresolveit]> select * from devicememories limit 1 \G
      // *************************** 1. row ***************************
      //                   id: 1
      //                 name: DDR3
      //              comment: NULL
      //           created_at: 2025-04-14 18:00:06
      //           updated_at: 2025-04-14 18:00:06
      //           deleted_at: NULL
      //            entity_id: 1
      //         is_recursive: 0
      //            frequence: 1600
      //      manufacturer_id: 0
      //         size_default: 0
      //  devicememorytype_id: 1
      // devicememorymodel_id: 1
      // 1 row in set (0.000 sec)
       
      // root@localhost [fusionresolveit]> select * from item_devicememory limit 1 \G
      // *************************** 1. row ***************************
      //              id: 41
      //      created_at: NULL
      //      updated_at: NULL
      //      deleted_at: NULL
      //       entity_id: 1
      //    is_recursive: 0
      //         item_id: 3
      //       item_type: App\Models\Computer
      // devicememory_id: 38
      //            size: 0
      //          serial: NULL
      //      is_dynamic: 1
      //           busID: 2
      //     otherserial: NULL
      //     location_id: 0
      //        state_id: 0
      // 1 row in set (0.000 sec)


// MOVE data
// recreate devicememories and attach to the item_devicememory

    $memoryslots = $this->table('memoryslots');
    $memorymodules = $this->table('memorymodules');

    $stmt = $this->query('SELECT * FROM item_devicememory');
    $rows = $stmt->fetchAll();
    foreach ($rows as $row)
    {
      $data = [
        'item_id'     => $row['item_id'],
        'item_type'   => $row['item_type'],
        'is_dynamic'  => $row['is_dynamic'],
        'slotnumber'  => $row['busID'],
        'created_at'  => $row['created_at'],
        'updated_at'  => $row['updated_at'],
      ];
      $memoryslots->insert($data)
                  ->saveData();
      $id = $this->getAdapter()->getConnection()->lastInsertId();

      if ($row['size'] > 0)
      {
        $stmtDevice = $this->query('SELECT * FROM devicememories WHERE id=' . $row['devicememory_id']);
        $rowsDevice = $stmtDevice->fetchAll();
        foreach ($rowsDevice as $rowDevice)
        {
          $data = [
            'size'                  => $row['size'],
            'frequence'             => $rowDevice['frequence'],
            'manufacturer_id'       => $rowDevice['manufacturer_id'],
            'devicememorymodel_id'  => $rowDevice['devicememorymodel_id'],
            'devicememorytype_id'   => $rowDevice['devicememorytype_id'],
            'serial'                => $row['serial'],
            'otherserial'           => $row['otherserial'],
            'state_id'              => $row['state_id'],
            'location_id'           => $row['location_id'],
            'memoryslot_id'         => $id,
            'created_at'            => $rowDevice['created_at'],
            'updated_at'            => $rowDevice['updated_at'],
            'entity_id'             => $rowDevice['entity_id'],
            'is_recursive'          => $rowDevice['is_recursive'],
          ];
          $memorymodules->insert($data)
                        ->saveData();
          // may not have 2 memory modules in a slot
          break;
        }
      }
    }
 
// TODO remove fields in item_devicememory

    // change profilerights
    $this->execute('UPDATE profilerights SET model = ? WHERE model = ?', ["App\Models\Memorymodule", "App\Models\Devicememory"]);

    // change displaypreferences
    $this->execute('UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?', ["App\Models\Memorymodule", "App\Models\Devicememory"]);

  }
}
