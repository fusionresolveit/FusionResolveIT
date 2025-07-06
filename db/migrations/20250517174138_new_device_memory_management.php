<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class NewDeviceMemoryManagement extends AbstractMigration
{
  public function up(): void
  {
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
          ->addIndex(['item_id', 'item_type', 'slotnumber'], ['unique' => true])
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

    $memoryslots = $this->table('memoryslots');
    $memorymodules = $this->table('memorymodules');

    $stmt = $this->query('SELECT * FROM item_devicememory');
    $rows = $stmt->fetchAll();
    foreach ($rows as $row)
    {
      $slotnumber = 0;
      if (is_numeric($row['busID']))
      {
        $slotnumber = intval($row['busID']);
      }
      $data = [
        'item_id'     => $row['item_id'],
        'item_type'   => $row['item_type'],
        'is_dynamic'  => $row['is_dynamic'],
        'slotnumber'  => $slotnumber,
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
          $frequence = null;
          if (is_numeric($rowDevice['frequence']))
          {
            $frequence = intval($rowDevice['frequence']);
          }

          $data = [
            'size'                  => $row['size'],
            'frequence'             => $frequence,
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

    // change profilerights
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Memorymodule", "App\Models\Devicememory"]
    );

    // change displaypreferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Memorymodule", "App\Models\Devicememory"]
    );

    $this->execute('DROP TABLE item_devicememory');
    $this->execute('DROP TABLE devicememories');
  }

  public function down(): void
  {
    $table = $this->table('item_devicememory');
    $table->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addColumn('entity_id', 'integer', ['null' => false, 'default' => 1])
          ->addColumn('is_recursive', 'boolean', ['null' => false, 'default' => '0'])
          ->addColumn('item_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('item_type', 'string', ['null' => true])
          ->addColumn('devicememory_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('size', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('serial', 'string', ['null' => true])
          ->addColumn('is_dynamic', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('busID', 'string', ['null' => true])
          ->addColumn('otherserial', 'string', ['null' => true])
          ->addColumn('location_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('state_id', 'integer', ['null' => false, 'default' => '0'])
          ->addIndex(['item_id'])
          ->addIndex(['devicememory_id'])
          ->addIndex(['size'])
          ->addIndex(['is_dynamic'])
          ->addIndex(['serial'])
          ->addIndex(['entity_id'])
          ->addIndex(['is_recursive'])
          ->addIndex(['busID'])
          ->addIndex(['item_type', 'item_id'])
          ->addIndex(['otherserial'])
          ->addIndex(['location_id'])
          ->addIndex(['state_id'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();

    $table = $this->table('devicememories');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addColumn('entity_id', 'integer', ['null' => false, 'default' => 1])
          ->addColumn('is_recursive', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('frequence', 'string', ['null' => true])
          ->addColumn('manufacturer_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('size_default', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('devicememorytype_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('devicememorymodel_id', 'integer', ['null' => true])
          ->addIndex(['name'])
          ->addIndex(['manufacturer_id'])
          ->addIndex(['devicememorytype_id'])
          ->addIndex(['entity_id'])
          ->addIndex(['is_recursive'])
          ->addIndex(['devicememorymodel_id'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();

    $item_devicememory = $this->table('item_devicememory');
    $devicememories = $this->table('devicememories');

    $stmt = $this->query('SELECT * FROM memoryslots');
    $rows = $stmt->fetchAll();
    foreach ($rows as $row)
    {
      $dataItem = [
        'item_id'     => $row['item_id'],
        'item_type'   => $row['item_type'],
        'is_dynamic'  => $row['is_dynamic'],
        'busID'       => $row['slotnumber'],
        'created_at'  => $row['created_at'],
        'updated_at'  => $row['updated_at'],
      ];

      $stmtModules = $this->query('SELECT * FROM memorymodules WHERE memoryslot_id=' . $row['id']);
      $rowsModules = $stmtModules->fetchAll();
      $id = 0;
      foreach ($rowsModules as $rowModule)
      {
        $data = [
          'size_default'          => $rowModule['size'],
          'frequence'             => $rowModule['frequence'],
          'manufacturer_id'       => $rowModule['manufacturer_id'],
          'devicememorymodel_id'  => $rowModule['devicememorymodel_id'],
          'devicememorytype_id'   => $rowModule['devicememorytype_id'],
          'created_at'            => $rowModule['created_at'],
          'updated_at'            => $rowModule['updated_at'],
          'entity_id'             => $rowModule['entity_id'],
          'is_recursive'          => $rowModule['is_recursive'],
        ];
        $dataItem['size'] = $rowModule['size'];
        $dataItem['serial'] = $rowModule['serial'];
        $dataItem['otherserial'] = $rowModule['otherserial'];
        $dataItem['state_id'] = $rowModule['state_id'];
        $dataItem['location_id'] = $rowModule['location_id'];

        $devicememories->insert($data)
                        ->saveData();
        $id = $this->getAdapter()->getConnection()->lastInsertId();

        break;
      }
      if ($id > 0)
      {
        $dataItem['devicememory_id'] = $id;
      }
      $item_devicememory->insert($dataItem)
                        ->saveData();
    }

    // change profilerights
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Devicememory", "App\Models\Memorymodule"]
    );

    // change displaypreferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Devicememory", "App\Models\Memorymodule"]
    );

    $this->execute('DROP TABLE memoryslots');
    $this->execute('DROP TABLE memorymodules');
  }
}
