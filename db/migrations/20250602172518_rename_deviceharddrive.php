<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RenameDeviceharddrive extends AbstractMigration
{
  public function up(): void
  {
    $table = $this->table('deviceharddrives');
    $table->rename('storages')
          ->addColumn('serial', 'string', ['null' => true])
          ->addColumn('otherserial', 'string', ['null' => true])
          ->addColumn('state_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('location_id', 'integer', ['null' => false, 'default' => 0])
          ->renameColumn('capacity_default', 'size')
          ->addColumn('type', 'integer', ['null' => false, 'default' => 0])
          ->renameColumn('deviceharddrivemodel_id', 'storagemodel_id')
          ->update();

    $stmt = $this->query('SELECT * FROM item_deviceharddrive');
    $rows = $stmt->fetchAll();
    foreach ($rows as $row)
    {
      $this->execute(
        'UPDATE storages SET size = ?, serial = ?, otherserial = ?, state_id = ?, location_id = ? WHERE id = ?',
        [
          $row['capacity'],
          $row['serial'],
          $row['otherserial'],
          $row['state_id'],
          $row['location_id'],
          $row['deviceharddrive_id']
        ]
      );
    }

    // modify item_deviceharddrive structure
    $table = $this->table('item_deviceharddrive');
    $table->rename('item_storage')
          ->removeColumn('serial')
          ->removeColumn('otherserial')
          ->removeColumn('state_id')
          ->removeColumn('location_id')
          ->removeColumn('capacity')
          ->renameColumn('deviceharddrive_id', 'storage_id')
          ->update();


    $table = $this->table('deviceharddrivemodels');
    $table->rename('storagemodels')
          ->update();

    // change profilerights
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Storagemodel", "App\Models\Deviceharddrivemodel"]
    );
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Storage", "App\Models\Deviceharddrive"]
    );

    // change displaypreferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Storagemodel", "App\Models\Deviceharddrivemodel"]
    );
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Storage", "App\Models\Deviceharddrive"]
    );
  }

  public function down(): void
  {
    $table = $this->table('storages');
    $table->rename('deviceharddrives')
          ->removeColumn('serial')
          ->removeColumn('otherserial')
          ->removeColumn('state_id')
          ->removeColumn('location_id')
          ->renameColumn('size', 'capacity_default')
          ->removeColumn('type')
          ->renameColumn('storagemodel_id', 'deviceharddrivemodel_id')
          ->update();

    $table = $this->table('item_storage');
    $table->rename('item_deviceharddrive')
          ->addColumn('serial', 'string', ['null' => true])
          ->addColumn('otherserial', 'string', ['null' => true])
          ->addColumn('state_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('location_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('capacity', 'integer', ['null' => false, 'default' => '0'])
          ->renameColumn('storage_id', 'deviceharddrive_id')
          ->update();

    $table = $this->table('storagemodels');
    $table->rename('deviceharddrivemodels')
          ->update();

    // change profilerights
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Deviceharddrivemodel", "App\Models\Storagemodel"]
    );
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Deviceharddrive", "App\Models\Storage"]
    );

    // change displaypreferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Deviceharddrivemodel", "App\Models\Storagemodel"]
    );
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Deviceharddrive", "App\Models\Storage"]
    );
  }
}
