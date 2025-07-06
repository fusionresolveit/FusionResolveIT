<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RemoveDevicefirmwaremodel extends AbstractMigration
{
  public function up(): void
  {
    $table = $this->table('firmware');
    $table->removeColumn('devicefirmwaremodel_id')
          ->update();

    $this->execute('DROP TABLE devicefirmwaremodels');

    // remove in profilerights
    $this->execute(
      'DELETE FROM profilerights WHERE model = ?',
      ["App\Models\Devicefirmwaremodel"]
    );

    // remove displaypreferences
    $this->execute(
      'DELETE FROM displaypreferences WHERE itemtype = ?',
      ["App\Models\Devicefirmwaremodel"]
    );

    $this->execute(
      'DELETE FROM displaypreferences WHERE num=? AND itemtype=?',
      [12, 'App\Models\Firmware']
    );
  }

  public function down(): void
  {
    $table = $this->table('firmware');
    $table->addColumn('devicefirmwaremodel_id', 'integer', ['null' => true])
          ->update();

    $table = $this->table('devicefirmwaremodels');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addColumn('product_number', 'string', ['null' => true])
          ->addIndex(['name'])
          ->addIndex(['product_number'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();
  }
}
