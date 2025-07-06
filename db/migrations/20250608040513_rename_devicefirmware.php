<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RenameDevicefirmware extends AbstractMigration
{
  public function up(): void
  {
    // copy version to name field
    $this->execute('UPDATE devicefirmwares SET name = version');

    $table = $this->table('devicefirmwares');
    $table->rename('firmware')
          ->removeColumn('devicefirmwaretype_id')
          ->removeColumn('version')
          ->addColumn('model', 'string', ['null' => true])
          ->addIndex(['model'])
          ->update();

    $table = $this->table('item_devicefirmware');
    $table->rename('item_firmware')
          ->removeColumn('serial')
          ->removeColumn('otherserial')
          ->removeColumn('location_id')
          ->removeColumn('state_id')
          ->renameColumn('devicefirmware_id', 'firmware_id')
          ->update();

    $table = $this->table('computers');
    $table->addColumn('firmware_id', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $table = $this->table('networkequipments');
    $table->addColumn('firmware_id', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $table = $this->table('enclosures');
    $table->addColumn('firmware_id', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $table = $this->table('storages');
    $table->addColumn('firmware_id', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $table = $this->table('deviceprocessors');
    $table->addColumn('firmware_id', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $table = $this->table('printers');
    $table->addColumn('firmware_id', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $table = $this->table('peripherals');
    $table->addColumn('firmware_id', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $table = $this->table('phones');
    $table->addColumn('firmware_id', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $firmwares = [];

    $stmt = $this->query('SELECT * FROM item_firmware');
    $rows = $stmt->fetchAll();
    foreach ($rows as $row)
    {
      $table = null;
      switch ($row['item_type']) {
        case 'App\\Models\\Computer':
          $table = 'computers';
            break;

        case 'App\\Models\\Networkequipment':
          $table = 'networkequipments';
            break;

        case 'App\\Models\\Enclosure':
          $table = 'enclosures';
            break;

        case 'App\\Models\\Storage':
          $table = 'storages';
            break;

        case 'App\\Models\\Deviceprocessor':
          $table = 'deviceprocessors';
            break;

        case 'App\\Models\\Printer':
          $table = 'printers';
            break;

        case 'App\\Models\\Peripheral':
          $table = 'peripherals';
            break;

        case 'App\\Models\\Phone':
          $table = 'phones';
            break;
      }
      if (!is_null($table))
      {
        $firmwareId = 0;
        $item = $this->fetchRow('select * from ' . $table . ' where id = ' . $row['item_id']);
        if ($item === false)
        {
          continue;
        }

        // check if firmware has manufacturer_id and model
        $firmware = $this->fetchRow('SELECT * FROM firmware where id = ' . $row['firmware_id']);

        if ($firmware === false || is_null($firmware['model']))
        {
          $this->execute(
            'UPDATE firmware SET manufacturer_id = ?, model = ? WHERE id = ?',
            [$item['manufacturer_id'], $row['item_type'], $row['firmware_id']]
          );
          $firmwares[$row['firmware_id']] = $row['item_type'];
          $firmwareId = $row['firmware_id'];
        } elseif (isset($firmwares[$row['firmware_id']]) && $firmwares[$row['firmware_id']] == $row['item_type']) {
          $firmwareId = $row['firmware_id'];
        } else {
          // Create a new firmware
          $data = $firmware;
          $data['manufacturer_id'] = $item['manufacturer_id'];
          $data['model'] = $row['item_type'];

          $firmwareModel = $this->table('firmware');
          $firmwareModel->insert($data)->saveData();
          // get ID inserted
          $firmwareId = $this->getAdapter()->getConnection()->lastInsertId();
        }
        // update firmware_id in item table
        $this->execute(
          'UPDATE ' . $table . ' SET firmware_id = ? WHERE id = ?',
          [$firmwareId, $row['item_id']]
        );
      }
    }

    $this->execute('DROP TABLE item_firmware');

    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Firmware", "App\Models\Devicefirmware"]
    );

    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Firmware", "App\Models\Devicefirmware"]
    );

    $this->execute(
      'DELETE FROM displaypreferences WHERE num=? AND itemtype=?',
      [14, 'App\Models\Firmware']
    );
  }

  public function down(): void
  {
    $table = $this->table('firmware');
    $table->rename('devicefirmwares')
          ->addColumn('devicefirmwaretype_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('version', 'string', ['null' => true])
          ->removeColumn('model')
          ->removeIndex(['model'])
          ->update();

    $this->execute('UPDATE devicefirmwares SET version = name');

    $table = $this->table('item_devicefirmware');
    $table->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addColumn('entity_id', 'integer', ['null' => false, 'default' => 1])
          ->addColumn('is_recursive', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('item_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('item_type', 'string', ['null' => true])
          ->addColumn('devicefirmware_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('is_dynamic', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('serial', 'string', ['null' => true])
          ->addColumn('otherserial', 'string', ['null' => true])
          ->addColumn('location_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('state_id', 'integer', ['null' => false, 'default' => '0'])
          ->addIndex(['item_id'])
          ->addIndex(['devicefirmware_id'])
          ->addIndex(['is_dynamic'])
          ->addIndex(['entity_id'])
          ->addIndex(['is_recursive'])
          ->addIndex(['serial'])
          ->addIndex(['item_type', 'item_id'])
          ->addIndex(['otherserial'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();

    $table = $this->table('computers');
    $table->removeColumn('firmware_id')
          ->update();

    $table = $this->table('networkequipments');
    $table->removeColumn('firmware_id')
          ->update();

    $table = $this->table('enclosures');
    $table->removeColumn('firmware_id')
          ->update();

    $table = $this->table('storages');
    $table->removeColumn('firmware_id')
          ->update();

    $table = $this->table('deviceprocessors');
    $table->removeColumn('firmware_id')
          ->update();

    $table = $this->table('printers');
    $table->removeColumn('firmware_id')
          ->update();

    $table = $this->table('peripherals');
    $table->removeColumn('firmware_id')
          ->update();

    $table = $this->table('phones');
    $table->removeColumn('firmware_id')
          ->update();

    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Devicefirmware", "App\Models\Firmware"]
    );

    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Devicefirmware", "App\Models\Firmware"]
    );
  }
}
